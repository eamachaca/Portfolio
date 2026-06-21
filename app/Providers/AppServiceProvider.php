<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('t', function (string $expression) {
            return "<?php echo e(\\App\\Support\\Locales::translate({$expression})); ?>";
        });
    }
}
