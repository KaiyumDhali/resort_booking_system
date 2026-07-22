<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
{
    Builder::defaultStringLength(191);

    KTBootstrap::init();

    Blade::directive('formatDate', function ($date) {
        return "<?php echo (new \Carbon\Carbon($date))->format('d-m-Y'); ?>";
    });

    Blade::directive('formatCurrency', function ($expression) {
        return "<?php echo formatCurrency($expression); ?>";
    });

    if(Session::has('locale')){
        App::setLocale(Session::get('locale'));
    }else{
        App::setLocale('en');
    }
}
}
