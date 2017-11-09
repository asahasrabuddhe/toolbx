<?php

namespace App\Http\ViewCreators;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class EditUserCreator
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function create(View $view)
    {
        if( Request::is('admin/user/*'))
            $runner = DB::table('tbl_registration')->where('RegistrationId', Request::route('id'))->first();
        else if ( Request::is('admin/employee/*') || Request::is('admin/owner/*'))
            $runner = DB::table('tbl_registration')->join('tbl_companies', 'tbl_companies.CompanyId', '=', 'tbl_registration.CompanyId')->where('RegistrationId', Request::route('id'))->first();
        $view->with('user_info', $runner);
    }
}