<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::creator('admin.users.view', 'App\Http\ViewCreators\EditUserCreator');
        View::creator('admin.users.edit', 'App\Http\ViewCreators\EditUserCreator');
        View::creator('admin.company.view', 'App\Http\ViewCreators\EditCompanyCreator');
        View::creator('admin.company.edit', 'App\Http\ViewCreators\EditCompanyCreator');
        View::creator('admin.product.view', 'App\Http\ViewCreators\EditProductCreator');
        View::creator('admin.product.edit', 'App\Http\ViewCreators\EditProductCreator');
        View::creator('admin.order.view', 'App\Http\ViewCreators\ViewOrderCreator');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
