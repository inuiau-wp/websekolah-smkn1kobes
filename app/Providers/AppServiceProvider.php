<?php

namespace App\Providers;

use App\Models\Jurusan;
use App\Models\RaporPendidikan;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\View;
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
        app()->setLocale(session('locale', config('app.locale')));

        View::composer('*', function ($view) {
            $view->with('schoolProfile', SchoolProfile::query()->firstOrCreate(
                ['id' => 1],
                ['school_name' => 'SMKN 1 Kotabesi']
            ));
        });

        View::composer('layouts.navigation', function ($view) {
            $view->with([
                'jurusan' => Jurusan::all(),
                'raporYears' => RaporPendidikan::published()
                    ->orderByDesc('year')
                    ->pluck('year'),
            ]);
        });
    }
}
