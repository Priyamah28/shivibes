<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public function __construct(
        public string $title = 'Welcome to Shivibes',
        public ?string $subtitle = null,
        public string $variant = 'default',
    ) {}

    public function render(): View
    {
        return view('layouts.guest');
    }
}
