<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Editor extends Component
{
    public string $label;
    public string $name;
    public string $value;

    public function __construct($label, $name, $value = null)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value != null ? $value : "";
    }

    public function render(): View|Closure|string
    {
        return view('components.inputs.editor');
    }
}
