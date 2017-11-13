<?php

namespace App\Http\ViewCreators;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ViewOrderCreator
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function create(View $view)
    {
    	$order_details = DB::table('tbl_order')
    		->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
    		->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
    		->select('tbl_order.OrderId', 'tbl_jobsite.Address', 'tbl_order.OrderDate', 'tbl_order.status', 'tbl_order.TotalAmount', 'tbl_order.TaxAmount', 'tbl_order.DeliveryCharges')
    		->where('tbl_order.OrderId', Request::route('id'))->first();

        $view->with('order_details', $order_details);
        $view->with('mode', 'web');
    }
}