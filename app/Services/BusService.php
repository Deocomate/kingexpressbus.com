<?php

namespace App\Services;

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
        return DB::table('buses')
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get buses for DataTables with server-side processing.
     */
    public function getBusesForDataTable(array $params): array
    {
        $query = DB::table('buses');

        // Apply search
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('model_name', 'like', "%{$searchValue}%");
            });
        }

        // Apply service filter
        if (!empty($params['service_filter'])) {
            $serviceFilter = $params['service_filter'];
            $query->where('services', 'like', "%\"{$serviceFilter}\"%");
        }

        // Apply seats filter
        if (!empty($params['seats_filter'])) {
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

        $totalRecords = DB::table('buses')->count();
        $filteredRecords = $query->count();

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
        $bus = DB::table('buses')->where('id', $id)->first();

        if ($bus) {
            $bus->services = json_decode($bus->services, true) ?? [];
            $bus->seat_map = json_decode($bus->seat_map, true) ?? [];
            $bus->image_list_url = json_decode($bus->image_list_url, true) ?? [];
        }

        return $bus;
    }

    /**
     * Create a new bus.
     */
    public function createBus(array $data): int
    {
        // Process services array to JSON
        $data['services'] = isset($data['services']) ? json_encode($data['services']) : json_encode([]);

        // Auto-calculate seat_count from seat_map
        if (isset($data['seat_map'])) {
            $seatMap = is_string($data['seat_map']) ? json_decode($data['seat_map'], true) : $data['seat_map'];
            $data['seat_count'] = is_array($seatMap) ? count($seatMap) : 0;
            $data['seat_map'] = is_string($data['seat_map']) ? $data['seat_map'] : json_encode($data['seat_map']);
        }

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        return DB::table('buses')->insertGetId($data);
    }

    /**
     * Update a bus.
     */
    public function updateBus(int $id, array $data): bool
    {
        // Process services array to JSON
        if (isset($data['services'])) {
            $data['services'] = is_array($data['services']) ? json_encode($data['services']) : $data['services'];
        }

        // Auto-calculate seat_count from seat_map
        if (isset($data['seat_map'])) {
            $seatMap = is_string($data['seat_map']) ? json_decode($data['seat_map'], true) : $data['seat_map'];
            $data['seat_count'] = is_array($seatMap) ? count($seatMap) : 0;
            $data['seat_map'] = is_string($data['seat_map']) ? $data['seat_map'] : json_encode($data['seat_map']);
        }

        $data['updated_at'] = Carbon::now();

        return DB::table('buses')->where('id', $id)->update($data) > 0;
    }

    /**
     * Delete a bus.
     */
    public function deleteBus(int $id): bool
    {
        return DB::table('buses')->where('id', $id)->delete() > 0;
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
        return DB::table('bus_services')
            ->orderBy('priority', 'desc')
            ->get();
    }
}
