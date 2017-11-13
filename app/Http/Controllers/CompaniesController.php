<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\ToolbxAPI;
use App\Helper;

class CompaniesController extends Controller
{
    protected $toolbxAPI;
    
    public function __construct()
    {
        $this->toolbxAPI = new ToolbxAPI;
    }

    public function getAllCompanies(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_companies')->where( 'display', 'Y')->count();

        $runners = DB::table('tbl_companies')
                        ->select('CompanyId', 'CompanyName')
                        ->offset($start)->limit($length)                            
                        ->where( 'display','Y')
                        ->orderBy('CompanyId', 'DESC')->get();
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function getAllCompaniesCsv(Request $request)
    {
        $runners = DB::table('tbl_companies')
                        ->select('CompanyId', 'CompanyName')                           
                        ->where( 'display','Y')
                        ->orderBy('CompanyId', 'DESC')->get();

        ini_set('auto_detect_line_endings', true);

        $csv_data = Helper::str_putcsv($runners->toArray());

        return response()->json([
            'message_code' => 1000, 'data_text' => $csv_data,
        ]);
    }

    public function invite(Request $request)
    {
        $response = $this->toolbxAPI->post('owner/invitation', '', [
            'Name' => $request->get('name'),
            'PhoneNo' => $request->get('phonenumber'),
            'Email' => $request->get('email'),
            'Company' => $request->get('company')
        ]);

        if( $response === NULL ) {
            Session::flash('success_msg','Owner Invitation Sent successfully');
            return $msg = 'Owner Invitation Sent successfully';
        } else {
            Session::flash('success_msg','Email or Company already exist.');
            return $response->message_text;
        }
    }

    public function getOwner(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_order')->where( 'tbl_order.RegistrationId',$id)->count();

        $runners = DB::table('tbl_registration')
                        ->join('tbl_order', 'tbl_order.RegistrationId', '=', 'tbl_registration.RegistrationId')
                        ->join('tbl_companies', 'tbl_companies.CompanyId', '=', 'tbl_order.CompanyId')
                        ->join('tbl_order_details', 'tbl_order_details.OrderId', '=', 'tbl_order.OrderId')
                        ->join('tbl_product', 'tbl_product.ProductId', '=', 'tbl_order_details.ProductId')
                        ->selectRaw('tbl_order.OrderId, tbl_companies.CompanyName, tbl_order.TotalAmount, GROUP_CONCAT(tbl_product.ProductName) AS ProductName, tbl_order.status' )
                        ->offset($start)->limit($length)
                        ->where('tbl_registration.RegistrationId', $id)
                        ->where( 'tbl_order.display','Y')
                        ->orderBy('tbl_order.OrderId', 'DESC')
                        ->groupBy('tbl_order.OrderId')->get();
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function updateOwner(Request $request, $id)
    {
        $company = $request->get('company');

        DB::table('tbl_companies')
            ->where('CompanyId', $id)
            ->update(['CompanyName' => $company]);

        Session::flash('success_msg', 'Owner Updated Successfully');

        return Redirect::to('/admin/company/list_companies');
    }

    public function deleteOwner(Request $request, $id)
    {
        DB::table('tbl_companies')->where('CompanyId', $id)->delete();

        Session::flash('success_msg', 'Owner Deleted Successfully');

        return Redirect::to('/admin/company/list_companies');
    }

    public function getOrders(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_order')->where('tbl_order.CompanyId',$id)->count();

        $runners = DB::table('tbl_order')
                        ->join('tbl_jobsite', 'tbl_jobsite.JobSiteId', 'tbl_order.JobSiteId')
                        ->join('tbl_order_details', 'tbl_order_details.OrderId', 'tbl_order.OrderId')
                        ->join('tbl_product', 'tbl_product.ProductId', 'tbl_order_details.ProductId')
                        ->selectRaw('tbl_order.OrderDate, tbl_order.OrderId, tbl_jobsite.JobSiteName, tbl_order.TotalAmount, GROUP_CONCAT(tbl_product.ProductName) AS ProductName')
                        ->offset($start)->limit($length)
                        ->where('tbl_order.CompanyId',$id)
                        ->groupBy('tbl_order.OrderId')
                        ->get();
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function getEmployees(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_registration')
                    ->where('CompanyId',$id)
                    ->where('RegsitrationRoleId', 4)
                    ->count();

        $runners = DB::table('tbl_registration')
                        ->select('RegistrationName', 'RegistrationPhoneNo', 'RegistrationEmail', 'RegistrationId')
                        ->offset($start)->limit($length)
                        ->where('CompanyId',$id)
                        ->where('RegsitrationRoleId', 4)
                        ->get();
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function getOwners(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_registration')
                    ->where('CompanyId',$id)
                    ->where('RegsitrationRoleId', 2)
                    ->count();

        $runners = DB::table('tbl_registration')
                        ->select('RegistrationName', 'RegistrationPhoneNo', 'RegistrationEmail', 'RegistrationId')
                        ->offset($start)->limit($length)
                        ->where('CompanyId',$id)
                        ->where('RegsitrationRoleId', 2)
                        ->get();
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function getPayments(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_order')->join('tbl_payments', 'tbl_payments.OrderId', 'tbl_order.OrderId')->where('tbl_order.CompanyId',$id)->count();

        $runners = DB::table('tbl_order')
                        ->join('tbl_payments', 'tbl_payments.OrderId', 'tbl_order.OrderId')
                        ->select('tbl_payments.PaymentDate', 'tbl_payments.OrderId', 'tbl_payments.CardStripTokan', 'tbl_payments.TotalAmount')
                        ->offset($start)->limit($length)
                        ->where('tbl_order.CompanyId',$id)
                        ->get();
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }
}
