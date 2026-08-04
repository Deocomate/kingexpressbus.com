<?php

namespace App\Support\Admin\OptionSources;

use Illuminate\Support\Facades\DB;

class Trips implements OptionSourceContract
{
    public function search(string $query): array
    {
        $routeId = (int) request()->input('route_id', 0);

        $q = DB::table('trips')
            ->join('routes', 'trips.route_id', '=', 'routes.id')
            ->join('buses', 'trips.bus_id', '=', 'buses.id')
            ->select('trips.id', 'trips.start_time', 'routes.name as route_name', 'buses.name as bus_name', 'buses.model_name as bus_model')
            ->where(function ($q) use ($query) {
                $q->where('routes.name', 'like', '%'.$query.'%')
                    ->orWhere('buses.name', 'like', '%'.$query.'%');
            });

        if ($routeId > 0) {
            $q->where('trips.route_id', $routeId);
        }

        return $q
            ->limit(50)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'text' => sprintf('%s - %s (%s)', substr((string) $row->start_time, 0, 5), $row->bus_name, $row->route_name),
            ])
            ->all();
    }
}
