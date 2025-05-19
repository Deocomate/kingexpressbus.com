<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditorArray extends Component
{
    public string $label;
    public string $name;
    public $value;

    public function __construct($label, $name, $value = [])
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value ?: [];
    }

    public function render(): View|Closure|string
    {
        return view('components.inputs.editor-array');
    }
}
