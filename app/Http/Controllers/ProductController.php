<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\ToolbxAPI;

class ProductController extends Controller
{
    protected $toolbxAPI;
    
    public function __construct()
    {
        $this->toolbxAPI = new ToolbxAPI;
    }

    public function getAllProducts(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $categoryId = $request->get('categoryId');
        $subCategoryId = $request->get('subCategoryId');
        $search = $request->get('search');


        $total = DB::table('tbl_product')->where( 'display', 'Y')->where('Rate', '>', '0')->count();

        if( $categoryId == 0 )
        {
            if( isset($search['value']) && !empty($search['value'])) {
                $total = DB::table('tbl_product')
                        ->where( 'display', 'Y')
                        ->where('Rate', '>', '0')->where('ProductName', 'like', '%' . $search['value'] . '%')->count();
                $filtered = $total;
                $products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->where('ProductName', 'like', '%' . $search['value'] . '%')
                            ->orderBy('ProductId', 'DESC')->get();
            } else {
                $filtered = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')                     
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->orderBy('ProductId', 'DESC')->count();
                $products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->orderBy('ProductId', 'DESC')->get();
            }
        }
        else if ( $subCategoryId == 0 )
        {
            if( isset($search['value']) && !empty($search['value'])) {
                $total = DB::table('tbl_product')
                        ->where( 'display', 'Y')
                        ->where('Rate', '>', '0')->where('ProductName', 'like', '%' . $search['value'] . '%')->where('CategoryId', $categoryId)->count();
            	$filtered = $total;
            	$products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->where('CategoryId', $categoryId)
                            ->where('ProductName', 'like', '%' . $search['value'] . '%')
                            ->orderBy('ProductId', 'DESC')->get();
            } else {
                $filtered = DB::table('tbl_product')->where( 'display', 'Y')->where('CategoryId', $categoryId)->where('Rate', '>', '0')->count();
                $products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->where('CategoryId', $categoryId)
                            ->orderBy('ProductId', 'DESC')->get();
            }
        }
        else
        {
            if( isset($search['value']) && !empty($search['value'])) {
                $total = DB::table('tbl_product')
                        ->where( 'display', 'Y')
                        ->where('Rate', '>', '0')->where('ProductName', 'like', '%' . $search['value'] . '%')
                        ->where('CategoryId', $categoryId)
                        ->where('SubCategoryId', $subCategoryId)->count();
            	$filtered = $total;
            	$products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->where('CategoryId', $categoryId)
                            ->where('SubCategoryId', $subCategoryId)
                            ->where('ProductName', 'like', '%' . $search['value'] . '%')
                            ->orderBy('ProductId', 'DESC')->get();
            } else {
                $filtered = DB::table('tbl_product')->where( 'display', 'Y')->where('CategoryId', $categoryId)->where('SubCategoryId', $subCategoryId)->where('Rate', '>', '0')->count();
                $products = DB::table('tbl_product')
                            ->select('ProductId', 'ProductName', 'ProductDetails', 'ProductImage', 'Rate')
                            // ->offset($start)->limit($length)                            
                            ->where( 'display','Y')
                            ->where('Rate', '>', '0')
                            ->where('CategoryId', $categoryId)
                            ->where('SubCategoryId', $subCategoryId)
                            ->orderBy('ProductId', 'DESC')->get();
            }
        }
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $products->toArray()
        ];

        return response()->json($data);
    }

    public function addProduct(Request $request)
    {
        $category = $request->get('category');
        $subCategory = $request->get('subcategory');
        $productName = $request->get('productname');
        $descripton = $request->get('description');
        $productImage = $request->get('productimage');
        $price = $request->get('price');
        $createdBy = Session::get('user_data')->admin_id;

        $response = $this->toolbxAPI->post('product/addproduct', '', [
            'Category' => $category,
            'SubCategory' => $subCategory,
            'ProductName' => $productName,
            'Description' => $descripton,
            'Image' => $productImage,
            'Price' => $price,
            'CreatedBy' => $createdBy,
        ]);

        if( $response->message_code == 1000) {
            Session::flash('success_msg', $message_text);
            Redirect::to('/admin/product/list_products');
        } else {
            Session::flash('error_message', $message_text);
            Redirect::to('/admin/product/list_products');
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $category = $request->get('category');
        $subCategory = $request->get('subcategory');
        $productName = $request->get('productname');
        $descripton = $request->get('description');
        $productImage = $request->get('newImage');
        $price = $request->get('price');
        $lastModifiedBy = Session::get('user_data')->admin_id;

        $response = $this->toolbxAPI->post('product/update', '', [
            'id' => $id,
            'Category' => $category,
            'SubCategory' => $subCategory,
            'ProductName' => $productName,
            'Description' => $descripton,
            'Image' => $productImage,
            'Price' => $price,
            'LastModifiedBy' => $lastModifiedBy,
        ]);

        if( $response->message_code == 1000 )
        {
            Session::flash('success_msg', $response->message_text);
            return Redirect::to('admin/product/list_products');
        }
    }

    public function deleteProduct(Request $request, $id)
    {
        $response = $this->toolbxAPI->delete('product/' . $id . '/deleteproduct');
        Session::flash('success_msg', 'Product deleted successufully');
        return Redirect::to('admin/product/list_products');
    }
}
