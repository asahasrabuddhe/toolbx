<?php

namespace App\Http\ViewCreators;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class EditCompanyCreator
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function create(View $view)
    {
    	$owner = DB::table('tbl_companies')->where('CompanyId', Request::route('id'))->first();
        $view->with('company_info', $owner);
    }
}