<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ToolbxAPI;

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

        $total = DB::table('tbl_order')->where('display', 'Y')->count();

        $orders = DB::table('tbl_order')
        				->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', '=', 'tbl_order.JobSiteId')
        				->join('tbl_registration', 'tbl_registration.RegistrationId', '=', 'tbl_order.RegistrationId')
                        ->select('tbl_order.OrderId', 'tbl_jobsite.JobSiteName', 'tbl_order.TotalAmount', 'tbl_order.OrderDate', 'tbl_registration.RegistrationName', 'tbl_order.OrderDate', 'tbl_order.status')
                        ->offset($start)->limit($length)
                        ->where( 'tbl_order.display','Y')
                        ->orderBy('tbl_order.OrderId', 'DESC')->get();
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $orders->toArray()
        ];

        return response()->json($data);
    }
}
