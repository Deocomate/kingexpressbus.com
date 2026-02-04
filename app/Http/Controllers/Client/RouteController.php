<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\TripController;

/**
 * RouteController - redirects to TripController for backwards compatibility.
 *
 * @deprecated Use TripController instead
 */
class RouteController extends TripController
{
    // This class extends TripController for backwards compatibility
    // All functionality is now in TripController
}

