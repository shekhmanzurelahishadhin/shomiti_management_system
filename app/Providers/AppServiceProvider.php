<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Blade::directive('active', function ($expression) {
            return "<?php echo (request()->routeIs({$expression})) ? 'active' : ''; ?>";
        });
    }
}
