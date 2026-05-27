<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\BusService as BusServiceModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling bus-related business logic.
 */
class BusService
{
    /**
     * Get all buses for admin listing.
     */
    public function getAllBuses(): Collection
    {
        return Bus::query()
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get buses for DataTables with server-side processing.
     */
    public function getBusesForDataTable(array $params): array
    {
        $query = Bus::query()->with(['services' => function ($servicesQuery) {
            $servicesQuery
                ->select('bus_services.id', 'name', 'icon', 'priority')
                ->orderBy('priority', 'desc')
                ->orderBy('name');
        }]);

        // Apply search
        if (! empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('model_name', 'like', "%{$searchValue}%");
            });
        }

        // Apply service filter
        if (! empty($params['service_filter'])) {
            $serviceFilter = (int) $params['service_filter'];
            $query->whereHas('services', fn ($q) => $q->where('bus_services.id', $serviceFilter));
        }

        // Apply seats filter
        if (! empty($params['seats_filter'])) {
            $seatsFilter = $params['seats_filter'];
            if ($seatsFilter === '1-20') {
                $query->whereBetween('seat_count', [1, 20]);
            } elseif ($seatsFilter === '21-35') {
                $query->whereBetween('seat_count', [21, 35]);
            } elseif ($seatsFilter === '36-50') {
                $query->whereBetween('seat_count', [36, 50]);
            } elseif ($seatsFilter === '51+') {
                $query->where('seat_count', '>=', 51);
            }
        }

        $totalRecords = Bus::query()->count();
        $filteredRecords = (clone $query)->count();

        $buses = $query->orderBy('priority', 'desc')
            ->skip($params['start'] ?? 0)
            ->take($params['length'] ?? 10)
            ->get();

        // Get statistics
        $stats = $this->getBusStatistics();

        return [
            'data' => $buses,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'stats' => $stats,
        ];
    }

    /**
     * Get bus statistics for dashboard.
     */
    public function getBusStatistics(): array
    {
        $totalBuses = DB::table('buses')->count();
        $totalSeats = DB::table('buses')->sum('seat_count');
        $uniqueModels = DB::table('buses')
            ->whereNotNull('model_name')
            ->where('model_name', '!=', '')
            ->distinct()
            ->count('model_name');

        return [
            'total_buses' => $totalBuses,
            'total_seats' => $totalSeats ?? 0,
            'unique_models' => $uniqueModels,
        ];
    }

    /**
     * Get bus by ID.
     */
    public function getBusById(int $id): ?object
    {
        $bus = Bus::query()
            ->with(['services' => function ($query) {
                $query
                    ->select('bus_services.id', 'name', 'icon', 'priority')
                    ->orderBy('priority', 'desc')
                    ->orderBy('name');
            }])
            ->find($id);

        if (! $bus) {
            return null;
        }

        $data = (object) $bus->toArray();
        $data->services = $bus->services->pluck('id')->values()->all();
        $data->service_details = $this->formatServiceDetails($bus->services);

        return $data;
    }

    /**
     * Create a new bus.
     */
    public function createBus(array $data): int
    {
        $serviceIds = $this->extractServiceIds($data);
        $data = $this->prepareBusData($data);

        return DB::transaction(function () use ($data, $serviceIds) {
            $bus = Bus::query()->create($data);
            $bus->services()->sync($serviceIds);

            return $bus->id;
        });
    }

    /**
     * Update a bus.
     */
    public function updateBus(int $id, array $data): bool
    {
        $serviceIds = $this->extractServiceIds($data);
        $data = $this->prepareBusData($data);

        return DB::transaction(function () use ($id, $data, $serviceIds) {
            $bus = Bus::query()->find($id);

            if (! $bus) {
                return false;
            }

            $bus->fill($data);
            $bus->updated_at = Carbon::now();
            $bus->save();
            $bus->services()->sync($serviceIds);

            return true;
        });
    }

    /**
     * Delete a bus.
     */
    public function deleteBus(int $id): bool
    {
        return Bus::query()->where('id', $id)->delete() > 0;
    }

    /**
     * Get buses for select options.
     */
    public function getBusesForSelect(): Collection
    {
        return DB::table('buses')
            ->select('id', DB::raw("CONCAT(name, ' - ', COALESCE(model_name, 'N/A')) as text"))
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all bus services.
     */
    public function getAllServices(): Collection
    {
        return BusServiceModel::query()
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();
    }

    private function extractServiceIds(array &$data): array
    {
        $serviceIds = collect($data['services'] ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique()
            ->values()
            ->all();

        unset($data['services']);

        return $serviceIds;
    }

    private function prepareBusData(array $data): array
    {

        if (array_key_exists('image_list_url', $data)) {
            $imageList = is_string($data['image_list_url']) ? json_decode($data['image_list_url'], true) : $data['image_list_url'];
            $data['image_list_url'] = is_array($imageList) ? $imageList : [];
        }

        return $data;
    }

    private function formatServiceDetails(Collection $services): array
    {
        return $services
            ->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'icon' => $service->icon,
            ])
            ->values()
            ->all();
    }
}
