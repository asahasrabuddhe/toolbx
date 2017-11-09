<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ToolbxAPI;

class CategoryController extends Controller
{
    protected $toolbxAPI;

    public function __construct()
    {
        $this->toolbxAPI = new ToolbxAPI;
    }

    public function getAllCategories(Request $request)
    {
        $categories = DB::table('tbl_product_category')
                        ->select('CategoryId', 'CategoryName')
                        ->where( 'display','Y')->get();
        $response = [];
        foreach($categories as $category)
        {
        	if( $category->CategoryId == -1 )
        		continue;

        	$response[] = [
        		'id' => $category->CategoryId,
        		'text' => $category->CategoryName,
        	];
        }
  
        return response()->json(['results' => $response]);
    }

    public function getAllSubCategories(Request $request, $id)
    {
    	$response = [];

    	if( $id != 0 )
    	{
    		$categories = DB::table('tbl_product_sub_category')
                        ->select('SubCategoryId', 'SubCategoryName')
                        ->where( 'display','Y')
                        ->where('categoryId', $id)->get();

            foreach($categories as $category)
	        {
	        	$response[] = [
	        		'id' => $category->SubCategoryId,
	        		'text' => $category->SubCategoryName,
	        	];
	        }
    	}
  
        return response()->json(['results' => $response]);
    }
}
