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

        $fromDate = date('Y:m:d H:i:s', strtotime($request->get('fromDate')));
        $toDate = date('Y:m:d H:i:s', strtotime($request->get('toDate')));

        $total = DB::table('tbl_order')->where('display', 'Y')->count();

        if( isset($fromDate) && isset($toDate) ) {
            $filtered = DB::table('tbl_order')->where('display', 'Y')->whereBetween('OrderDate', [$fromDate, $toDate])->count();

            $orders = DB::table('tbl_order')
                        ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                        ->select('tbl_order.OrderId', 'tbl_jobsite.JobSiteName', 'tbl_order.TotalAmount', 'tbl_order.OrderDate', 'tbl_registration.RegistrationName', 'tbl_order.OrderDate', 'tbl_order.status')
                        ->offset($start)->limit($length)
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
        } else {
            $filtered = $total;

            $orders = DB::table('tbl_order')
                        ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                        ->select('tbl_order.OrderId', 'tbl_jobsite.JobSiteName', 'tbl_order.TotalAmount', 'tbl_order.OrderDate', 'tbl_registration.RegistrationName', 'tbl_order.OrderDate', 'tbl_order.status')
                        ->offset($start)->limit($length)
                        ->where( 'tbl_order.display','Y')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
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
        $fromDate = date('Y:m:d H:i:s', strtotime($request->query('from_date')));
        $toDate = date('Y:m:d H:i:s', strtotime($request->query('to_date')));

        if( isset($fromDate) && isset($toDate) ) {
            $filtered = DB::table('tbl_order')->where('display', 'Y')->whereBetween('OrderDate', [$fromDate, $toDate])->count();

            $orders = DB::table('tbl_order')
                        ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                        ->select('tbl_order.OrderId', 'tbl_jobsite.JobSiteName', 'tbl_order.TotalAmount', 'tbl_order.OrderDate', 'tbl_registration.RegistrationName', 'tbl_order.OrderDate', 'tbl_order.status')
                        ->where( 'tbl_order.display','Y')
                        ->whereBetween('OrderDate', [$fromDate, $toDate])
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
        } else {
            $filtered = $total;

            $orders = DB::table('tbl_order')
                        ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
                        ->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                        ->select('tbl_order.OrderId', 'tbl_jobsite.JobSiteName', 'tbl_order.TotalAmount', 'tbl_order.OrderDate', 'tbl_registration.RegistrationName', 'tbl_order.OrderDate', 'tbl_order.status')
                        ->where( 'tbl_order.display','Y')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
        }
        
        ini_set('auto_detect_line_endings', true);

        $csv_data = Helper::str_putcsv($orders->toArray());

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

        return PDF::loadHTML($html)->inline();
    }
}
