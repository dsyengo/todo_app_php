<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct(
        public string $name,
        public string $size = '20',
        public string $class = '',
    ) {}

    public function render()
    {
        return view('components.icon');
    }
}
