<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\HomeService;

class HomeController extends Controller
{
    protected HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Display the homepage.
     */
    public function index()
    {
        // Popular routes (top 8 by priority)
        $popularRoutes = $this->homeService->getPopularRoutes(8);

        // Featured buses (single-tenant: show buses instead of companies)
        $featuredBuses = $this->homeService->getFeaturedBuses(8);

        $featuredDestinations = $this->homeService->getFeaturedDestinations();

        // Statistics (optional for display)
        $statistics = $this->homeService->getStatistics();

        return view('client.home.index', compact(
            'popularRoutes',
            'featuredBuses',
            'featuredDestinations',
            'statistics'
        ));
    }
}
