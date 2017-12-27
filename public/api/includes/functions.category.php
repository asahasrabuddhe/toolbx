<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

function tbx_category_list( Request $request, Response $response)
{
 	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

 	$db = database();
	$base_query = 'SELECT CategoryId, CategoryName, CategoryDetails FROM `tbl_product_category` WHERE `display`=\'Y\' ';
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ) )
	{
		$res = array( 'message_code' => 1000, 'data_text' => $result );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Database error! No any categories found.' ) );
	}
	return $response->withJson( $res, 200 );
}

 /*
 * List of sub categoery depends on category id
 */
function tbx_subcategory_list( Request $request, Response $response)
{
 	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

 	$db = database();
 	$id = $request->getAttribute('id');
	//$base_query = 'SELECT tpc.CategoryName,tpc.CategoryDetails, tpsc.SubCategoryId, tpsc.SubCategoryName FROM `tbl_product_category` AS tpc LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpc.CategoryId=tpsc.CategoryId WHERE tpc.CategoryId= "'.$id.'"  AND tpsc.display=\'Y\' ';
	$base_query = ' SELECT tpc.CategoryName, tpsc.SubCategoryId, tpsc.SubCategoryName, tpsc.SubCategoryDetails, tpsc.CategoryId ,tpc.CategoryId FROM `tbl_product_sub_category` tpsc, `tbl_product_category` tpc WHERE  tpc.CategoryId = tpsc.CategoryId AND tpc.CategoryId="'.$id.'" AND tpsc.display=\'Y\' ORDER BY tpsc.SubCategoryId DESC ';
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ) )
	{
		//return $response->withJson( $result );
		$res = array( 'message_code' => 1000, 'data_text' => $result );
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Database error! No any categories found.' );
	}
	return $response->withJson( $res, 200 );
}
 
 
/*
* Category menu with subcategory
*/
function tbx_category_menus( Request $request, Response $response)
{

	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

 	$db = database();
	$base_query = 'SELECT CategoryId, CategoryName, CategoryDetails FROM `tbl_product_category` WHERE `display`=\'Y\' ';
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ) )
	{
		foreach($result as $row)
		{
			$id = $row->CategoryId;
			$base_query1 = ' SELECT tpsc.SubCategoryId, tpsc.SubCategoryName, tpsc.SubCategoryDetails FROM `tbl_product_sub_category` tpsc, `tbl_product_category` tpc WHERE  tpc.CategoryId = tpsc.CategoryId AND tpc.CategoryId="'. $id . '" AND tpsc.display=\'Y\' ';
			$result1 = $db->get_results( $base_query1 );
			if( isset( $result1 ) && !empty( $result1 ) )
				$row->SubCategories = $result1;
				
			else
				$row->SubCategories = null;
		}
		$res = array( 'message_code' => 1000, 'data_text' => $result );
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Database error! No any categories found.' );
	}	

	return $response->withJson( $res, 200 );
}
 

 
 
 /*
 * List of sub categoery ALL
 */
function tbx_subcategory_listAll( Request $request, Response $response)
{
 	$db = database();
 	//$id = $request->getAttribute('id');
	$base_query = 'SELECT tpsc.SubCategoryId, tpsc.SubCategoryName,tpsc.SubCategoryDetails, tpc.CategoryId, tpc.CategoryName, tpc.CategoryDetails FROM `tbl_product_sub_category` tpsc, `tbl_product_category` tpc WHERE tpc.CategoryId=tpsc.AllId AND tpsc.display=\'Y\' ';
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ) )
	{
		return $response->withJson( $result );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 900 ) );
	}
	return $response->withJson( $res, 200 );
}