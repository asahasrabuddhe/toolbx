<?php

namespace App\Http\ViewCreators;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

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
        else if( Request::is('admin/account')) {
            $runner = new \stdClass;
            $runner->admin_id = Session::get('user_data')->admin_id;
            $runner->RegistrationName = Session::get('user_data')->admin_name;
            $runner->RegistrationEmail = Session::get('user_data')->admin_email;
        }
        $view->with('user_info', $runner);
    }
}