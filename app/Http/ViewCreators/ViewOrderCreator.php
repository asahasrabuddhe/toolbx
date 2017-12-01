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
    		->selectRaw('tbl_order.OrderId, tbl_jobsite.Address, tbl_order.OrderDate, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled"  WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" ELSE "Pending" END) as status, tbl_order.TotalAmount, tbl_order.TaxAmount, tbl_order.DeliveryCharges')
    		->where('tbl_order.OrderId', Request::route('id'))->first();

        $view->with('order_details', $order_details);
        $view->with('mode', 'web');
    }
}