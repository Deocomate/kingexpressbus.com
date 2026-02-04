<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use App\Support\Client\SearchDataBuilder;

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
        // 1. Search bar data (location dropdown)
        $searchData = SearchDataBuilder::make();

        // 2. Popular routes (top 8 by priority)
        $popularRoutes = $this->homeService->getPopularRoutes(8);

        // 3. Featured buses (single-tenant: show buses instead of companies)
        $featuredBuses = $this->homeService->getFeaturedBuses(8);

        // 4. Statistics (optional for display)
        $statistics = $this->homeService->getStatistics();

        return view('client.home.index', compact(
            'searchData',
            'popularRoutes',
            'featuredBuses',
            'statistics'
        ));
    }
}
