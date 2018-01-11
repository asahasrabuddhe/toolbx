<?php
 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/*
* Runner Order List
*/
function tbx_runner_order_list( Request $request, Response $response )
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $db = database();
    
    $base_query =  '  SELECT tbo.OrderId, tbo.OrderDate, (tbo.TotalAmount - tbo.TaxAmount - tbo.DeliveryCharges) as TotalAmount, tj.Address, tj.PostalCode, tj.JobSiteName , tc.CompanyName, tbo.Notes FROM `tbl_order` AS tbo 
                    INNER JOIN `tbl_companies` AS tc ON tc.CompanyId = tbo.CompanyId 
                    INNER JOIN `tbl_jobsite` AS tj ON tj.jobsiteid= tbo.jobsiteid 
                    LEFT JOIN `tbl_registration` AS tr ON tbo.RegistrationId = tr.RegistrationId WHERE tbo.Delivered=\'N\' AND tbo.IsAccepted=\'N\' AND tbo.display=\'Y\' AND tbo.IsCancel= \'N\' ORDER BY tbo.OrderId ASC';
    $result = $db->get_results( $base_query );
    
    if( isset( $result ) && !empty( $result ) )
    {
        foreach ($result as $var)
        {
            $base_query = ' SELECT sum(tod.Amount) FROM `tbl_order_details` AS tod 
                    LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId
                    LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId
                    INNER JOIN `tbl_jobsite` AS tj ON tj.jobsiteid= tbo.jobsiteid 
                    LEFT JOIN `tbl_registration` AS tr ON tbo.RegistrationId = tr.RegistrationId
                    WHERE tbo.OrderId="' .$var->OrderId . '" AND tod.display=\'Y\' ';  
            $var->TotalAmount = $db->get_var($base_query);
        }
        $res = array( 'message_code' => 1000, 'data_text' => $result );
    }
    else
    {
        return $response->withJson( array( 'message_code' => 999 ,'message_text' => 'Order not found.') );
    }
    return $response->withJson( $res, 200 );
}
function tbx_runner_order_details(Request $request, Response $response)
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $order_id = $request->getAttribute('id');
    $res = tbx_runner_owner_order_details($order_id, 2);
    return $response->withJson( $res, 200 );
}
function tbx_owner_order_details(Request $request, Response $response)
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $order_id = $request->getAttribute('id');
    $res = tbx_runner_owner_order_details($order_id, 1);
    return $response->withJson( $res, 200 );
}
/*
* order details for runner
tbx_order_details 
*/
function tbx_runner_owner_order_details($order_id, $source)
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $db = database();
                    
    $base_query = ' SELECT tbo.RegistrationId,tod.OrderDetailId, tod.Quantity, tod.Amount, tp.ProductName, tp.ProductDetails, tp.Rate, tp.ProductImage, tbo.JobSiteId, tj.JobSiteName, tj.Address, tj.PostalCode, tbo.Notes,  tr.RegistrationPhoneNo, tp.manufacturer, tp.model, tp.sku, tod.Available , tod.ProductId FROM `tbl_order_details` AS tod 
                    LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId
                    LEFT JOIN `tbl_order` AS tbo ON tbo.OrderId = tod.OrderId
                    INNER JOIN `tbl_jobsite` AS tj ON tj.jobsiteid= tbo.jobsiteid 
                    LEFT JOIN `tbl_registration` AS tr ON tbo.RegistrationId = tr.RegistrationId
                    WHERE tbo.OrderId="' . $order_id . '" AND tod.display=\'Y\' ';  
        if ($source == 1)
                $base_query = $base_query . " and (Available = 1 or Available = 0)";
                    
    $result = $db->get_results( $base_query );
    if( isset( $result ) && !empty( $result ) )
    {
         $RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");
        foreach ($result as $var)
        {
            $var->ProductDetails = htmlspecialchars($var->ProductDetails);
            $var->ProductName = htmlspecialchars($var->ProductName);
            if ($source == 1)
            {
                $var->Amount =  round($var->Amount + (($var->Amount * $RateThresold) /100),2) . "";
                $var->Amount = number_format( $var->Amount, 2, '.', '' );
                $var->Rate =  round($var->Rate + (($var->Rate * $RateThresold) /100),2) . "";
                $var->Rate = number_format( $var->Rate, 2, '.', '' );
            }
        }
        $res = array( "message_code" => 1000, "data_text" => $result );
    }
    else
    {
        $res = array( 'message_code' => 999 ,'message_text' => 'Order not found.') ;
    }
    return $res;
}
/*
* order update item for runner
*/
// function tbx_runner_update_item( Request $request, Response $response )
// {
//     $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
//     $db = database();
//     $order_id = $request->getAttribute('id');
//     $body = $request->getParsedBody();
//     $productId = $body['productId'];
//     $productQty = $body['productQty'];
//     $productAvailable = $body['productAvailable']; 
//     if (($productId == null) || ($productId==""))
//         $res = array( 'message_code' => 999, 'message_text' => 'Please provide the productId.');
//     else if (($productQty == null) || ($productQty==""))
//         $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product quantity');
//     else if (($productAvailable == null) || ($productAvailable==""))
//         $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product availibility.');
//     else             
//     {    
//         $sSQL = "UPDATE tbl_order_details SET OriginalQuantity = Quantity WHERE OrderId=" . $order_id . " AND ProductId=" . $productId; 
        
//         $db->query( $sSQL );
//         $sSQL = "UPDATE tbl_order_details SET Available=" . $productAvailable . ", Quantity=" . $productQty . ", Amount = (Rate*". $productQty . ") WHERE OrderId=" . $order_id . " AND ProductId=" . $productId; 
//         //echo $sSQL;
        
//         if ($db->query( $sSQL ))
//             $res = array( "message_code" => 1000, "data_text" => "Product updated successfully." );
//         else
//             $res = array( "message_code" => 999, "data_text" => "Unable to update product." );
//     }
//     return $response->withJson( $res, 200 );
// }
function tbx_runner_update_item( Request $request, Response $response )
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $db = database();
    $order_id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $productId = $body['productId'];
    $productQty = $body['productQty'];
    $productAvailable = $body['productAvailable']; 
    if (($productId == null) || ($productId==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the productId.');
    else if (($productQty == null) || ($productQty==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product quantity');
    else if (($productAvailable == null) || ($productAvailable==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product availibility.');
    else             
    {  
        $sSQL = "SELECT count(*) as cnt FROM tbl_order_details WHERE OrderId=" . $order_id . " AND ProductId=" . $productId;
        $cnt = $db->get_var( $sSQL );
       // $RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");
        if ($cnt==1)
        {
            $sSQL = "UPDATE tbl_order_details SET OriginalQuantity = Quantity WHERE OrderId=" . $order_id . " AND ProductId=" . $productId; 
        
            $db->query( $sSQL );
            $sSQL = "UPDATE tbl_order_details SET Available=" . $productAvailable . ", Quantity=" . $productQty . ", Amount = ( Rate * ". $productQty . ") WHERE OrderId=" . $order_id . " AND ProductId=" . $productId; 
            // echo $sSQL;exit();
            
            if (false !== $db->query( $sSQL ))
                $order_details = $db->get_results('SELECT * FROM tbl_order_details WHERE Available <> -1 AND  OrderId = ' . $order_id);
                $subtotal = 0;
                foreach($order_details as $detail) {
                    $subtotal += ($detail->Amount + ($detail->Amount * 0.1));
                }
                $order = $db->get_row('SELECT DeliveryCharges FROM tbl_order WHERE OrderId = ' . $order_id);
                $taxes = ($subtotal + $order->DeliveryCharges) * 0.13;

                $updateOrder = $db->query('UPDATE tbl_order SET TaxAmount = ' . $taxes . ' WHERE OrderId = ' . $order_id);
                if(false !== $updateOrder) {
                    $res = array( "message_code" => 1000, "data_text" => "Product updated successfully." );
                }
            else
                $res = array( "message_code" => 999, "data_text" => "Unable to update product." );
        }
        else
        {
            $query = "SELECT Rate FROM tbl_product WHERE ProductId=" .$productId;
            $rate = $db->get_var($query);
            //print_r($rate);exit();
            if($rate > 0)
            {
                //$rate =  round($rate + (($rate * $RateThresold) /100),2) . "";
                //$rate = number_format( $rate, 2, '.', '' );
                $sSQL = 'INSERT INTO `tbl_order_details`(DeletedBy,DeletedOn,CreatedBy,CreatedOn,LastModifiedBy,LastModifiedOn,display,ProductId,OrderId,Quantity,Rate,Amount,Delivered,Available,OriginalQuantity) VALUES(NULL,NULL,NULL,now(),NULL,now(),"Y",'.$productId.','.$order_id.','.$productQty.', '.$rate.','.$rate * $productQty.', "N","' . $productAvailable . '",0 ) ';
                
                if (false !== $db->query( $sSQL ))
                    $order_details = $db->get_results('SELECT * FROM tbl_order_details WHERE Available <> -1 AND OrderId = ' . $order_id);
                    $subtotal = 0;
                    foreach($order_details as $detail) {
                        $subtotal += ($detail->Amount + ($detail->Amount * 0.1));
                    }
                    $order = $db->get_row('SELECT DeliveryCharges FROM tbl_order WHERE OrderId = ' . $order_id);
                    $taxes = ($subtotal + $order->DeliveryCharges) * 0.13;

                    $updateOrder = $db->query('UPDATE tbl_order SET TaxAmount = ' . $taxes . ' WHERE OrderId = ' . $order_id);
                    if(false !== $updateOrder) {
                        $res = array( "message_code" => 1000, "data_text" => "Product added successfully." );
                    }
                else
                    $res = array( "message_code" => 999, "data_text" => "Unable to add product." );
            }
            else
            {
                $res = array( "message_code" => 999, "data_text" => "You are trying to insert invalid product." );
            }
        }   
    }
    return $response->withJson( $res, 200 );
}
function tbx_runner_insert_item( Request $request, Response $response )
{
    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    $db = database();
    $order_id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $manufacturer = $body['Manufacturer'];
    $productName = $body['productName'];
    $productQty = $body['productQty'];
    $sku = $body['SKU'];
    $productRate = $body['productRate'];
    $productAvailable = $body['productAvailable']; 
    if (($manufacturer == null) || ($manufacturer==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product manufacturer');
    else if (($productName == null) || ($productName==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product name');
    else if (($productQty == null) || ($productQty==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product quantity');
    else if (($sku == null) || ($sku==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product SKU');
    else if (($productRate == null) || ($productRate==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product rate.');
    else if (($productAvailable == null) || ($productAvailable==""))
        $res = array( 'message_code' => 999, 'message_text' => 'Please provide the product availibility.');
    else
    {
        $sSQL = 'INSERT INTO `tbl_product`(CategoryId, SubCategoryId, ProductName, ProductDetails, ProductImage, Rate, model, LastModifiedOn, manufacturer, sku, CreatedBy, CreatedOn, privateYN) VALUES (NULL,NULL,"' . $productName . '", NULL, NULL,' . $productRate . ', NULL, now(),"' . $manufacturer . '","' . $sku . '", 1, now(), "Y")';
        //echo $sSQL; die();
        $base_query = $db->query($sSQL);
        $productId = $db->insert_id;
        //print_r($product_ID);exit();
        if(!$base_query )
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Oops! Something went wrong please try again.','data_text'=>$base_query);
        }
        else
        {  
          $sSQL = 'INSERT INTO `tbl_order_details`(DeletedBy, DeletedOn, CreatedBy, CreatedOn, LastModifiedBy, LastModifiedOn, display, ProductId, OrderId, Quantity, Rate, Amount, Delivered, Available, OriginalQuantity) VALUES(NULL,NULL,NULL, now(),NULL, now(), "Y", "' . $productId.  '",' . $order_id . ',' . $productQty . ', ' . $productRate . ',' . $productRate * $productQty . ', "N",' . $productAvailable . ',' . $productQty . ' ) ';
        
            if (false !== $db->query( $sSQL )) {
                $order_details = $db->get_results('SELECT * FROM tbl_order_details WHERE Available <> -1 AND  OrderId = ' . $order_id);
                $subtotal = 0;
                foreach($order_details as $detail) {
                    $subtotal += ($detail->Amount + ($detail->Amount * 0.1));
                }
                $order = $db->get_row('SELECT DeliveryCharges FROM tbl_order WHERE OrderId = ' . $order_id);
                $taxes = ($subtotal + $order->DeliveryCharges) * 0.13;

                $updateOrder = $db->query('UPDATE tbl_order SET TaxAmount = ' . $taxes . ' WHERE OrderId = ' . $order_id);
                if(false !== $updateOrder) {
                    $res = array( "message_code" => 1000, "data_text" => "Product added successfully.", 'debug' => ['st' => $subtotal, 't' => $taxes] );
                }
            }
            else {
                $res = array( "message_code" => 999, "data_text" => "Unable to add product." );   
            }
        }
    }
    return $response->withJson( $res, 200 );
}
    /***************** if user(runner)not deleted from databse then accept, cancel, leaving, delivered  ***********************/
    
    /*
    * Accept order (Error)
    */
    function tbx_runner_accept_order( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $order_id = $request->getAttribute('id'); //orderid
        $body = $request->getParsedBody();
        $runnerid= $body['RegistrationId'];
        $AcceptOrderDate = date('Y-m-d H:i:s');
        $CreatedOn = date('Y-m-d H:i:s');
        $lastmodofiedOn = date('Y-m-d H:i:s');
        $resultque = $db->get_row('SELECT IsDeleted FROM tbl_registration WHERE RegistrationId = "' . $runnerid . '" ');
        if(!$resultque)
        {
            $res = array( 'message_code' => 999, 'message_text' => 'These user not register with ToolBX, please contact with admin, or try later.');
        }
        else if($resultque->IsDeleted == "N")
        {
            $base_query = $db->get_row('SELECT IsAccepted FROM `tbl_order` WHERE display= \'Y\' AND IsAccepted=\'Y\' AND OrderId =' . $order_id);
            if($base_query )
            {
                $res = array( 'message_code' => 999, 'message_text' => 'This order is no longer available.');
            }
            else
            {
                /***/
                $query = ' SELECT IsCancel FROM tbl_order WHERE OrderId = "'. $order_id . '"';
                $result = $db->get_row($query);
                if($result->IsCancel == "N")
                {
                /***/
                    $base_query = ' INSERT INTO `tbl_runner_order`(OrderId,RunnerId,AcceptOrderDate,CreatedOn) VALUES("'.$order_id.'","'.$runnerid.'","'.$AcceptOrderDate.'", "'.$CreatedOn.'") ';
                    $result = $db->query($base_query);
                    if( $result)
                    {
                        $base_query = ' UPDATE `tbl_order` SET LastModifiedOn="'. $lastmodofiedOn .'", IsAccepted=\'Y\' WHERE `OrderId` = "'.$order_id.'" ';
                        $result = $db->query($base_query);
                        if($result == FALSE)
                            $res = array( 'message_code' => 999, 'message_text' => 'Error While assigning order.');
                        else if($result == 0)
                            $res = array( 'message_code' => 1000, 'message_text' => 'Order Assigned.');
                        else
                             $res = array( 'message_code' => 1000, 'message_text' => 'Order Assigned.');  
                    }
                    else
                    {
                        $res = array( 'message_code' => 999, 'message_text' => 'Error While assigning order.');
                    }
                /***/
                }
                else
                { 
                    $res = array( 'message_code' => 901, 'message_text' => 'This order has been cancelled.'); 
                }
                /***/
            }
        }
        else
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Your account has been deleted from ToolBX , please contact with admin.');
        }
        return $response->withJson( $res, 200 );
    }
    /*
    * Cancel Order
    */
    
    function tbx_runner_cancel_order( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
        $db = database();
        $order_id = $request->getAttribute('id');
        $body = $request->getParsedBody();
        $runnerid = $body['RegistrationId'];
        $canceldate = date('Y-m-d H:i:s');
        $LastModifiedOn = date('Y-m-d H:i:s');
        $resultque = $db->get_row('SELECT IsDeleted FROM tbl_registration WHERE RegistrationId = "' . $runnerid . '" ');
        if(!$resultque)
        {
            $res = array( 'message_code' => 999, 'message_text' => 'These user not register with ToolBX, please contact with admin, or try later.');
        }
        else if($resultque->IsDeleted == "N")
        {
            $base_query = $db->get_row('SELECT MAX(RunnerOrderId) as ROI FROM `tbl_runner_order` WHERE OrderId="'.$order_id.'" AND RunnerId= "'.$runnerid.'" AND display=\'Y\' ');
            if (!$base_query) 
            {
                $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            }
            else
            {
                $maxroi = $base_query->ROI;
                
                $query = ' SELECT Count(*) FROM `tbl_order` as tbo WHERE tbo.OrderId ="'. $order_id . '"';
                $cnt = $db->get_var($query);
                if ($cnt == 1)
                {
                    /***/
                    $query = ' SELECT IsCancel FROM tbl_order WHERE OrderId = "'. $order_id . '"';
                    $result = $db->get_row($query);
                    if($result->IsCancel == "N")
                    {
                    /***/
                        $query =' UPDATE `tbl_order` SET IsAccepted =\'N\', IsLeaving =\'N\' WHERE OrderId ='. $order_id;
                        $data = $db->query( $query );
                        
                        $query = ' SELECT Count(*) FROM `tbl_runner_order` as tro WHERE tro.RunnerOrderId="'.$maxroi.'" AND tro.RunnerId="'.$runnerid.'" ';
                        $cnt = $db->get_var($query);
                        if($cnt == 1)
                        {
                            $query='UPDATE `tbl_runner_order` SET IsAccepted = \'N\', CancelDate= "' . $canceldate . '", LastModifiedOn="'.$LastModifiedOn.'" WHERE RunnerOrderId = "' . $maxroi . '" AND RunnerId="' . $runnerid . '" ';
                            $data = $db->query( $query );
        
                            $sSQL = "UPDATE tbl_order_details SET Quantity = OriginalQuantity, Amount = (Rate*OriginalQuantity) WHERE OrderId=" . $order_id . " AND OriginalQuantity!=0"; 
                            $db->query( $sSQL );
                            $sSQL = "UPDATE tbl_order_details SET Available =0 WHERE OrderId=" . $order_id;
                            $db->query( $sSQL );
                            $base_query = $db->get_row('SELECT RegistrationName, RegistrationEmail FROM tbl_registration WHERE RegistrationId=' . $runnerid);
                            $name = $base_query->RegistrationName;
                            $email = $base_query->RegistrationEmail;
                            send_order_cancel_email($name, $email, $order_id);
                            $res = array( 'message_code' => 1000, 'message_text' => ' Order Cancelled successfully.');
                        }
                        else
                            $res = array( 'message_code' => 999, 'message_text' => 'Order id not found.');
                    /***/
                    }
                     else
                    { 
                        $res = array( 'message_code' => 901, 'message_text' => 'This order has cancelled already!.'); 
                    }
                    /***/
                }
                else
                    $res = array( 'message_code' => 999, 'message_text' => 'Order id not found.');
                
            }
        }
        else
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Your account has been deleted from ToolBX , please contact with admin.');
        }
        return $response->withJson( $res, 200 );
    }
    /*
    * Leaving store
    */
    function tbx_runner_leaving_store( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $order_id = $request->getAttribute('id');
        $resultque = $db->get_row('SELECT tr.IsDeleted FROM tbl_order AS tbo LEFT JOIN tbl_registration AS tr ON tbo.registrationid = tr.registrationid WHERE orderid = "' . $order_id . '" ');
        if(!$resultque)
        {
            $res = array( 'message_code' => 999, 'message_text' => 'These user not register with ToolBX, please contact with admin, or try later.');
        }
        else if($resultque->IsDeleted == "N")
        {
            /***/
            $query = ' SELECT IsCancel FROM tbl_order WHERE OrderId = "'. $order_id . '"';
            $result = $db->get_row($query);
            if($result->IsCancel == "N")
            {
            /***/
                $base_query = ' UPDATE `tbl_order` SET IsLeaving=\'Y\' WHERE `OrderId` = "'.$order_id.'"';
                $data = $db->query( $base_query );
                
                if ($data === FALSE) 
                {
                    $res = array( 'message_code' => 999, 'message_text' => 'Please Try again.');
                }
                else if ($data == 0)
                {
                    $res = array( 'message_code' => 1000, 'message_text' => 'Your delivery has left the store and it’s on its way.');
                }
                else
                {   
                    $res = array( 'message_code' => 1000, 'message_text' => 'Your delivery has left the store and it’s on its way.');
                }
            /***/
            }
            else
            { 
                $res = array( 'message_code' => 901, 'message_text' => 'This order has cancelled!.'); 
            }
            /***/
        }
        else
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Your account has been deleted from ToolBX , please contact with admin.');
        }
        
        return $response->withJson( $res, 200 );
    }
    /*
    * Deliivered Order
    */
    function tbx_runner_deliverorder( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $order_id = $request->getAttribute('id');
        $body = $request->getParsedBody();
        $runnerid = $body['RegistrationId'];
        $delivredON = date('Y-m-d H:i:s');
        $resultque = $db->get_row('SELECT IsDeleted FROM tbl_registration WHERE RegistrationId = "' . $runnerid . '" ');
        if(!$resultque)
        {
            $res = array( 'message_code' => 999, 'message_text' => 'These user not register with ToolBX, please contact with admin, or try later.');
        }
        else if($resultque->IsDeleted == "N")
        {
            $base_query = $db->get_row('SELECT MAX(RunnerOrderId) as ROI FROM `tbl_runner_order` WHERE OrderId="'.$order_id.'" AND RunnerId= "'.$runnerid.'" AND display=\'Y\' ');
            if(!$base_query)
                $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            else
                $maxroi = $base_query->ROI;
                  // order id is unique
                $base_query = 'SELECT IsCancel FROM `tbl_order` WHERE OrderId= '.$order_id.' ';                
                $iscancel = $db->get_row($base_query);
                
                if($iscancel->IsCancel === "N")
                {  
                
                    $base_query = ' UPDATE `tbl_order_details` tod, `tbl_order` tbo, `tbl_runner_order` tro SET tod.Delivered = \'Y\', tbo.Delivered=\'Y\' , tbo.DeliveredOn = "'.$delivredON.'" , tro.DeliveredOrderDate= "'.$delivredON.'" WHERE tod.OrderId = tbo.OrderId AND tro.RunnerOrderId="'.$maxroi.'"AND  tod.OrderId = "'.$order_id.'" ';
                    $data = $db->query( $base_query );
                    if($data == FALSE)
                    {
                        $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
                    }
                    else if($data == 0)
                    {
                        $base_query = $db->get_row('SELECT RegistrationId FROM tbl_order WHERE OrderId = ' . $order_id);
                        $result = $db->query(' DELETE FROM tbl_cart WHERE RegistrationId = ' . $base_query->RegistrationId);
                        
                        $res = array( 'message_code' => 1000, 'message_text' => ' Order delivered successfully.');
                    }
                    else
                    {
                        $base_query = $db->get_row('SELECT RegistrationId FROM tbl_order WHERE OrderId='. $order_id);
                        
                        $db->query(' DELETE FROM tbl_cart WHERE RegistrationId = ' . $base_query->RegistrationId);
                        
                        $res = array( 'message_code' => 1000, 'message_text' => ' Order delivered successfully.');
                    }
                }
                else
                {
                    $res = array( 'message_code' => 999, 'message_text' => 'These Order has cancelled, you can not be delivered.');
                }
        }
        else
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Your account has been deleted from ToolBX , please contact with admin.');
        }
     return $response->withJson( $res, 200 );
    }
    
    /****************************************/
    /*
    * Product order (At Order Placed from owner/employee) (mobileapp)
    */
    function tbx_order_create(Request $request, Response $response)
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $body = $request->getParsedBody();
        $companyid = $body['CompanyId'];
        $registrationid= $body['RegistrationId'];
        $jobsiteid =$body['JobSiteId'];
        $drivernotes =$body['Notes'];
        $taxamount = $body['TaxAmount'];
        $deliverycharges= $body['DeliveryCharges'];
        $totalamount= $body['TotalAmount'];
        $CreatedBy= $body['RegistrationId'];
        $orderdate = date('Y-m-d H:i:s');
        
        $CreatedOn = date('Y-m-d H:i:s');
        if (($companyid == null) || ($companyid==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Company id cannot be blank.');
        else if (($registrationid == null) || ($registrationid==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Registratin id cannot be blank.');
        else if (($jobsiteid == null) || ($jobsiteid==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Jobsite id cannot be blank.');
        else if (($drivernotes == null) || ($drivernotes==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Driver notes cannot be blank.');
        else if (($taxamount == null) || ($taxamount==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Tax amount cannot be blank.');
        else if (($deliverycharges == null) || ($deliverycharges==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Delivery charges cannot be blank.');
        else if (($totalamount == null) || ($totalamount==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Total amount cannot be blank.');
        else
        {
            $base_query = $db->get_row('SELECT COUNT(*) AS cartcount FROM `tbl_cart` WHERE RegistrationId=' . $registrationid);
            if(!$base_query )
            {
                $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            }
            elseif($base_query->cartcount >= 1)
            {
                
                $query = 'INSERT INTO `tbl_order`(CompanyId, RegistrationId, JobSiteId, TaxAmount, DeliveryCharges, TotalAmount, Notes, OrderDate, CreatedBy, CreatedOn) VALUES("'.$companyid.'","'.$registrationid.'","'.$jobsiteid.'","'.$taxamount.'","'.$deliverycharges.'", "'.$totalamount.'", "'.$drivernotes.'" ,"'.$orderdate.'", "'.$CreatedBy.'", "'. $CreatedOn .'")';
                $result = $db->query($query);
                if (!$result) 
                {
                    $res = array( 'message_code' => 999, 'message_text' => 'Your order failed please try again.');
                }
                else
                {
                    $order_id = $db->insert_id;
                    $result = 'INSERT INTO `tbl_order_details` (OrderId, ProductId, Quantity, Rate, Amount,CreatedOn)  SELECT tbo.OrderId, tc.ProductId, tc.Quantity, P.Rate as Rate, P.Rate * tc.Quantity as Amount, "'. $CreatedOn .'" FROM `tbl_cart`  tc ,`tbl_order` tbo, tbl_product as P WHERE P.ProductId = tc.ProductId and tc.RegistrationId = tbo.RegistrationId AND tbo.OrderId = ' . $order_id;
                    $base_query = $db->query($result);
                    if(!$base_query)
                    {               
                        $res = array( 'message_code' => 999, 'message_text' => 'Your product order failed please try again.');
                    }
                    else
                    {
                        $base_query = $db->get_row('SELECT RegistrationName, RegistrationEmail FROM tbl_registration WHERE RegistrationId=' . $registrationid);
                        $name = $base_query->RegistrationName;
                        $email = $base_query->RegistrationEmail;
                        send_order_email($name, $email, $order_id);
                        $res = array( 'message_code' => 1000,'message_text' => 'Order Placed Successfully.');
                    }       
                            
                }    
                
                $base_sql = $db->get_row(' SELECT MAX(OrderId) AS MaxOrderID FROM `tbl_order` WHERE Delivered=\'N\' ');
                $res = array( 'message_code' => 1000,'data_text'=> $base_sql);
            }
            else
                $res = array( 'message_code' => 999,'data_text'=>'You can not place order!. your cart is empty.');
        }
        return $response->withJson( $res, 200 );
    }
    /*
    * Current order id
    */
    //22/05/2017 
    function tbx_current_order_id(Request $request, Response $response)
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
        $db = database();
        $registration_id = $request->getAttribute('id');
        $body = $request->getParsedBody();
        $currentorderid = $body['OrderId'];
    
        if (($currentorderid == null) || ($currentorderid==""))
            $res = array( 'message_code' => 999, 'message_text' => 'order id cannot be blank.');
        else
        {
            $base_query = 'UPDATE `tbl_registration` SET CurrentOrderId = "'.$currentorderid.'" WHERE RegistrationId = "'.$registration_id.'" ';
            $data = $db->query( $base_query );
            if(!$data)
            {
                $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            }   
            else
            {       
                $res = array( 'message_code' => 1000, 'message_text' => ' Order id updated successfully.');
            }
        }
        return $response->withJson( $res, 200 );
    }
    
    /*
    * Order accepted Status (after 5 o clock)
    */
    
    function tbx_order_status(Request $request, Response $response)
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
        $db = database();
        $registration_id = $request->getAttribute('registrationid');
        $orderdate = date('Y-m-d');
        if (($registration_id == null) || ($registration_id==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Registration cannot be blank.');
        else
        {
            $base_queryres = 'SELECT MAX(OrderId) AS OrderId FROM `tbl_order` WHERE RegistrationId = "'.$registration_id.'"  AND DATE(OrderDate)= "'.$orderdate.'" ';
            $dataresult = $db->get_row( $base_queryres );
            
            $base_query = 'SELECT IsAccepted FROM `tbl_order` WHERE OrderId = "'.$dataresult->OrderId.'" ';
            //echo $base_query;exit;
            $data = $db->get_row( $base_query );
            if(!$data)
            {
                $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            }
            else
            {       
                $res = array( 'message_code' => 1000, 'data_text' => $data);
            }
        }
        return $response->withJson( $res, 200 );
    }
    /*
    * Owner and employee cancel order
    */
    
    //new 12/08/2017
    function tbx_owner_Employee_cancel_order( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
     
        $db = database();
        $userid = $request->getAttribute('RegId');
        $body = $request->getParsedBody();
        
        $canceldate = date('Y-m-d H:i:s');
        $resultque = $db->get_row('SELECT IsDeleted FROM tbl_registration WHERE RegistrationId = "' . $userid . '" ');
        if(!$resultque)
        {
            $res = array( 'message_code' => 999, 'message_text' => 'These user not register with ToolBX, please contact with admin, or try later.');
        }
        else if($resultque->IsDeleted == "N")
        {
            //max order id which is letest order (user only one order can be placed) and only last order should cancel
            $base_query = $db->get_row('SELECT MAX(OrderId) as OrdId FROM `tbl_order` WHERE RegistrationId= "'.$userid.'" '); //AND display=\'Y\' OR display=\'N\'
            if (!$base_query) 
            {
                $res = array( 'message_code' => 999, 'message_text' => 'Wrong order id.');
            }
            else
            { 
                $maxroi = $base_query->OrdId;
                $base_query = $db->get_row('SELECT Delivered FROM `tbl_order` WHERE OrderId= "'.$maxroi.'" '); 
                if($base_query->Delivered === "N")
                {   
                    // update flag to cancel order
                    $query = ' UPDATE `tbl_order` SET IsCancel = \'Y\' WHERE OrderId ='. $maxroi;
                    $data = $db->query( $query );
                    if($data == 1)
                    { 
                        //$base_query = ' SELECT RunnerId FROM tbl_runner_order AS tro WHERE tro.OrderId = "'. $maxroi .'" GROUP BY RunnerOrderId ';
                        $base_query = ' SELECT RunnerId FROM tbl_runner_order AS tro WHERE tro.OrderId = "'. $maxroi .'" AND tro.IsAccepted = \'Y\'  GROUP BY RunnerOrderId ';
                        $result = $db->get_row($base_query);
                        $base_query = $db->query(' UPDATE tbl_registration SET CurrentOrderId = 0 WHERE RegistrationId = "'. $result->RunnerId .'"  ');
                                 
                        if($result)
                        {   
                            $base_query = $db->get_row(' SELECT Token FROM tbl_registration WHERE RegistrationId = "'. $result->RunnerId .'" ');
                            $registration_ids = $base_query->Token;
                            $message = array(
                                                'title'=>'ToolBX',
                                                'image'=>'',
                                                'message'=>'This order has been cancelled.'
                                            );
                            
                            $data = send_notification($registration_ids, $message);
                            $res = array('message_code' => 1000, 'message_text' => $data);
                        }
                        $base_query = $db->get_row('SELECT tbl_registration.RegistrationName, tbl_registration.RegistrationEmail FROM tbl_registration JOIN tbl_order
                                ON tbl_order.RegistrationId = tbl_registration.RegistrationId WHERE tbl_order.OrderId=' . $maxroi );
                        $name = $base_query->RegistrationName;
                        $email = $base_query->RegistrationEmail;
                        send_order_cancel_email($name, $email, $maxroi);
                        $res = array('message_code' => 1000, 'message_text' => 'Order cancelled successfully');                                    
                    }
                    else if($data == 0)
                    {
                        $res = array('message_code' => 999, 'message_text' => 'These order already cancelled');
                    }
                    else
                        $res = array('message_code' => 999, 'message_text' => 'Please try later.');
                }
                else
                {
                    //$res = array('message_code' => 999, 'message_text' => 'These order has delivered you can not be cancel');
                    //$res = array('message_code' => 999, 'message_text' => 'This order has already been delivered. It cannot be cancelled now.');
                    
                    $res = array('message_code' => 999, 'message_text' => 'This order has already been delivered. It cannot be cancelled now.', 'data_text' => $base_query );
                }
            }
        }
        else
        {
            $res = array( 'message_code' => 999, 'message_text' => 'Your account has been deleted from ToolBX , please contact with admin.');
        }
        return $response->withJson( $res, 200 );
    }
    function tbx_owneremployee_order_history( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $body = $request->getParsedBody();
        $RegistrationId  = $request->getAttribute("id");
        $FromDate= $body['FromDate'];
        $ToDate= $body['ToDate'];
        $JobSiteId= $body['JobSiteId'];
        $EmployeeId = $body['EmployeeId'];
        $sSQL = "SELECT CompanyId, RegsitrationRoleId from tbl_registration WHERE RegistrationId=" . $RegistrationId;
        $regResult = $db->get_results($sSQL);
        if( isset( $regResult ) && !empty( $regResult ) )
        {
            if (($regResult[0]->RegsitrationRoleId != 4) && ($regResult[0]->RegsitrationRoleId != 2))
                $res = array( 'message_code' => 999, 'message_text' => 'Invalid registraion id. This id does not have permission to access orders.' );
            else
            {
                //CONCAT(JobSiteName,', ',Address) as JobSiteName
                $sSQL = "SELECT tor.OrderId, OrderDate, JobSiteName, '-' AS CompanyName, TotalAmount, (CASE WHEN Delivered = 'Y' THEN 'Delivered' WHEN IsCancel = 'Y' THEN 'Cancelled' WHEN IsLeaving='Y' THEN 'Out For Delivery' WHEN IsAccepted='Y' THEN 'Accepted' ELSE 'Pending' END) as STATUS, tr.RegistrationName FROM tbl_order AS tor, tbl_jobsite AS tj, tbl_registration as tr WHERE tr.RegistrationId = tor.CreatedBy and  tor.JobSiteId = tj.JobsiteId ";
                if ($regResult[0]->RegsitrationRoleId == 4)
                    $sSQL = $sSQL . " AND tor.CreatedBy =" . $RegistrationId;
                else if ($regResult[0]->RegsitrationRoleId == 2)
                    $sSQL = $sSQL . " AND tor.CompanyId =" . $regResult[0]->CompanyId;
                if( isset( $EmployeeId ) && !empty( $EmployeeId ) && ($EmployeeId!=0) )
                    $sSQL = $sSQL . " AND tor.CreatedBy =" . $EmployeeId;
                if( isset( $JobSiteId ) && !empty( $JobSiteId ) && ($JobSiteId!=0) )
                    $sSQL = $sSQL . " AND tor.JobSiteId =" . $JobSiteId;
                
                if( isset( $FromDate ) && !empty( $FromDate ) && isset( $ToDate ) && !empty( $ToDate ) )
                {
                    $date=date_create($ToDate);
                    date_add($date,date_interval_create_from_date_string("1 day"));
                    $sSQL = $sSQL . " AND OrderDate BETWEEN CAST('" . $FromDate . "' AS DATE) AND CAST('" . date_format($date,"Y-m-d") . "' AS DATE)";
                }
                $sSQL = $sSQL . " ORDER BY OrderDate DESC";
                //echo $sSQL . "<br/>";
                $orderResult = $db->get_results($sSQL);
                $RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");
                foreach ($orderResult as $var)
                {
                    $Total = $db->get_var("SELECT sum(Amount) FROM tbl_order_details WHERE Available = 1 and OrderId=" . $var->OrderId);
                    if (intval($Total) != 0.00)
                        $var->TotalAmount = $Total;
                    else
                        $var->TotalAmount = $db->get_var("SELECT sum(Amount) FROM tbl_order_details WHERE  OrderId=" . $var->OrderId);
                   
                    $var->TotalAmount =  round($var->TotalAmount + (($var->TotalAmount * $RateThresold) /100),2) . "";
                    $var->TotalAmount= number_format( $var->TotalAmount, 2, '.', '' );
                }
                if( isset( $orderResult ) && !empty( $orderResult ) )
                     $res = array( 'message_code' => 1000, 'data_text' => $orderResult );
                else
                    $res = array( 'message_code' => 999, 'message_text' => "No Orders for selected filters." );
            }
        }
        else
            $res = array( 'message_code' => 999, 'data_text' => "Wrong registration id." );
        
       
        return $response->withJson( $res, 200 );
    }
    function tbx_order_export_PDF_invoice($orderid, $email)
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        $response = file_get_contents('http://staging.toolbx.com/admin/order/' . $orderid . '/invoice');
        $subject = 'Order #' . $orderid . ' Invoice';
        $db = database();
        $base_query = $db->get_row('SELECT tbl_registration.RegistrationName FROM tbl_registration JOIN tbl_order ON tbl_order.RegistrationId = tbl_registration.RegistrationId WHERE tbl_order.OrderId = ' . $orderid);
        // $email = $base_query->RegistrationEmail;
        $name = $base_query->RegistrationName;
        $data = [
            [
                'name' => 'NAME',
                'content' => $name
            ],
            [
                'name' => 'ORDERID',
                'content' => $orderid
            ]
        ];
        $attachments = [
            [
                'type' => 'application/pdf',
                'name' => $orderid . '_invoice.pdf',
                'content' => base64_encode(file_get_contents(dirname(dirname(__DIR__)) . '/invoices/' . $orderid . '_invoice.pdf')),
            ]
        ];
        tbx_ajitem_order_invoice_mail($name, $email, $subject, $data, $attachments);
    }
    function tbx_order_export_PDF( Request $request, Response $response )
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $body = $request->getParsedBody();
        $OrderId  = $request->getAttribute("orderid");
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        $subject = 'Order# ' . $OrderId . ' Invoice';
        $r = file_get_contents('http://staging.toolbx.com/admin/order/' . $OrderId . '/invoice');
        $base_query = $db->get_row('SELECT tbl_registration.RegistrationName, tbl_registration.RegistrationEmail FROM tbl_registration JOIN tbl_order ON tbl_order.RegistrationId = tbl_registration.RegistrationId WHERE tbl_order.OrderId = ' . $OrderId);
        $email = $base_query->RegistrationEmail;
        $name = $base_query->RegistrationName;
        $data = [
            [
                'name' => 'NAME',
                'content' => $name
            ],
            [
                'name' => 'ORDERID',
                'content' => $OrderId
            ]
        ];
        $attachments = [
            [
                'type' => 'application/pdf',
                'name' => $OrderId . '_invoice.pdf',
                'content' => base64_encode(file_get_contents(dirname(dirname(__DIR__)) . '/invoices/' . $OrderId . '_invoice.pdf')),
            ]
        ];
        tbx_ajitem_order_invoice_mail($name, $email, $subject, $data, $attachments);
      
        // $Message = 'Dear Toolbx User, <br/><br/> As per your request we are sending the order details in the attached PDF file. Please download the attachment and keep it for your record.<br/><br/> Thanks and Regards<br /><br />Toolbx Support Team';
        // try {
        //     //Server settings
        //     $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        //     $mail->isSMTP();                                      // Set mailer to use SMTP
        //     $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        //     $mail->SMTPAuth = true;                               // Enable SMTP authentication
        //     $mail->Username = 'info@toolbx.com';                 // SMTP username
        //     $mail->Password = 'Toolbx123';                           // SMTP password
        //     $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        //     $mail->Port = 587;                                    // TCP port to connect to
        //     //Recipients
        //     $mail->setFrom('info@mytoolbx.com', 'Toolbx Support');
        //     $mail->addAddress($email);     // Add a recipient
        //     //Content
        //     $mail->isHTML(true);                                  // Set email format to HTML
        //     $mail->Subject = $Subject;
        //     $mail->Body    = $Message;
        //     // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        //     $mail->AddAttachment(dirname(dirname(__DIR__)) . '/invoices/' . $OrderId . '_invoice.pdf');
        //     $mail->send();
        //     // return 'Message has been sent';
        //     // return true;
        //     $res = array( 'message_code' => 1000, 'data_text' => 'Please check your email for order PDF.' );
        //     return $response->withJson( $res, 200 );
        // } catch (Exception $e) {
        //     echo 'Message could not be sent.';
        //     echo 'Mailer Error: ' . $mail->ErrorInfo;
        //     return false;
        // }
    }
    function tbx_order_status_words( Request $request, Response $response )
    {
        
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $db = database();
        $order_id  = $request->getAttribute("orderid");
        $status = $db->get_var("SELECT (CASE WHEN Delivered = 'Y' THEN 'Delivered' WHEN IsCancel = 'Y' THEN 'Cancelled' WHEN IsLeaving='Y' THEN 'Inprocess' WHEN IsAccepted='Y' THEN 'Accepted' ELSE 'Pending' END) as STATUS FROM tbl_order WHERE OrderId = " . $order_id);
        $res = array( 'message_code' => 1000, 'data_text' => $status );
        return $response->withJson( $res, 200 );
    }
    /**
     * AJITEM EDIT: 27/10/2017 - Orders API
     */
    function tbx_orders_list_all( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $page_number = $request->getQueryParam('page_number');
        $from_date = $request->getQueryParam('form_date');
        $to_date = $request->getQueryParam('to_date');
        $start = $request->getQueryParam('start');
        $length = $request->getQueryParam('length');
        $draw = $request->getQueryParam('draw');
        if( NULL === $page_number )
        {
            $page_number = 1;
        }
        $db = database();
        $base_query = 'SELECT MIN(CreatedOn) AS fromDate, MAX(CreatedOn) AS toDate FROM tbl_order';
        $dates = $db->get_row($base_query);
        if( NULL === $from_date )
        {
            $from_date = date('Y-m-d H:i:s', strtotime($dates->fromDate));
        }
        else
        {
            $from_date = date('Y-m-d H:i:s', strtotime($from_date));   
        }
        if( NULL === $to_date )
        {
            $to_date = date('Y-m-d H:i:s', strtotime($dates->toDate));
        }
        else
        {
            $to_date = date('Y-m-d H:i:s', strtotime($to_date));   
        }
        $page_limit = 10;
        
        $base_query = "SELECT count(*) AS count FROM tbl_order WHERE CreatedOn >= '" . $from_date . "' AND CreatedOn <= '" . $to_date . "'";
        $total_records = $db->get_row($base_query);
        $pages = round( $total_records->count / $page_limit, 0, PHP_ROUND_HALF_DOWN );
        $page_offset = $page_limit * ($page_number - 1);
        $base_query = "SELECT tor.OrderId, tc.CompanyName, tj.JobSiteName, tor.TotalAmount, tor.CreatedOn, tor.PickRunnerId, tor.status
                        FROM  `tbl_order` AS tor
                        JOIN  `tbl_companies` AS tc ON tor.CompanyId = tc.CompanyId
                        JOIN  `tbl_jobsite` AS tj ON tor.JobSiteId = tj.JobSiteId
                        WHERE tor.CreatedOn >= '" . $from_date . "' AND tor.CreatedOn <= '" . $to_date . "'
                        ORDER BY  tor.CreatedOn DESC
                        LIMIT " . $length . " OFFSET " . $start;
        $results = $db->get_results( $base_query, 'ARRAY_A' );
        if(!$results)
        {
            // $res = array( 'message_code' => 999, 'message_text' => 'Please try again.');
            $res = array( 'draw' => $draw,
            'recordsTotal' => $total_records->count,
            'recordsFiltered' => count($results),
            'data' => $results,
            'dates' => [ 'fromDate' => $from_date, 'toDate' => $to_date ] );
        }   
        else
        {       
            $res = array( 'draw' => $draw,
            'recordsTotal' => $total_records->count,
            'recordsFiltered' => count($results),
            'data' => $results,
            'dates' => [ 'fromDate' => $from_date, 'toDate' => $to_date ] );
        }
        return $response->withJson( $res, 200 );
    }
    function tbx_orders_details(Request $request, Response $response)
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $order_id = $request->getAttribute('orderId');
        $db = database();
        $base_query = 'SELECT tor.OrderId, CONCAT(tj.Address,",",tj.PostalCode) as Address, tor.CreatedOn, tor.status, tor.TotalAmount, tor.TaxAmount, tor.DeliveryCharges, tod . * , tp.ProductName
            FROM tbl_order AS tor
            JOIN  `tbl_companies` AS tc ON tor.CompanyId = tc.CompanyId
            JOIN  `tbl_jobsite` AS tj ON tor.JobSiteId = tj.JobSiteId
            JOIN  `tbl_order_details` AS tod ON tor.OrderId = tod.OrderId
            JOIN  `tbl_product` AS tp ON tp.ProductId = tod.ProductId
            WHERE tor.OrderId = ' . $order_id;
        $order_details = $db->get_results($base_query);
        return $response->withJson($order_details);
    }
    function tbx_orders_export( Request $request, Response $response )
    {
        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        $ids = $request->getQueryParam('ids');
        $from_date = $request->getQueryParam('from_date');
        $to_date = $request->getQueryParam('to_date');
        if( NULL !== $ids )
        {
            // SELECTED EXPORT
            $base_query = "SELECT tor.OrderId, tj.Address, tor.CreatedOn, tor.status, tor.TotalAmount, tor.TaxAmount, tor.DeliveryCharges, SUM(tod.Amount) as Amount, tod.Delivered, tc.ompanyName, tj.JobSiteName, tor.CreatedOn, tor.PickRunnerId
                FROM tbl_order AS tor
                JOIN  `tbl_companies` AS tc ON tor.CompanyId = tc.CompanyId
                JOIN  `tbl_jobsite` AS tj ON tor.JobSiteId = tj.JobSiteId
                JOIN  `tbl_order_details` AS tod ON tor.OrderId = tod.OrderId
                WHERE tor.OrderId IN (" . $ids . ") GROUP BY (tor.OrderId)";
        }
        else
        {
            // COMPLETE EXPORT
            $base_query = "SELECT tor.OrderId, tj.Address, tor.CreatedOn, tor.status, tor.TotalAmount, tor.TaxAmount, tor.DeliveryCharges, SUM(tod.Amount) as Amount, tod.Delivered, tc.ompanyName, tj.JobSiteName, tor.CreatedOn, tor.PickRunnerId
                FROM tbl_order AS tor
                JOIN  `tbl_companies` AS tc ON tor.CompanyId = tc.CompanyId
                JOIN  `tbl_jobsite` AS tj ON tor.JobSiteId = tj.JobSiteId
                JOIN  `tbl_order_details` AS tod ON tor.OrderId = tod.OrderId 
                WHERE tor.CreatedOn >= '" . $from_date . "' AND tor.CreatedOn <= '" . $to_date . "' GROUP BY (tor.OrderId)";
        }
        $db = database();
        $data = $db->get_results($base_query, 'ARRAY_A');
        ini_set("auto_detect_line_endings", true);
        $csv_data = str_putcsv($data);
        
        $res = array( 'message_code' => 1000, 'data_text' => $csv_data );
        return $response->withJson($res);
    }
    /** AJITEM EDIT END */