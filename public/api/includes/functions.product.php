<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 

function tbx_product_list( Request $request, Response $response )
{
	$db = database();
	
	//$base_query = ' SELECT ProductId, ProductName, ProductDetails, Rate, ProductImage FROM `tbl_product` WHERE display = \'Y\' ORDER BY ProductId DESC ';
	$base_query = ' SELECT ProductId, ProductName, ProductDetails, Rate, ProductImage FROM `tbl_product` WHERE Rate NOT IN (0.00) AND display = \'Y\' ORDER BY ProductId DESC LIMIT 10'; //LIMIT 10
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ))
	{
		//return $response->withJson( $result );
		$res = array( 'message_code' => 1000, 'data_text' => $result );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 999 ) );
	}
	return $response->withJson( $res, 200 );
}

// Products Details All/unique

function tbx_product_details(Request $request, Response $response)
{
	$db = database();
	$id = $request->getAttribute('id');

	if( $id < 0 || $id == 0)
	{
		/*$base_query = ' SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
						LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
						LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
						WHERE  tp.display=\'Y\' ORDER BY tp.ProductId DESC';*/

		/*$base_query = ' SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
						LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
						LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
						WHERE  tp.Rate not IN (0.00) AND tp.display=\'Y\' ORDER BY tp.ProductId DESC';
		*/

		$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,
							tpsc.SubCategoryName, tp.Rate,  
							count(tod.ProductId) as pidcnt
							FROM `tbl_product` AS tp
							LEFT JOIN `tbl_order_details` As tod On tod.ProductId = tp.ProductId
							LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							WHERE tp.Rate NOT IN (0.00) AND tp.display='Y' GROUP BY tp.ProductId  ORDER BY pidcnt DESC ";

		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 999, 'message_text' => 'Products not found for the category. Please try with other categories.' ) );
		}
	}
	else
	{
		/*$base_query = ' SELECT tp.ProductId, tp.ProductImage,tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
						LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
						LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
						WHERE  tp.Rate NOT IN ( 0.00 )
						AND tpc.CategoryId="'.$id.'" AND  tp.display=\'Y\' ORDER BY tp.ProductId DESC ';*/
		
		$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,
							tpsc.SubCategoryName, tp.Rate,  
							count(tod.ProductId) as pidcnt
							FROM `tbl_product` AS tp
							LEFT JOIN `tbl_order_details` As tod On tod.ProductId = tp.ProductId
							LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							WHERE tp.Rate NOT IN (0.00) AND tpc.CategoryId = '". $id ."' AND tp.display='Y' GROUP BY tp.ProductId  ORDER BY pidcnt DESC ";



		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 999, 'message_text' => 'Products not found for the category. Please try with other categories.' ) );
		}
	}
	return $response->withJson( $res, 200 );	
} 

//21/04/2017
/*
* order (owner/Employee) (mobileapp)
*/

function tbx_order(Request $request, Response $response)
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$orderby = $body['OrderBy'];
	$companyid = $body['CompanyId'];
	$ownerid = $body['OwnerId']; //order placed by owner and employee then get orderid
	$employeeid= $body['EmployeeId'];
	$pickrunnerid= $body['PickRunnerId'];
	$orderdate = date('Y-m-d H:i:s');
	$CreatedBy= $body['CreatedBy']; //roleid here
	
	
	$result = 'INSERT INTO `tbl_order`(OrderBy,CompanyId,OwnerId,EmployeeId,PickRunnerId,OrderDate,CreatedBy) VALUES("'.$orderby.'","'.$companyid.'","'.$ownerid.'","'.$employeeid.'","'.$pickrunnerid.'","'.$orderdate.'", "'.$CreatedBy.'")';

	$base_query = $db->query($result);
	if(!$base_query )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Order failed please try again.');
	}
	else
	{		
		$res = array( 'message_code' => 1000,'message_text' => 'Order Placed Successfully.', 'data_text'=>$base_query);
	}
	return $response->withJson( $res, 200 );
}

/*
* Product order (mobileapp)
*/

function tbx_order_product(Request $request, Response $response)
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$productid = $body['ProductId'];
	$orderid = $body['OrderId']; //order placed by owner and employee then get orderid
	$quantity= $body['Quantity'];
	$rateprice= $body['Rate'];
	$amount= $body['Amount']; // amount multiplyed on quantity and rate
	$CreatedBy= $body['CreatedBy']; //roleid here
	$CreatedOn = date('Y-m-d H:i:s');

	$result = 'INSERT INTO `tbl_order_details`(ProductId,OrderId,Quantity,Rate,Amount,CreatedBy,CreatedOn) VALUES("'.$productid.'","'.$orderid.'","'.$quantity.'","'.$rateprice.'","'.$amount.'", "'.$CreatedBy.'", "'.$CreatedOn.'")';
	$base_query = $db->query($result);
	if(!$base_query )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Your product order failed please try again.');
	}
	else
	{		
		$res = array( 'message_code' => 1000,'message_text' => 'Product Order Placed Successfully.', 'data_text'=>$base_query);
	}
	return $response->withJson( $res, 200 );
}





/************** backend product menu functions*****************/

//Add Products
function tbx_add_products(Request $request, Response $response)
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$category = $body['Category'];
	$SubCategory = $body['SubCategory'];
	$productname= $body['ProductName'];
	$productdetaile= $body['Description'];
	$image= $body['Image'];
	$rate = $body['Price'];
	$manufacturer = $body['Manufacturer'];
	$model = $body['Model'];
	$sku = $body['SKU'];
	$CreatedBy = $body['CreatedBy'];
	$CreatedOn = date('Y-m-d H:i:s');


	/*$actualetrate = $body['Price'];
	$Profitrate = 15 * $actualetrate / 100;
	$rate = $Profitrate + $actualetrate;*/

	if (($category == null) || ($category==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Category cannot be blank.');
	else if(($SubCategory == null) || ($SubCategory==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Sub Category cannot be blank.');
	else
		$result = 'INSERT INTO `tbl_product`(CategoryId,SubCategoryId,ProductName,ProductDetails,ProductImage,Rate,model,manufacturer,sku,CreatedBy,CreatedOn) VALUES ("'.$category.'","'.$SubCategory.'","'.$productname.'","'.$productdetaile.'","'.$image.'","'.$rate.'","'.$model.'","'.$manufacturer.'","'.$sku.'", "'.$CreatedBy.'" ,"'.$CreatedOn.'")';

		$base_query = $db->query($result);

		if(!$base_query )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Oops! Something went wrong please try again.','data_text'=>$base_query);
		}
		else
		{		
			$res = array( 'message_code' => 1000,'message_text' => 'Product Added Successfully.','data_text'=>$base_query);
		}
	return $response->withJson( $res, 200 );
}

/*
* Delete Product
*/ 
function tbx_product_delete(Request $request, Response $response)
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$id = $request->getAttribute('id');
	$deleted_by = "1";
	$deletedON = date('Y-m-d H:i:s');
	$base_query = ' UPDATE `tbl_product` SET `display`= \'N\', `DeletedOn`="'.$deletedON.'", `DeletedBy`="'.$deleted_by.'" WHERE `ProductId` = "'.$id.'" ';
	if( $db->query( $base_query ))
	{
		$res = array( 'message_code' => 1000, 'message_text' => 'Company deleted successfully.');
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Company not found.');
	}
	return $response->withJson( $res, 200 );
}

/*
* Get product detail
*/
function tbx_product_get_edit_single( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$id = $request->getAttribute('id');
	//$base_query = 'SELECT * FROM `tbl_product` WHERE ProductId= "' . $id . '" AND display= \'Y\' ';
	$base_query = ' SELECT tp.*,tpc.CategoryName,tpsc.SubCategoryName FROM tbl_product as tp LEFT JOIN tbl_product_category as tpc on tpc.CategoryId = tp.CategoryId LEFT JOIN tbl_product_sub_category as tpsc on tpsc.SubCategoryId = tp.SubCategoryId WHERE ProductId= "' . $id . '" AND tp.display= \'Y\'  ';
	$user = $db->get_row( $base_query );
	if( $user )
	{
		$user->Registration_Password = "";
		$res = array( 'message_code' => 1000, 'data_text' => $user );
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Product details not found.');
		
	}
	return $response->withJson( $res, 200 );
}

/*
* Product Update
*/
function tbx_product_update_data( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	
	$db = database();
	$body = $request->getParsedBody();
	$id = $body['id'];
	$category = $body['Category'];
	$SubCategory = $body['SubCategory'];
	$productname= $body['ProductName'];
	$productdetaile= $body['Description'];
	$image= $body['Image'];
	$rate= $body['Price'];
	$manufacturer = $body['Manufacturer'];
	$model = $body['Model'];
	$sku = $body['SKU'];
	$lastmodified_by= $body['LastModifiedBy'];
	$lastmodified_on= date('Y-m-d H:i:s');
	/*$actualetrate = $body['Price'];
	$Profitrate = 15 * $actualetrate / 100;
	$rate = $Profitrate + $actualetrate;*/
	
	
	
	$base_query ='UPDATE `tbl_product` SET `CategoryId`="'.$category.'",`SubCategoryId`="'.$SubCategory.'",`ProductName`="'.$productname.'", `ProductDetails`= "'.$productdetaile.'", `Rate`="'.$rate.'", `model`="'.$model.'", `manufacturer`="'.$manufacturer.'", `sku`="'.$sku.'",`ProductImage`= "'.$image.'",`LastModifiedBy` = "'.$lastmodified_by.'", `LastModifiedOn` = "'.$lastmodified_on.'" WHERE `ProductId`= "'.$id .'" ';
	
	$success = $db->query( $base_query );
	
	if($success)
	{
            $res = array( 'message_code' => 1000, 'message_text' => 'Product updated successfully.');
    } 
    else
    {
       $res = array( 'message_code' => 999, 'message_text' => 'Failed to update Product Details.');
    } 
	
	return $response->withJson( $res, 200 );
}

/*
* Populated table prduct list(On select subcategory).
*/
function tbx_sub_CategoryProductList( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$SubCAtId = $request->getAttribute('SubCatId');
	$base_query = $db->get_results(' SELECT ProductId, ProductName, ProductDetails, Rate, ProductImage FROM `tbl_product` WHERE Rate NOT IN(0.00) AND SubCategoryId="'.$SubCAtId.'" AND display = \'Y\' ORDER BY ProductId DESC LIMIT 10');
	if(!$base_query )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Data not found.');
	}
	else 
	{
		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
	}
	return $response->withJson( $res, 200 );
}


    /*
    * Recent purchases list (app) 070617
    */
    
    function tbx_recent_purchases(Request $request, Response $response)
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

    	$db = database();
    	$registrationid = $request->getAttribute('RegistrationId');
    	
  //   	//$base_query = $db->get_results(' SELECT DISTINCT(tod.ProductId) ,tbo.RegistrationId, tp.ProductName, tp.SubCategoryId,tp.CategoryId,tp.ProductDetails, tp.Rate,tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.ProductImage FROM `tbl_product` AS tp LEFT JOIN `tbl_order_details` AS tod ON tod.ProductId = tp.ProductId LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId WHERE tbo.RegistrationId= "'.$registrationid.'"  AND tod.display = \'Y\' ');

    	$sSQL = ' SELECT 
										tod.ProductId,
										tbo.RegistrationId, 
										tp.ProductName, 
										tp.SubCategoryId,
										tp.CategoryId,tp.
										ProductDetails, tod.Rate,
										tpc.CategoryName,
										tpc.CategoryId,tpsc.SubCategoryName,
										tp.ProductImage,
										tp.manufacturer,
										tp.model,
										tp.sku

										FROM `tbl_product` AS tp 
										LEFT JOIN `tbl_order_details` AS tod ON tod.ProductId = tp.ProductId 
										LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId 
										LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId 
										LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId 
										WHERE tbo.RegistrationId="'.$registrationid.'"  AND tod.display = \'Y\'  GROUP BY tod.ProductId ORDER BY tbo.Orderdate DESC ';
		//echo $sSQL . "<br/>";
		

     	$base_query = $db->get_results($sSQL);
    	
  //   	//$base_query = $db->get_results(' SELECT DISTINCT(tod.ProductId) ,tbo.RegistrationId, tp.ProductName, tp.SubCategoryId,tp.CategoryId,tp.ProductDetails, tp.Rate,tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.ProductImage FROM `tbl_product` AS tp LEFT JOIN `tbl_order_details` AS tod ON tod.ProductId = tp.ProductId LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId WHERE tbo.RegistrationId= "'.$registrationid.'"  AND tod.display = \'Y\' ORDER BY tod.ProductId DESC ');
  //       //$base_query = $db->get_results(' SELECT tod.ProductId, tp.ProductName, tp.Rate, tp.ProductDetails, tp.ProductImage, tp.SubCategoryId, tp.CategoryId, tpc.CategoryName,tpsc.SubCategoryName FROM `tbl_order_details` AS tod LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId WHERE OrderId = (SELECT MAX(OrderId) AS OrderId FROM `tbl_order` WHERE RegistrationId = "'.$registrationid.'" ) AND tod.display = \'Y\' ORDER BY tod.ProductId DESC  ');
    	if(!$base_query )
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'No reacent purchase available.');
    	}
    	else 
    	{
    		$RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");

    		foreach($base_query as $product)
			{	
				$product->ProductDetails = htmlspecialchars($product->ProductDetails);
				$product->ProductName = htmlspecialchars($product->ProductName);

				$product->Rate =  round($product->Rate + (($product->Rate * $RateThresold) /100),2) . "";
				$product->Rate = number_format( $product->Rate, 2, '.', '' );
			}
			
    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
    	}
    	return $response->withJson( $res, 200 );
    }


    //30/07/2017
    // search sub categoery
   	function tbx_search_subcategory( Request $request, Response $response )
   	{
   		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

   		$db = database();
		$body = $request->getParsedBody();
		$catid = $body['CatId'];
		$name = $body['SubCatName'];
		$base_query = $db->get_results(" SELECT SubCategoryId, CategoryId, SubCategoryName FROM tbl_product_sub_category WHERE CategoryId = '". $catid ."' AND SubCategoryName LIKE  '%" .$name . "%' AND display = 'Y' ");
		if(!$base_query )
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'Not available.');
    	}
    	else 
    	{
    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
    	}
    	return $response->withJson( $res, 200 );

   	}

   	function tbx_search_product( Request $request, Response $response )
   	{
   		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

   		$db = database();
		$body = $request->getParsedBody();
		$subcatid = $body['SubCatId'];
		$pname = $body['ProductName'];
		$base_query = $db->get_results(" SELECT SubCategoryId, ProductName FROM tbl_product WHERE SubCategoryId = '". $subcatid ."' AND ProductName LIKE  '%" .$pname . "%' AND display = 'Y' ");
		if(!$base_query )
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
    	}
    	else 
    	{
    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
    	}
    	return $response->withJson( $res, 200 );
   	}

   	/*
   	* product search on category 
   	*/
   	function tbx_search_product_name( Request $request, Response $response )
   	{
   		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

   		$db = database();
		$body = $request->getParsedBody();

		$RegId = $body['RegistrationId'];		
		$catid = $body['CatId'];
		//$subcatid = $body['SubCatId'];
		$pname = $body['ProductName'];

		if( $catid == 0 ) // All		
		{
			//$base_query = $db->get_results(" SELECT CategoryId,SubCategoryId, ProductName FROM tbl_product WHERE SubCategoryId = '". $subcatid ."' AND ProductName LIKE  '%" .$pname . "%' AND display = 'Y' ");
			//$base_query = $db->get_results(" SELECT CategoryId,SubCategoryId, ProductName FROM tbl_product WHERE ProductName LIKE  '%" .$pname . "%' AND display = 'Y' ");
			
			$base_query = $db->get_results(" 
											SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate 
											FROM `tbl_product` AS tp 
											LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId
											LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
											WHERE tp.Rate NOT IN (0.00) AND tp.ProductName LIKE '%" .$pname . "%' AND tp.display= 'Y'  ");
			if(!$base_query)
	    	{
	    		$res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else if($base_query == 0)
	    	{
	    	    $res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else 
	    	{
	    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
	    	}
    	}
    	else if($catid == -1) // recent purchas search
    	{
    		$base_query = $db->get_results(" SELECT 
							tod.ProductId, tbo.RegistrationId, tp.ProductName, tp.SubCategoryId, tp.CategoryId, 
							tp.ProductDetails, tp.Rate, tpc.CategoryName, tpc.CategoryId, tpsc.SubCategoryName, tp.ProductImage
							FROM `tbl_product` AS tp
							LEFT JOIN `tbl_order_details` AS tod ON tod.ProductId = tp.ProductId
							LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId
							WHERE 
							tp.Rate NOT IN (0.00) AND tbo.RegistrationId = '". $RegId ."' AND tod.display = 'Y' AND tp.ProductName LIKE '%" .$pname . "%' ");
    	
    		if(!$base_query)
	    	{
	    		$res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else if($base_query == 0)
	    	{
	    	    $res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else 
	    	{
	    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
	    	}
    	}		
		else
		{
			//$base_query = $db->get_results(" SELECT CategoryId,SubCategoryId, ProductName FROM tbl_product WHERE CategoryId = '". $catid ."' AND SubCategoryId = '". $subcatid ."' AND ProductName LIKE  '%" .$pname . "%' AND display = 'Y' ");
			
			$base_query = $db->get_results(" SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate 
											FROM `tbl_product` AS tp 
											LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId
											LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
											WHERE tp.Rate NOT IN (0.00) AND  tp.CategoryId = '". $catid ."'  AND tp.ProductName LIKE '%" .$pname . "%' AND tp.display= 'Y' ");
			if(!$base_query)
	    	{
	    		$res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else if($base_query == 0)
	    	{
	    	    $res = array( 'message_code' => 999, 'message_text' => 'Product not available.');
	    	}
	    	else 
	    	{
	    		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
	    	}
	    }
    	return $response->withJson( $res, 200 );
   	}


   /*
   * product lazy loading on category
   */
    function tbx_product_details_lazyloading(Request $request, Response $response)
    {
    	$db = database();
    	$id = $request->getAttribute('CatId'); //CategoryId
    
    	$body = $request->getParsedBody();
    	$count = $body['Count'];
    
        if(isset($count) &&  !empty($count))
    	{
    		$count = $count;
    	}
    	else
    	{
    		$count = 0;
    	}
    
    	if( $id <= 0 ) //|| $id == 0
    	{
    		/*$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
    						LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
    						LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
    						WHERE tp.Rate not IN (0.00) AND tp.display='Y' ORDER BY tp.ProductId DESC ";
    		*/
    		$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,
							tpsc.SubCategoryName, tp.Rate,  
							count(tod.ProductId) as pidcnt
							FROM `tbl_product` AS tp
							LEFT JOIN `tbl_order_details` As tod On tod.ProductId = tp.ProductId
							LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							WHERE tp.Rate NOT IN (0.00) AND tp.display='Y' GROUP BY tp.ProductId  ORDER BY pidcnt DESC ";
			//echo $base_query;exit;							
    		$base_query = $base_query . " LIMIT $count,10";				
    		$result = $db->get_results( $base_query );
    		if( isset( $result ) && !empty( $result ) )
    		{
    			$res = array( 'message_code' => 1000, 'data_text' => $result );
    		}
    		else
    		{
    			return $response->withJson( array( 'message_code' => 999, 'message_text' => 'Products not found for the category. Please try with other categories.' ) );
    		}
    	}
    	else
    	{
    		/*$base_query = " SELECT tp.ProductId, tp.ProductImage,tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
    						LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
    						LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
    						WHERE tp.Rate not IN (0.00) AND tpc.CategoryId='". $id ."' AND  tp.display='Y' ORDER BY tp.ProductId DESC ";*/
    		
    		$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,
							tpsc.SubCategoryName, tp.Rate,  
							count(tod.ProductId) as pidcnt
							FROM `tbl_product` AS tp
							LEFT JOIN `tbl_order_details` As tod On tod.ProductId = tp.ProductId
							LEFT JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							WHERE tp.Rate NOT IN (0.00) AND tpc.CategoryId = '". $id ."' AND tp.display='Y' GROUP BY tp.ProductId  ORDER BY pidcnt DESC ";				
    		//echo $base_query;exit;
    		$base_query = $base_query . " LIMIT $count,10";
    		$result = $db->get_results( $base_query );
    		if( isset( $result ) && !empty( $result ) )
    		{
    			$res = array( 'message_code' => 1000, 'data_text' => $result );
    		}
    		else
    		{
    			return $response->withJson( array( 'message_code' => 999, 'message_text' => 'Products not found for the category. Please try with other categories.' ) );
    		}
    	}
    	return $response->withJson( $res, 200 );	
    }

    /*
   	* product lazy loading on subcategory
   	*/ 
   	function tbx_product_details_lazyloading_subcategory(Request $request, Response $response)
	{
		$db = database();
		$SubCatid = $request->getAttribute('SubcatId'); //CategoryId

		$body = $request->getParsedBody();
		$count = $body['Count'];
		if(isset($count) &&  !empty($count))
		{
			$count = $count;
		}
		else
		{
			$count = 0;
		}

		/*if (($count == null) || ($count==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Please send count.');*/		
		/*else
		{*/			
			$base_query = " SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId,tpsc.SubCategoryName, tp.Rate FROM `tbl_product` AS tp 
							LEFT JOIN `tbl_product_category` AS tpc ON tpc.CategoryId = tp.CategoryId
							LEFT JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId
							WHERE tp.Rate NOT IN (0.00) AND tp.SubCategoryId = ".$SubCatid." AND tp.display='Y' ORDER BY tp.ProductId DESC ";

			$base_query = $base_query . " LIMIT $count,10"; //'".$count."'
			$result = $db->get_results( $base_query );
			if( isset( $result ) && !empty( $result ) )
			{
				$res = array( 'message_code' => 1000, 'data_text' => $result );
			}
			else
			{
				return $response->withJson( array( 'message_code' => 999, 'message_text' => 'Products not found for the Sub category. Please try with other Sub categories.' ) );
			}
		//}
		
		return $response->withJson( $res, 200 );	
	} 




function tbx_product_json(Request $request, Response $response)
{
	$db = database();
	$RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");

	$sSQL = "SELECT tp.ProductId, tp.ProductImage, tp.ProductName, tp.ProductDetails, tp.CategoryId, tp.SubCategoryId, tpc.CategoryName,tpc.CategoryId, tpsc.SubCategoryName, tp.Rate, manufacturer, model, sku, AssembledWeight, ProductURL FROM `tbl_product` AS tp JOIN `tbl_product_category` As tpc On tpc.CategoryId = tp.CategoryId JOIN `tbl_product_sub_category` AS tpsc ON tpsc.SubCategoryId = tp.SubCategoryId WHERE tp.Rate != 0.00 AND tp.display='Y' order by tp.CategoryId, tp.SubcategoryId, tp.ProductId";
	
	//echo $sSQL . "<br/>";		
	$result = $db->get_results( $sSQL );
	$file = fopen( dirname( __DIR__ ) . '/product.json','w+');
	//$file = fopen( '/var/www/html/toolbx/public/api/product.json','w+');

	$flag = 0;
	foreach($result as $product)
	{	
		$product->ProductDetails = htmlspecialchars($product->ProductDetails);
		$product->ProductName = htmlspecialchars($product->ProductName);

		$product->Rate =  round($product->Rate + (($product->Rate * $RateThresold) /100),2) . "";

		$product->Rate = number_format( $product->Rate, 2, '.', '' );
		
		$jsonproduct = json_encode($product);
		if (isset($jsonproduct) && !empty($jsonproduct))
		{
			if ($flag == 0)
				fwrite($file, "[" . $jsonproduct);
			else
				fwrite($file,  "," . $jsonproduct);
			$flag = 1;
		}

	}
	fwrite($file,  "]");
	fclose($file);

	//echo $file;
	$db->query("UPDATE tbl_SystemParameters SET ParameterValue=UNIX_TIMESTAMP(NOW()) WHERE ParameterName='PRODUCTUPDATED'");

	//print_r($result);
	if( isset( $result ) && !empty( $result ) )
	{
		$res = array('message_code' => 1000, 'message_text' => 'Products updated.' );
	}
	else
		$res = array('message_code' => 999, 'message_text' => 'No products found.');
	
	return $response->withJson( $res, 200 );	
}


function tbx_product_jsonupdated(Request $request, Response $response)
{
	$db = database();

	$body = $request->getParsedBody();
    $LastUpdatedDate = $body['LastUpdatedDate'];

    //echo "LastUpdatedDate:" . $LastUpdatedDate . "<br/>";
		
    if (isset( $LastUpdatedDate ) && !empty( $LastUpdatedDate ) && ($LastUpdatedDate!="" ))
    {	
		$sSQL = "SELECT ParameterValue from tbl_SystemParameters WHERE ParameterName='PRODUCTUPDATED'";
		
		$ParameterValue = $db->get_var( $sSQL );

		//echo "ParameterValue" . $ParameterValue . "<br/>";

		if( $LastUpdatedDate < $ParameterValue)
			$res = array('message_code' => 1000, 'message_text' => 'Products are updated.' );
		else
			$res = array('message_code' => 999, 'message_text' => 'No products update.');
	}
	else
		$res = array('message_code' => 1000, 'message_text' => 'Products are updated.' );

	return $response->withJson( $res, 200 );	
}
   

function tbx_product_priceThresold(Request $request, Response $response)
{
	$db = database();
	
	$ParameterValue = 10;
	$res = array('message_code' => 1000, 'message_text' => $ParameterValue );
	
	$sSQL = "SELECT ParameterValue from tbl_SystemParameters WHERE ParameterName='PRICETHRESOLD'";
	
	$ParameterValue = $db->get_var( $sSQL );
	$res = array('message_code' => 1000, 'message_text' => $ParameterValue );

	return $response->withJson( $res, 200 );	
} 
    
   


