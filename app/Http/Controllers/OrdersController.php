<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\ToolbxAPI;
use App\Helper;
use PDF;

class OrdersController extends Controller
{
    protected $toolbxAPI;

    public function __construct()
    {
        $this->toolbxAPI = new ToolbxAPI;
    }

    public function getAllOrders(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $fromDate = date('Y-m-d', strtotime($request->query('fromDate'))) . ' 00:00:01';
        $toDate = date('Y-m-d', strtotime($request->query('toDate'))) . ' 23:59:59';
        
        $search = $request->get('search');

        if( isset($search['value']) && !empty($search['value'])) {
            $total = DB::table('tbl_order')->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')->where('tbl_order.display', 'Y')->count();
        } else {
            $total = DB::table('tbl_order')->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->where('tbl_order.display', 'Y')
                        ->where('tbl_companies.CompanyName', 'LIKE', '%' . $search['value'] . '%')
                        ->count();
        }

        if( isset($fromDate) && isset($toDate) ) {

            if( isset($search['value']) && !empty($search['value'])) {
                $filtered = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->where('tbl_companies.CompanyName', 'LIKE', '%' . $search['value'] . '%')
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
            $filtered = count($filtered);

            $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->offset($start)->limit($length)
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->where('tbl_companies.CompanyName', 'LIKE', '%' . $search['value'] . '%')
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
            } else {
                $filtered = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
            $filtered = count($filtered);

            $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->offset($start)->limit($length)
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();

            }
        } else {
            if( isset($search['value']) && !empty($search['value'])) {
                $filtered = $total;

                $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->offset($start)->limit($length)
                        ->where('tbl_order.display','Y')
                        ->where('tbl_companies.CompanyName', 'LIKE', '%' . $search['value'] . '%')
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
                
            } else {
                $filtered = $total;

                $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                        ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                        ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                        ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.TotalAmount, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RegistrationName, tbl_order.OrderDate, tbl_order.status, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                        ->offset($start)->limit($length)
                        ->where('tbl_order.display','Y')
                        ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();   
            }
        }
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $orders->toArray()
        ];

        return response()->json($data);
    }

    public function getAllOrdersCsv(Request $request)
    {
        $fromDate = date('Y-m-d', strtotime($request->query('from_date'))) . ' 00:00:01';
        $toDate = date('Y-m-d', strtotime($request->query('to_date'))) . ' 23:59:59';

        $ids = explode(',', $request->query('ids') );

        $company = $request->query('company');

        if( isset($fromDate) && isset($toDate) ) {
            if( NULL !==  $request->query('ids') ) {
                if( NULL !== $company ) {
                    $orders = DB::table('tbl_order')
                                ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                ->where('tbl_order.display','Y')
                                ->whereBetween('tbl_order.OrderDate', [$fromDate, $toDate])
                                ->whereIn('tbl_order.OrderId', $ids)
                                ->where('tbl_order.CompanyId', $company)
                                ->groupBy('tbl_order.OrderId')
                                ->orderBy('tbl_order.OrderId', 'DESC')->get();
                } else {
                    $orders = DB::table('tbl_order')
                                ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                ->where('tbl_order.display','Y')
                                ->whereBetween('tbl_order.OrderDate', [$fromDate, $toDate])
                                ->whereIn('tbl_order.OrderId', $ids)
                                ->groupBy('tbl_order.OrderId')
                                ->orderBy('tbl_order.OrderId', 'DESC')->get();
                }
            } else {
                if( NULL !== $company) {
                    $orders = DB::table('tbl_order')
                                ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                ->where('tbl_order.display','Y')
                                ->where('tbl_order.CompanyId', $company)
                                ->groupBy('tbl_order.OrderId')
                                ->orderBy('tbl_order.OrderId', 'DESC')->get();
                } else {
                    $orders = DB::table('tbl_order')
                                    ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                    ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                    ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                    ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                    ->leftJoin('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                                    ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                    ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                    ->where('tbl_order.display','Y')
                                    ->whereBetween('tbl_order.OrderDate', [$fromDate, $toDate])
                                ->groupBy('tbl_order.OrderId')
                                    ->orderBy('tbl_order.OrderId', 'DESC')->get();
                }
            }
        } else {
           if ( NULL != $company ) {
                 $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                ->where('tbl_order.display','Y')
                                ->where('tbl_order.CompanyId', $company)
                                ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
           } else {
                 $orders = DB::table('tbl_order')
                        ->leftJoin('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                                ->leftJoin('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_runner_order.RunnerId')
                                ->leftJoin('tbl_notifications', 'tbl_notifications.OrderId', '=', 'tbl_order.OrderId')
                                ->leftJoin('tbl_companies', 'tbl_order.CompanyId', '=', 'tbl_companies.CompanyId')
                                ->selectRaw('tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_companies.CompanyName, tbl_order.OrderDate, GROUP_CONCAT(DISTINCT(tbl_registration.RegistrationName)) AS RunnerName, GROUP_CONCAT(DISTINCT(tbl_runner_order.RunnerId)) AS RunnerId, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" WHEN tbl_order.status IS NULL THEN "Pending" ELSE "Pending" END) as status, (tbl_order.TotalAmount - tbl_order.TaxAmount - tbl_order.DeliveryCharges) as SubTotal, tbl_order.DeliveryCharges, tbl_order.TaxAmount,  tbl_order.TotalAmount AS Total, MAX(tbl_notifications.OrderRating) AS OrderRating, MAX(tbl_notifications.id)')
                                ->offset($start)->limit($length)
                                ->where('tbl_order.display','Y')
                                ->groupBy('tbl_order.OrderId')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
           }
        }
        
        ini_set('auto_detect_line_endings', true);

        $export = $orders->map(function($item){
           return array_except((array)$item, ['RunnerId', 'OrderRating', 'MAX(tbl_notifications.id)']);
        });

        $csv_data = Helper::str_putcsv($export->toArray());

        return response()->json([
            'message_code' => 1000, 'data_text' => $csv_data,
        ]);
    }

    public function getOrder(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_order')
                    ->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                    ->join('tbl_product', 'tbl_product.ProductId', '=', 'tbl_order_details.ProductId')
                    ->select('tbl_product.ProductName', 'tbl_order_details.Quantity', 'tbl_order_details.Amount')
                    ->where('tbl_order.OrderId', $id)
                    ->count();

        $order = DB::table('tbl_order')
                    ->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                    ->join('tbl_product', 'tbl_product.ProductId', '=', 'tbl_order_details.ProductId')
                    ->select('tbl_product.ProductName', 'tbl_order_details.Quantity', 'tbl_order_details.Amount')
                    ->offset($start)->limit($length)
                    ->where('tbl_order.OrderId', $id)
                    ->orderBy('tbl_order_details.ProductId')
                    ->get();

        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $order->toArray()
        ];

         return response()->json($data);
    }

    public function exportOrderPdf(Request $request, $id)
    {
        $order_details = DB::table('tbl_order')
                    ->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                    ->join('tbl_product', 'tbl_product.ProductId', '=', 'tbl_order_details.ProductId')
                    ->select('tbl_product.ProductName', 'tbl_order_details.Quantity', 'tbl_order_details.Amount')
                    ->where('tbl_order.OrderId', $id)
                    ->get();

        $view = View::make('admin.order.pdf', ['order_details_table' => $order_details]);
        
        $html = $view->render();

        return PDF::loadHTML($html)->stream();
    }

    public function exportOrderPdfInvoice(Request $request, $id)
    {
        $order = DB::table('tbl_order')
                    ->join('tbl_companies', 'tbl_companies.CompanyId', '=', 'tbl_order.CompanyId')
                    ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                    ->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                    ->selectRaw('tbl_companies.CompanyName, tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_registration.RegistrationName, tbl_order.OrderDate, (CASE WHEN tbl_order.status = "PAID" AND tbl_order.Delivered = "Y" THEN "Delivered" WHEN tbl_order.status = "PAID" AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status IS NULL AND tbl_order.IsCancel = "Y" THEN "Cancelled" WHEN tbl_order.status = "PAID" AND tbl_order.IsLeaving="Y" THEN "In Process" WHEN tbl_order.status = "PAID" AND tbl_order.IsAccepted="Y" THEN "Accepted" WHEN tbl_order.status = "declined" THEN "Payment Declined" ELSE "Pending" END) as status, tbl_order.TotalAmount, tbl_order.TaxAmount, tbl_order.DeliveryCharges')
                    ->where('tbl_order.OrderId', $id)
                    ->first();

        $order_details = DB::table('tbl_order')
                    ->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                    ->join('tbl_product', 'tbl_product.ProductId', '=', 'tbl_order_details.ProductId')
                    ->select('tbl_product.manufacturer', 'tbl_product.ProductName', 'tbl_product.sku', 'tbl_order_details.Quantity', 'tbl_order_details.Rate', 'tbl_order_details.Amount')
                    ->where('tbl_order.OrderId', $id)
                    ->get();

        $view = View::make('admin.order.invoice', ['order' => $order, 'order_details' => $order_details]);
        
        $html = $view->render();

        PDF::loadHTML($html)->save(public_path('invoices/' . $id . '_invoice.pdf'), true);
        if( $request->is('admin/order/*/export') )
            return response()->file(public_path('invoices/' . $id . '_invoice.pdf'));
        else {
            return 'file saved';
        }
    }
}
