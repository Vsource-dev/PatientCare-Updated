<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\GuestLayout;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
          Paginator::useBootstrapFive();
        Blade::component('guest-layout', GuestLayout::class);
    }
}