<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Support\Admin\OptionSources\OptionSourceRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public function __invoke(Request $request, string $resource): JsonResponse
    {
        if (! OptionSourceRegistry::has($resource)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $q = trim((string) $request->input('q', ''));

        // Require minimum 2 characters to prevent full-table scans
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $source = OptionSourceRegistry::resolve($resource);
        $results = $source->search($q);

        return response()->json(['results' => $results]);
    }
}
