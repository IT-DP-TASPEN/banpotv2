<?php

namespace App\Providers;

use App\Models\BanpotMaster;
use App\Models\BanpotMasterNeedApproveMitra;
use App\Observers\BanpotMasterApproveObserver;
use App\Observers\BanpotMasterObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //BanpotMaster::observe(BanpotMasterObserver::class);
        BanpotMasterNeedApproveMitra::observe(BanpotMasterApproveObserver::class);
    }
}
