<?php

namespace App\View\Components\Menus;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuBar extends Component
{
    public string $icon;
    public string $name;
    public string $route;

    /**
     * Create a new component instance.
     */
    public function __construct($icon, $name, $route)
    {
        $this->icon = $icon;
        $this->name = $name;
        $this->route = $route;
    }


    public function render(): View|Closure|string
    {
        return view('components.menus.menu-bar');
    }
}
