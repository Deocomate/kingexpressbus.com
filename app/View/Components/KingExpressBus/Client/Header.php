<?php

namespace App\View\Components\KingExpressBus\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Header extends Component
{

    public function __construct()
    {

    }

    public function render(): View|Closure|string
    {
        return view('components.king-express-bus.client.header');
    }
}
