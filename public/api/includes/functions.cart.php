<?php
 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

	/*
	* Count Itam
	*/

	function tbx_cart_item_count(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$registrationid = $body['RegistrationId']; //order placed by owner and employee then get ownerid
		
	 	$base_query = ' SELECT COUNT(*) AS count FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" ';
		$result = $db->get_row($base_query);
		if(!$result )
		{
		    
			$res = array( 'message_code' => 999, 'message_text' => 'Database Error! please try again.');
		}
		else
		{
		    /*$basequery = $db->get_row(' SELECT MAX(OrderId) AS maxorderid FROM tbl_order WHERE RegistrationId= "'.$registrationid.'" ');
		    $result1 = $db->get_row('    SELECT COUNT(tod.OrderDetailId)AS TotalReacentPro FROM `tbl_order_details` AS tod
	                                            INNER JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId
	                                            WHERE tod.OrderId="'.$basequery->maxorderid.'" ORDER BY tod.OrderDetailId DESC ');*/
	                                            
	        $result1 = $db->get_row('    SELECT COUNT(DISTINCT(tod.productid)) AS TotalReacentPro FROM `tbl_order_details` AS tod
	                                            INNER JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId
	                                            WHERE tbo.registrationid="'.$registrationid.'" ');
			
			$res = array( 'message_code' => 1000,'data_text'=>$result,'RecentPurchase'=>$result1);
		} 	
		return $response->withJson( $res, 200 );
	}

	/*
	* Show Cart
	*/

	function tbx_cart_show(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();

		$registrationid = $body['RegistrationId'];
	 	
	 	$base_query = ' SELECT Quantity FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" ';
		$result = $db->get_row($base_query);
		if($result->Quantity == 0)
		{
		 	$base_query = $db->query(' DELETE FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" ');
			if(!$base_query )
			{		
				$res = array( 'message_code' => 999, 'message_text' => 'Cart is empty!.');
			}
			else
			{
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
		}
		else
		{
			$base_query = ' SELECT tp.ProductId,tp.ProductName, tp.ProductImage,tc.Quantity, tp.Rate, (tp.Rate * tc.Quantity) as TotalAmount FROM `tbl_product` AS tp , `tbl_cart` AS tc WHERE  tc.ProductId = tp.ProductId and tc.RegistrationId= "'.$registrationid.'" ';
			$result = $db->get_results($base_query);
			if(!$result )
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Cart is empty!.');
			}
			else
			{
				$res = array( 'message_code' => 1000,'data_text'=>$result);
			}
		}
		return $response->withJson( $res, 200 );
	}

	/*
	* cart itam edit (Add or delete)
	*/

    function tbx_cart_edititam(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$registrationid = $body['RegistrationId'];
		$productid = $body['ProductId'];
		$action = $body['Action'];
		
	    if (($registrationid == null) || ($registrationid==""))
			$res = array( 'message_code' => 999, 'message_text' => 'User id cannot be blank.');
		else if (($productid == null) || ($productid==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Product id cannot be blank.');
		else if (($action == null) || ($action==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Please Click on plus or minus sign.');
		else
		{
			$base_query = 'SELECT COUNT(*) AS count, SUM(Quantity) as Quantity FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" GROUP BY ProductId';
			$result = $db->get_row($base_query);
			if (!isset($result))
			{
				if ($action == "+")
				{
					$result = ' INSERT INTO `tbl_cart` (RegistrationId, ProductId, Quantity) VALUES("'.$registrationid.'","'.$productid.'" , "1") ';
					$base = $db->query($result);
					$res = array( 'message_code' => 1000,'data_text'=>$base);
				}
			}	
			else if($result->count == 0 && $action == "+")
			{
				$result = ' INSERT INTO `tbl_cart` (RegistrationId, ProductId, Quantity) VALUES("'.$registrationid.'","'.$productid.'" , "1") ';
				$base = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base);
			}
			elseif($result->count == 0 && $action == "-")
			{
				
				$res = array( 'message_code' => 1000,'message_text'=>"Product quantity is zero.");
			}
			elseif($result->Quantity == 1 && $action == "-")
			{
				$result = 'DELETE FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			elseif($result->count == 1 && $action == "+")
			{
				$result = 'UPDATE `tbl_cart` SET Quantity = "'. ($result->Quantity + 1) .'"  WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			elseif($result->count == 1 && $action == "-")
			{
				$result = 'UPDATE `tbl_cart` SET Quantity = "'. ($result->Quantity - 1) .'" WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			else
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Somthing went wrong!. please try again.' );
			}
			
			$result = ' SELECT tp.ProductId,tp.ProductName, tp.ProductImage,tc.Quantity, tp.Rate, (tp.Rate * tc.Quantity) as TotalAmount FROM `tbl_product` AS tp , `tbl_cart` AS tc WHERE  tc.ProductId = tp.ProductId and tc.ProductId = "'.$productid.'" AND tc.RegistrationId= "'.$registrationid.'" ';
			$result = $db->get_results($result);
			if (!isset($result))
				$res = array( 'message_code' => 1000,'message_text'=>"Product quantity is zero.");
			else
				$res = array( 'message_code' => 1000,'data_text'=>$result);
	    }
		return $response->withJson( $res, 200 );
	}


	/*old function*/
	/*function tbx_cart_edititam(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$registrationid = $body['RegistrationId'];
		$productid = $body['ProductId'];
		$action = $body['Action'];
		
	    if (($registrationid == null) || ($registrationid==""))
			$res = array( 'message_code' => 999, 'message_text' => 'User id cannot be blank.');
		else if (($productid == null) || ($productid==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Product id cannot be blank.');
		else if (($action == null) || ($action==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Please Click on plus or minus sign.');
		else
		{
			$base_query = ' SELECT COUNT(*) AS count, Quantity FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
			$result = $db->get_row($base_query);
			if($result->count == 0 && $action == "+")
			{
				$result = ' INSERT INTO `tbl_cart` (RegistrationId, ProductId, Quantity) VALUES("'.$registrationid.'","'.$productid.'" , "1") ';
				$base = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base);
			}
			elseif($result->Quantity <= 1 && $action == "-")
			{
				$result = ' DELETE FROM `tbl_cart` WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			elseif($result->count == 1 && $action == "+")
			{
				$result = 'UPDATE `tbl_cart` SET Quantity = "'. ($result->Quantity + 1) .'"  WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			elseif($result->count == 1 && $action == "-")
			{
				$result = 'UPDATE `tbl_cart` SET Quantity = "'. ($result->Quantity - 1) .'" WHERE RegistrationId = "'.$registrationid.'" AND ProductId= "'.$productid.'" ';
				$base_query = $db->query($result);
				$res = array( 'message_code' => 1000,'data_text'=>$base_query);
			}
			else
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Somthing went wrong!. please try again.' );
			}
			$result = ' SELECT tp.ProductId,tp.ProductName, tp.ProductImage,tc.Quantity, tp.Rate, (tp.Rate * tc.Quantity) as TotalAmount FROM `tbl_product` AS tp , `tbl_cart` AS tc WHERE  tc.ProductId = tp.ProductId and tc.ProductId = "'.$productid.'" AND tc.RegistrationId= "'.$registrationid.'" ';
			$base_query = $db->get_results($result);
			$res = array( 'message_code' => 1000,'data_text'=>$base_query);
	    }
		return $response->withJson( $res, 200 );
	}*/


/*
* cart item inser on placing order
*/
function tbx_cartitem(Request $request, Response $response)
{
	 $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	 $db = database();
	 $body = $request->getParsedBody();
	 
	 $user_id = $body['user_id'];
	 $product_id_s = $body['product_id'];
	 $product_qty_s = $body['product_qty'];
	
	 $product_id=explode(",",$product_id_s);
	 $product_qty=explode(",",$product_qty_s);
	
	 $count=count($product_id);
	 for($i=0;$i<$count;$i++)
	 {
	       $base_query = ' INSERT INTO tbl_cart (RegistrationId, ProductId,Quantity) VALUES( "'. $user_id .'","'. $product_id[$i] .'","'. $product_qty[$i] .'") ';
	       $result = $db->query( $base_query );
	 }
	 if($result)
	 {
	      $res = array( 'message_code' => 1000, 'message_text' => 'Product add successfully.' );
	 }
	 else
	 {
	   $res = array( 'message_code' => 999, 'message_text' => 'Unable to add product,Please try again');
	 }
 return $response->withJson( $res, 200 );
}

function tbx_cartclear(Request $request, Response $response)
{
	 $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	 $db = database();
	 $RegistrationId = $request->getAttribute("id");

	 $result = $db->query('DELETE FROM tbl_cart WHERE RegistrationId =' . $RegistrationId);
	  
	 
	 $res = array( 'message_code' => 1000, 'message_text' => 'Cartitems are deleted.');
	 
	 return $response->withJson( $res, 200 );

}

























