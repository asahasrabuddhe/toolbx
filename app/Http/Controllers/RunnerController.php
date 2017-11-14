<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\ToolbxAPI;

class RunnerController extends Controller
{
    protected $toolbxAPI;

    public function __construct()
    {
        $this->toolbxAPI = new ToolbxAPI;
    }

    public function getAllRunners(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');

        if( isset($search['value']) && !empty($search['value'])) {
            $total = DB::table('tbl_registration')
                        ->where('RegsitrationRoleId', 3)
                        ->where( 'IsDeleted', 'N')
                        ->where('RegistrationName', 'like', '%' . $search['value'] . '%')->count();

            $runners = DB::table('tbl_registration')
                        ->select('RegistrationId', 'RegistrationName', 'RegistrationEmail', 'RegistrationPhoneNo')
                        ->offset($start)->limit($length)
                        ->where('RegsitrationRoleId', 3)
                        ->where( 'IsDeleted','N')
                        ->where('RegistrationName', 'like', '%' . $search['value'] . '%')
                        ->orderBy('RegistrationId', 'DESC')->get();
        } else {
            $total = DB::table('tbl_registration')->where('RegsitrationRoleId', 3)->where( 'IsDeleted', 'N')->count();

            $runners = DB::table('tbl_registration')
                        ->select('RegistrationId', 'RegistrationName', 'RegistrationEmail', 'RegistrationPhoneNo')
                        ->offset($start)->limit($length)
                        ->where('RegsitrationRoleId', 3)
                        ->where( 'IsDeleted','N')
                        ->orderBy('RegistrationId', 'DESC')->get();
        }
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $runners->toArray()
        ];

        return response()->json($data);
    }

    public function invite(Request $request)
    {
        $response = $this->toolbxAPI->post('user/invitation', '', [
            'Name' => $request->get('name'),
            'PhoneNo' => $request->get('phonenumber'),
            'Email' => $request->get('email')
            'CompanyName' => $request->get('company')
        ]);

        if( $response === NULL ) {
            Session::flash('success_msg','Runner Invitation Sent successfully');
            return $msg = 'Runner Invitation Sent successfully';
        } else {
            Session::flash('success_msg','Email already exist.');
            return $response->message_text;
        }
    }

    public function getRunner(Request $request, $id)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $total = DB::table('tbl_order')
                        ->join('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->where('tbl_runner_order.RunnerId', $id)
                        ->where( 'tbl_order.display','Y')
                        ->orderBy('tbl_order.OrderId', 'DESC')
                        ->count();

        $runners = DB::table('tbl_order')
                        ->join('tbl_runner_order', 'tbl_runner_order.OrderId', '=', 'tbl_order.OrderId')
                        ->join('tbl_registration', 'tbl_runner_order.RunnerId', '=', 'tbl_registration.RegistrationId')
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

    public function updateRunner(Request $request, $id)
    {
        $name = $request->get('name');
        $phoneno = $request->get('phoneno');
        $company = $request->get('company');
        $type = $request->get('type');

        if( isset($type) && $type == 'employee')
            Session::flash('success_msg', 'Employee Updated Successfully');
        else
            Session::flash('success_msg', 'Runner Updated Successfully');

        if( isset($type) && $type == 'employee')
        {
            DB::table('tbl_registration')
                ->where('RegistrationId', $id)
                ->update(['RegistrationName' => $name, 'RegistrationPhoneNo' => $phoneno]);
            
            return Redirect::to('/admin/company/' . $id . '/view#employees');
        }
        else if ( isset($type) && $type == 'owner' )
        {
            DB::table('tbl_registration')
                ->where('RegistrationId', $id)
                ->update(['RegistrationName' => $name, 'RegistrationPhoneNo' => $phoneno]);
            
            $companyId = DB::table('tbl_registration')->select('CompanyId')->where('RegistrationId', $id)->first();

            DB::table('tbl_companies')
                ->where('CompanyId', $companyId->CompanyId)
                ->update(['CompanyName' => $company]);
            
            return Redirect::to('/admin/company/' . $id . '/view#owner');
        }
        else
        {
            DB::table('tbl_registration')
                ->where('RegistrationId', $id)
                ->update(['RegistrationName' => $name, 'RegistrationPhoneNo' => $phoneno]);
            
            return Redirect::to('/admin/user/list_users');
        }
    }

    public function deleteRunner(Request $request, $id)
    {
        DB::table('tbl_registration')->where('RegistrationId', $id)->delete();

        Session::flash('success_msg', 'Runner Deleted Successfully');

        return Redirect::to('/admin/user/list_users');
    }

    public function updateAdmin(Request $request)
    {
        DB::table('tbl_administrator')
            ->where('admin_id', Session::get('user_data')->admin_id)
            ->update(['admin_name' => $request->get('name')]);

        Session::flash('success_msg', 'Admin Updated Successfully');

        $user_data = Session::get('user_data');
        $user_data->admin_name = $request->get('name');
        Session::put('user_data', $user_data);

        return Redirect::to('/admin/user/list_users');
    }

    public function changeAdminPassword(Request $request)
    {
        if( $request->get('new_password') !== $request->get('confirm_password'))
        {
            $response = $this->toolbxAPI->post('admin/changepassword', '', [
                'id' => Session::get('user_data')->admin_id,
                'old_password' => $request->get('current_password'),
                'new_password' => $request->get('new_password')
            ]);
        }
        else
        {
            Session::flash('New password and Confirm Password are not matching');
            return Redirect::to(url('/admin/change_password'));
        }

        if( $response === NULL ) {
            Session::flash('success_msg','Admin Password changed successfully');
            return $msg = 'Runner Invitation Sent successfully';
        } else {
            Session::flash('success_msg','Current password incorrect.');
            return $response->message_text;
        }
    }
}
