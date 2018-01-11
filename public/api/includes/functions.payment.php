<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    //require 'stripe/Stripe.php';

    /*
    * admin payment list
    */
    function tbx_payment_details( Request $request, Response $response )
    {
      $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    	$db = database();
      $cmpid = $request->getAttribute('cmpid');
      //$base_query = 'SELECT PaymentDate, OrderId, CardStripTokan, TotalAmount  FROM `tbl_payments` WHERE display=\'Y\' ORDER BY `PaymentId` DESC ';
    	$base_query = 'SELECT tpay.PaymentDate, tpay.OrderId, tpay.CardStripTokan, tpay.TotalAmount, tp.CompanyName,tp.CompanyId  FROM `tbl_payments` AS tpay 
                      LEFT JOIN tbl_registration AS tr ON tr.RegistrationId = tpay.OwnerId
                      LEFT JOIN tbl_companies AS tp ON tp.companyId = tr.companyId 
                      WHERE tpay.display=\'Y\' AND tr.CompanyId= "'.$cmpid.'" ORDER BY tpay.`PaymentId` DESC ';
    	$result = $db->get_results( $base_query );
    	if( $result )
    	{
    		$res = array( 'message_code' => 1000, 'data_text' => $result );
    	}
    	else
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'Payment details not found.');
    		
    	}
    	return $response->withJson( $res, 200 );
    
    }

    /*
    * Get payment data in orderid
    */
    function tbx_payment_card_details_onorderid( Request $request, Response $response )
    {
      $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        	
    	$db = database();
    	$OrderId = $request->getAttribute('orderid');
    	
    	if (($OrderId == null) || ($OrderId==""))
    		$res = array( 'message_code' => 999, 'message_text' => 'Order id cannot be blank.');
    	else
    	{
        	$base_query = 'SELECT tbo.OrderId, tbo.RegistrationId, tbo.CompanyId,tr.CardNumber,tr.CardExpiryDate, tr.CVC, tr.CardStripTokan, tr.CardType,tr.CardDetails FROM `tbl_order` AS tbo 
                            LEFT JOIN `tbl_registration` AS tr ON tr.RegistrationId = tbo.RegistrationId WHERE tbo.OrderId="'.$OrderId.'" ';
        	$result = $db->get_results( $base_query );
        	if( $result )
        	{
        		$res = array( 'message_code' => 1000, 'data_text' => $result );
        	}
        	else
        	{
        		$res = array( 'message_code' => 999, 'message_text' => 'Payment details not found on these order.');
        		
        	}
    	}
    	return $response->withJson( $res, 200 );
    
    }

    function tbx_user_payments( Request $request, Response $response )
    {
        require_once("./includes/Stripe/init.php");

        $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

        $db = database();
        $body = $request->getParsedBody();

        $order_id = $body['OrderId'];
        
      if (($order_id == null) || ($order_id==""))
          $res = array( 'message_code' => 999, 'message_text' => 'Order id cannot be blank.');
      else
      {
        $base_querystst = 'SELECT Delivered, status FROM `tbl_order` WHERE OrderId = ' . $order_id;
        $resultquery = $db->get_row($base_querystst);

        if (($resultquery->status == "") || ($resultquery->status== null)) 
        {             
            $base_query = 'SELECT RegistrationId FROM `tbl_order` WHERE OrderId = ' . $order_id;
            $user_id = $db->get_var($base_query);
            
            $base_query = 'SELECT tr.RegistrationId, tr.CardStripeTokenId, tr.RegistrationEmail FROM tbl_registration AS tr WHERE tr.RegsitrationRoleId = 2 and tr.CompanyId in (Select CompanyId from tbl_order WHERE OrderId = ' . $order_id . ')';

            $result = $db->get_results( $base_query );
            if ( isset($result) && !empty($result) )
            {

                $Owner_Id = $result[0]->RegistrationId;
                $cardstripeid = $result[0]->CardStripeTokenId;
                $regemail = $result[0]->RegistrationEmail;

                if (($cardstripeid == null) || ($cardstripeid =="") || ($cardstripeid =="-")  )
                  $res = array( 'message_code' => 999, 'message_text' => 'Your card information is not updated and verified with Stripe. Please update it using profile option.');
                else
                {
                    $base_query = 'SELECT tbo.OrderId, RegistrationId  FROM `tbl_order` AS tbo WHERE tbo.OrderId=' . $order_id;
                    
                    $result = $db->get_results( $base_query );
                    if ( isset($result) && !empty($result) )
                    {
                        $orderid = $result[0]->OrderId;
                        // $totalamount =$result[0]->TotalAmount;
                        $sSQL = "SELECT sum(Amount) FROM tbl_order_details WHERE OrderId=" . $order_id . " and Available=1";
                        $totalamount = $db->get_var($sSQL);

                        $RateThresold = $db->get_var("SELECT ParameterValue from tbl_SystemParameters where ParameterName='PRICETHRESOLD'");
                        $DeliveryCharges = 35.00;

                        $totalamount = round((floatval($totalamount) + ((floatval($totalamount) * floatval($RateThresold))/100)) ,2);


                        //HST 13%
                        $tax = round((($totalamount + $DeliveryCharges) * 0.13),2);
                        // $totalamount = round(((floatval($totalamount)) + (((floatval($totalamount)) * 13)/100)) ,2);

                        // if (intval($totalamount) <= 100 && intval($totalamount) > 0)
                        //     $DeliveryCharges = 25.00;
                        // else if (intval($totalamount) > 100) 
                        //     $DeliveryCharges = 50.00;
                        // else 
                        //     $DeliveryCharges = 0.00;

                        $totalamount = (floatval($totalamount) + intval($DeliveryCharges)) + $tax;
                        
                        $amount_cents = floatval($totalamount) * 100; 
                        $description = "Order No#" . $orderid;

                         // $res = array( 'message_code' => 999, 'message_text' => $sSQL . "-" . $amount_cents);
                         // return $response->withJson( $res, 200 );

                        \Stripe\Stripe::setApiKey(STRIPE_PUBLISHED_KEY); //Replace with your Secret Key

                        // $customer = \Stripe\Customer::create(array(
                        // 'card' => $cardstripeid,
                        // 'email'    => $regemail
                        // ));
                        // $cardstripeid = $customer['id'];
                        // $lastmodifiedon = date('Y-m-d H:i:s');
                        // $base_query = "UPDATE tbl_registration SET LastModifiedOn='" . $lastmodifiedon . "', CardStripeTokenId='" . $cardstripeid . "' WHERE RegistrationId=" . $Owner_Id;
                        // $db->query($base_query);
                        try 
                        {
                            $charge = \Stripe\Charge::create(array(
                            "customer"    => $cardstripeid,
                            "amount" => $amount_cents,
                            "currency" => "cad",
                            "capture" => true,
                            "description" => $description)     
                            );

                            $stripeToken = $charge->id;

                            // Payment has succeeded, no exceptions were thrown or otherwise caught    
                            $result = "success";
                            $output = str_replace("Stripe_Charge JSON: ","",$charge);

                            $lastmodifiedon = date('Y-m-d H:i:s');
                            $base_query = $db->query('UPDATE tbl_order SET LastModifiedOn="'. $lastmodifiedon .'", status="PAID" where OrderId='. $order_id);

                            $paymentdate = date('Y-m-d H:i:s');
                            $CreatedOn = date('Y-m-d H:i:s');
                            $base_queryres = ' INSERT INTO tbl_payments (OrderId, TotalAmount, OwnerId, CardNumber, CardExpiryDate, CardDetails, CardStripTokan, PaymentDate, CreatedOn) VALUES("' . $orderid . '" , "' .  $amount_cents . '" ,"' . $Owner_Id . '"  ,"-" , "-", "' . $description . '" , "'. $stripeToken .'", "'. $paymentdate .'", "'. $CreatedOn .'") ';
                            $query_res = $db->query($base_queryres);

                            tbx_order_export_PDF_invoice($orderid, $regemail);
                            $res = array( 'message_code' => 1000, 'data_text' => $output );
                        }
                        catch(\Stripe\Error\Card $e) 
                        {
                        
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        } 
                        catch (\Stripe\Error\RateLimit $e) 
                        {
                          // Too many requests made to the API too quickly
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        } 
                        catch (\Stripe\Error\InvalidRequest $e)
                        {
                          // Invalid parameters were supplied to Stripe's API
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        }
                        catch (\Stripe\Error\Authentication $e)
                        {
                          // Authentication with Stripe's API failed
                          // (maybe you changed API keys recently)
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        }
                        catch (\Stripe\Error\ApiConnection $e)
                        {
                          // Network communication with Stripe failed
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        }
                        catch (\Stripe\Error\Base $e)
                        {
                          // Display a very generic error to the user, and maybe send
                          // yourself an email
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        }
                        catch (Exception $e)
                        {
                          // Something else happened, completely unrelated to Stripe
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
                          $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                        }
                    } 
                    else
                      $res = array( 'message_code' => 999, 'message_text' => 'Order details are not right.');
                }
            }
            else
              $res = array( 'message_code' => 999, 'message_text' => 'Order details are not right.');
        }
        else
          $res = array( 'message_code' => 999, 'message_text' => 'These Order Payment has already done!');
      }
      return $response->withJson( $res, 200 );
    }





    /*
    * user payment
    */
    // function tbx_user_payments( Request $request, Response $response )
    // {
    //    require_once("./includes/Stripe/init.php");

    //   $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        	
    // 	$db = database();
    // 	$body = $request->getParsedBody();
    
    // 	$order_id = $body['OrderId'];
    // 	//$Action = $body['Action'];
    // 	$Owner_Id=0;
    	
    // 	//$user_id = $body['RegistrationId'];
    	
    // 	if(($order_id == null) || ($order_id==""))
  		// $res = array( 'message_code' => 999, 'message_text' => 'Order id cannot be blank.');
    //   // 	else if(($user_id == null) || ($user_id==""))
    //   // 	$res = array( 'message_code' => 999, 'message_text' => 'User/Registration id cannot be blank.');
    // 	else
    // 	{
	   //      $base_querystst = 'SELECT Delivered, status FROM `tbl_order` WHERE OrderId = ' . $order_id;
    // 	    $resultquery = $db->get_row($base_querystst);
    //       if(($resultquery->status == "") || ($resultquery->status== null)) 
	   //      {	            
    //     	    $base_query = 'SELECT RegistrationId FROM `tbl_order` WHERE OrderId = ' . $order_id;
    //     	    $user_id = $db->get_var($base_query);
    	    
    	   
    //     	    $base_query = 'SELECT tr.RegsitrationRoleId FROM `tbl_registration` AS tr WHERE tr.RegistrationId = ' . $user_id;
    //     	    $RegId = 0;
    //     	    $Role = $db->get_var($base_query);
        	    
    //     	   // if ($Role == 2)
        	    
    //             //   $base_query = 'SELECT tr.CardNumber,tr.CardExpiryDate, tr.CVC  FROM `tbl_registration` AS tr WHERE tr.RegsitrationRoleId = ' . $user_id;
        	        
    //     	   // else
    	   
    // 	        $base_query = 'SELECT tr.RegistrationId, tr.CardNumber, tr.CardExpiryDate, tr.CVC, tr.CardStripeTokenId, tr.RegistrationEmail  FROM `tbl_registration` AS tr WHERE tr.RegsitrationRoleId = 2 and tr.CompanyId in (Select CompanyId from tbl_order WHERE OrderId = ' . $order_id . ')';
    	    
    // 	        //echo $base_query . "<br/>";
    // 	    	  $result = $db->get_results( $base_query );
    //          	if( $result  )
    //         	{
            	    
    //     	        $cardnumber = $result[0]->CardNumber;
    //                 $cardexpdate = $result[0]->CardExpiryDate;
    //                 $cardcvc = $result[0]->CVC;
    //                 $Owner_Id = $result[0]->RegistrationId;
    //                 $cardstripeid = $result[0]->CardStripeTokenId;
    //                 $regemail = $result[0]->RegistrationEmail;
                    
                  
                    
    //     	    }
        	   
    // 	        $base_query = 'SELECT tbo.OrderId, tbo.TotalAmount, RegistrationId  FROM `tbl_order` AS tbo WHERE tbo.OrderId=' . $order_id;
    // 	        //echo $base_query . "<br/>";
    	    
    //         	$result = $db->get_results( $base_query );
    //         	if( $result  )
    //         	{
    //         	     $orderid = $result[0]->OrderId;
    //                  $totalamount =$result[0]->TotalAmount;
            	    
    //                 // echo $totalamount . "<br/>";
    //                 // echo $cardnumber . "<br/>";
    //                 // echo $cardcvc . "<br/>";
    //                 // echo $cardexpdate . "<br/>";
            
    //                 // $orderid = "123456";
    //                 // $totalamount = "100.00";
    //                 // $cardnumber = "4242424242424242";
    //                 // $cardcvc = "123";
    //                 // $cardexpdate = "01/18";
                    
                    
    //                 $expiry = explode("/", $cardexpdate);
                    
    //                 //echo STRIPE_PUBLISHED_KEY . "<br/>";
    //                 \Stripe\Stripe::setApiKey(STRIPE_PUBLISHED_KEY); //Replace with your Secret Key
    //                 //echo "above is the Key<br/>"; 
    //                   try {  
                          
    //                         $result = \Stripe\Token::create(
    //                         array(
    //                                 "card" => array(
    //                                     "name" => "Order No#" . $orderid,
    //                                     "number" => $cardnumber,
    //                                     "exp_month" => $expiry[0],
    //                                     "exp_year" => $expiry[1],
    //                                     "cvc" => $cardcvc
    //                                 )
    //                             )
    //                         );
        
    //                         //echo $result . "<br/>";
    //                         $stripeToken = $result['id'];
                            
    //                         //echo $stripeToken . "<br/>";
    //                         if(($stripeToken == null) || ($stripeToken==""))
    //                              $res = array( 'message_code' => 999, 'message_text' => 'Unable to get Stripe Token for card details.');
    //                         else
    //                         {
                             
    //                            if(($cardstripeid == null) || ($cardstripeid =="") || ($cardstripeid =="-")  )
    //                               {
    //                                   //echo "card stripe id is blank";
    //                                   $customer = \Stripe\Customer::create(array(
    //                                                   'card' => $stripeToken,
    //                                                   'email'    => $regemail
    //                                               ));
                                            
    //                                      $cardstripeid = $customer['id'];
    //                                      $lastmodifiedon = date('Y-m-d H:i:s');
    //                                      $base_query = "UPDATE tbl_registration SET LastModifiedOn='" . $lastmodifiedon . "', CardStripeTokenId='" . $cardstripeid . "' WHERE RegistrationId=" . $Owner_Id;
    //                                      $db->query($base_query);
    //                                   }
                                       
                                    
    //                                   $amount_cents = str_replace(".","",$totalamount); 
    //                                   $description = "Order No#" . $orderid;
                                     
                                 
    //                                      try 
    //                                      {
                                            
    //                                          $charge = \Stripe\Charge::create(array(
    //                                          "customer"    => $cardstripeid,
    //                                          "amount" => $amount_cents,
    //                                          "currency" => "cad",
    //                                          "capture" => true,
    //                                          "description" => $description)     
    //                                       );
                
    //                                       // Payment has succeeded, no exceptions were thrown or otherwise caught    
    //                                         $result = "success";
    //                                         $output = str_replace("Stripe_Charge JSON: ","",$charge);
                                            
    //                                         $lastmodifiedon = date('Y-m-d H:i:s');
    //                                         $base_query = $db->query('UPDATE tbl_order SET LastModifiedOn="'. $lastmodifiedon .'", status="PAID" where OrderId='. $order_id);
                                              
    //                                         $paymentdate = date('Y-m-d H:i:s');
    //                                         $CreatedOn = date('Y-m-d H:i:s');


    //                                         $base_queryres = ' INSERT INTO tbl_payments (OrderId, TotalAmount, OwnerId, CardNumber, CardExpiryDate, CardDetails, CardStripTokan, PaymentDate, CreatedOn) VALUES("' . $orderid . '" , "' . $totalamount . '" ,"' . $Owner_Id . '"  ,"' . $cardnumber . '" , "' . $cardexpdate . '", "' . $description . '" , "'. $stripeToken .'", "'. $paymentdate .'", "'. $CreatedOn .'") ';
    //                         				        $query_res = $db->query($base_queryres);
                            				
    //                                         $res = array( 'message_code' => 1000, 'data_text' => $output );
                                            
    //                                     } 
    //                                     catch(\Stripe\Error\Card $e) {
    //                                     // Since it's a decline, \Stripe\Error\Card will be caught
    //                                     $body = $e->getJsonBody();
    //                                     $err  = $body['error'];

    //                                     // print('Status is:' . $e->getHttpStatus() . "\n");
    //                                     // print('Type is:' . $err['type'] . "\n");
    //                                     // print('Code is:' . $err['code'] . "\n");
    //                                     // // param is '' in this case
    //                                     // print('Param is:' . $err['param'] . "\n");
    //                                     // print('Message is:' . $err['message'] . "\n");
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                     $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   } catch (\Stripe\Error\RateLimit $e) {
    //                                     // Too many requests made to the API too quickly
    //                                     $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                     $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
                                        
    //                                   } catch (\Stripe\Error\InvalidRequest $e) {
    //                                     // Invalid parameters were supplied to Stripe's API
    //                                      $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                      $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                      $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   } catch (\Stripe\Error\Authentication $e) {
    //                                     // Authentication with Stripe's API failed
    //                                     // (maybe you changed API keys recently)
    //                                      $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                      $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   } catch (\Stripe\Error\ApiConnection $e) {
    //                                     // Network communication with Stripe failed
    //                                      $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                      $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   } catch (\Stripe\Error\Base $e) {
    //                                     // Display a very generic error to the user, and maybe send
    //                                     // yourself an email
    //                                      $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                      $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   } catch (Exception $e) {
    //                                     // Something else happened, completely unrelated to Stripe
    //                                      $body = $e->getJsonBody();
    //                                     $err  = $body['error'];
    //                                     $error = $err['message'];
    //                                     $result = "declined";
    //                                     $res = array( 'message_code' => 999, 'message_text' => $error);
    //                                      $base_query = $db->query('UPDATE tbl_order SET status = "' . $result . '" where OrderId='. $order_id);
    //                                     $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $res . '") ';
    //                                   }
    //                             }
                            
    //                       }
    //                       catch (Exception $e) {
    //                         // Something else happened, completely unrelated to Stripe
    //                          $body = $e->getJsonBody();
    //                         $err  = $body['error'];
    //                         $error = $err['message'];
    //                         $result = "declined";
    //                         $res = array( 'message_code' => 999, 'message_text' => $error);
    //                         $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $error . '") ';
    //                             $query_res = $db->query($base_queryerror);
    //                       }

    //           }
    //         	else
    //         	{
    //         		$res = array( 'message_code' => 999, 'message_text' => 'Payment details not found on these order.');
    //         	}

    // 	    } 
    // 	    else
    // 	    {
    // 	        $res = array( 'message_code' => 999, 'message_text' => 'These Order Payment has already done!');
    // 	    }
    // 	}
    // 	return $response->withJson( $res, 200 );
    // }

    /*
    * Validating card details
    */
    // On Card details
    
    function tbx_user_card( Request $request, Response $response )
    {
      //require_once("/var/www/html/toolbx/public/api/includes/stripe/lib/Stripe.php");
      //require_once("./includes/Stripe/lib/Stripe.php");
      
      require_once("./includes/Stripe/init.php");


      $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	
    	$body = $request->getParsedBody();
        
        $cardnumber = $body['CardNumber'];
        $cardexpdate = $body['CardExpiryDate'];
        $cardcvc = $body['CVC'];
        $Owner_Id  = $body['OwnerId'];

        if(($cardnumber == null) || ($cardnumber==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Card number cannot be blank.');
        else if(($cardexpdate == null) || ($cardexpdate==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Card expiry date cannot be blank.');
        else if(($cardcvc == null) || ($cardcvc==""))   
            $res = array( 'message_code' => 999, 'message_text' => 'Card CVC cannot be blank.');
        else
        {
    	       
    	        /*$orderid = "-1";*/
                $totalamount = "1.00";
                $expiry = explode("/", $cardexpdate);
                
                //echo STRIPE_PUBLISHED_KEY . "<br/>";
                \Stripe\Stripe::setApiKey(STRIPE_PUBLISHED_KEY); //Replace with your Secret Key

                //echo "Done";
                //echo "above is the Key<br/>"; 
                try {  
                      
                        $result = \Stripe\Token::create(
                        array(
                            "card" => array(
                                //"name" => "Order No#" . $orderid,
                                "number" => $cardnumber,
                                "exp_month" => $expiry[0],
                                "exp_year" => $expiry[1],
                                "cvc" => $cardcvc
                               
                            )
                        )
                    );
    
                    $stripeToken = $result['id'];
                    
                     //echo $stripeToken . "<br/>";
                     
                    if(($stripeToken == null) || ($stripeToken==""))
                         $res = array( 'message_code' => 999, 'message_text' => 'Unable to get Stripe Token for card details.');
                    else
                    {
                         $amount_cents = str_replace(".","",$totalamount); 
                         $description = "Verify Card";
                      
                         try 
                         {
                            $charge = \Stripe\Charge::create(array(   
                             "amount" => $amount_cents,
                             "currency" => "cad",
                             "source" => $stripeToken,
                             "capture" => false,
                             "description" => $description)     
                          );
                        
                          // Payment has succeeded, no exceptions were thrown or otherwise caught    
                            $result = "success";
                            $res = array( 'message_code' => 1000, 'data_text' => "Card information is valid." );

                            $rows= $db->get_results("SELECT RegistrationName, RegistrationEmail FROM tbl_registration WHERE RegistrationId=" . $Owner_Id);
                            if (isset($rows) && !empty($rows))
                            {
                               $result = \Stripe\Token::create( array(
                                        "card" => array(
                                            "name" => $rows[0]->RegistrationName,
                                            "number" => $cardnumber,
                                            "exp_month" => $expiry[0],
                                            "exp_year" => $expiry[1],
                                            "cvc" => $cardcvc
                                        )
                                    )
                                );
          
                                $stripeToken = $result['id'];
                                if(($stripeToken == null) || ($stripeToken==""))
                                     $res = array( 'message_code' => 999, 'message_text' => 'Unable to get Stripe Token for card details.');
                                else
                                {
                                    $customer = \Stripe\Customer::create(array(
                                    'card' => $stripeToken,
                                    'email'    => $rows[0]->RegistrationEmail
                                    ));
                                    
                                    //echo $customer['id'] . "<br/>"; 
                                    $cardstripeid = $customer['id'];
                                    $lastmodifiedon = date('Y-m-d H:i:s');
                                    $base_query = "UPDATE tbl_registration SET LastModifiedOn='" . $lastmodifiedon . "', CardStripeTokenId='" . $cardstripeid . "' WHERE RegistrationId=" . $Owner_Id;
                                    //echo $base_query;
                                    $db->query($base_query);
                                  
                                    $res = array( 'message_code' => 1000, 'message_text' => 'Customer updated in Stripe.');
                                }
                            }
                         } 
                         catch(\Stripe\Error\Card $e) {
                          // Since it's a decline, \Stripe\Error\Card will be caught
                          $body = $e->getJsonBody();
                          $err  = $body['error'];

                          // print('Status is:' . $e->getHttpStatus() . "\n");
                          // print('Type is:' . $err['type'] . "\n");
                          // print('Code is:' . $err['code'] . "\n");
                          // // param is '' in this case
                          // print('Param is:' . $err['param'] . "\n");
                          // print('Message is:' . $err['message'] . "\n");
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        } catch (\Stripe\Error\RateLimit $e) {
                          // Too many requests made to the API too quickly
                          $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                          
                        } catch (\Stripe\Error\InvalidRequest $e) {
                          // Invalid parameters were supplied to Stripe's API
                           $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        } catch (\Stripe\Error\Authentication $e) {
                          // Authentication with Stripe's API failed
                          // (maybe you changed API keys recently)
                           $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        } catch (\Stripe\Error\ApiConnection $e) {
                          // Network communication with Stripe failed
                           $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        } catch (\Stripe\Error\Base $e) {
                          // Display a very generic error to the user, and maybe send
                          // yourself an email
                           $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        } catch (Exception $e) {
                          // Something else happened, completely unrelated to Stripe
                           $body = $e->getJsonBody();
                          $err  = $body['error'];
                          $error = $err['message'];
                          $result = "declined";
                          $res = array( 'message_code' => 999, 'message_text' => $error);
                        }
                    }
                
                }
                catch (Exception $e) {
                  // Something else happened, completely unrelated to Stripe
                   $body = $e->getJsonBody();
                  $err  = $body['error'];
                  $error = $err['message'];
                  $result = "declined";
                  $res = array( 'message_code' => 999, 'message_text' => $error);
                }
    	  }
    	return $response->withJson( $res, 200 );
    } 



    /*
    * payment testing
    */
    function tbx_user_testing_payments( Request $request, Response $response )
    {
      require_once("./includes/Stripe/init.php");

      $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
        	
    	$db = database();
    	$body = $request->getParsedBody();
    
    	$order_id = $body['OrderId'];
    	
    	//$Owner_Id=0;
    	
    	//$user_id = $body['RegistrationId'];
    	
    	if(($order_id == null) || ($order_id==""))
    		$res = array( 'message_code' => 999, 'message_text' => 'Order id cannot be blank.');
        else
    	{
    	    $base_query = 'SELECT RegistrationId FROM `tbl_order` WHERE OrderId = ' . $order_id;
    	    $user_id = $db->get_var($base_query);
    	    
    	    $base_query = 'SELECT RegsitrationRoleId FROM `tbl_registration` WHERE RegistrationId = ' . $user_id;
    	    $Role = $db->get_var($base_query);
    	    $RegId = 0;
    	   if ($Role->RegsitrationRoleId==2)
    	    {
        	   $base_query = 'SELECT tr.CardNumber,tr.CardExpiryDate, tr.CVC  FROM `tbl_registration` AS tr WHERE tr.RegsitrationRoleId = ' . $user_id;
    	    }   
    	   else
    	   {
    	        $base_query = 'SELECT tr.RegistrationId, tr.CardNumber,tr.CardExpiryDate, tr.CVC  FROM `tbl_registration` AS tr WHERE tr.RegsitrationRoleId = 2 and tr.CompanyId in (Select CompanyId from tbl_order WHERE OrderId = ' . $order_id . ')';
    	   }
    	    
    	        //print_r($base_query);exit; 
    	    	$result = $db->get_results( $base_query );
               	if( $result  )
            	{
        	        $cardnumber = $result[0]->CardNumber;
                    $cardexpdate = $result[0]->CardExpiryDate;
                    $cardcvc = $result[0]->CVC;
                    $Owner_Id = $result[0]->RegistrationId;
        	    }
    	       
    	      $base_query = 'SELECT tbo.OrderId, tbo.TotalAmount, RegistrationId  FROM `tbl_order` AS tbo WHERE tbo.OrderId=' . $order_id;
    	    
    	    
        	$result = $db->get_results( $base_query );
        	if( $result  )
        	{
        	     $orderid = $result[0]->OrderId;
                 $totalamount =$result[0]->TotalAmount;
                
                $expiry = explode("/", $cardexpdate);
                
                //echo STRIPE_PUBLISHED_KEY . "<br/>";
                \Stripe\Stripe::setApiKey(STRIPE_PUBLISHED_KEY); //Replace with your Secret Key
                //echo "above is the Key<br/>"; 
                  try {  
                      
                    $result = \Stripe\Token::create(
                        array(
                            "card" => array(
                                "name" => "Order No#" . $orderid,
                                "number" => $cardnumber,
                                "exp_month" => $expiry[0],
                                "exp_year" => $expiry[1],
                                "cvc" => $cardcvc
                            )
                        )
                    );
    
                    
                    $stripeToken = $result['id'];
                    
                    
                    if(($stripeToken == null) || ($stripeToken == ""))
                         $res = array( 'message_code' => 999, 'message_text' => 'Unable to get Stripe Token for card details.');
                    else
                    {
                        /* $amount_cents = str_replace(".","",$totalamount); 
                         $description = "Order No#" . $orderid;*/
                          
                            try {
                                $amount_cents = str_replace(".","",$totalamount); 
                                $description = "Order No#" . $orderid;
                             
                                $charge = \Stripe\Charge::create(array(   
                                 "amount" => $amount_cents,
                                 "currency" => "cad",
                                 "source" => $stripeToken,
                                 "capture" => true,
                                 "description" => $description)     
                                  );
        
                               
                                // Payment has succeeded, no exceptions were thrown or otherwise caught    
                                  $result = "success";
                                  $output = str_replace("Stripe_Charge JSON: ","",$charge);
                                  
                                  $base_query = $db->query('UPDATE tbl_order SET status="PAID" where OrderId='. $order_id);
                                  
                                  $base_query = ' INSERT INTO tbl_payments (OrderId, TotalAmount, OwnerId, CardNumber, CardExpiryDate, CardDetails, CardStripTokan) VALUES("' . $orderid . '" , "' . $totalamount . '" ,"' . $Owner_Id . '"  ,"' . $cardnumber . '" , "' . $cardexpdate . '", "' . $description . '" , "'. $stripeToken .'") ';
                  				        $query_res = $db->query($base_query);
                              			
                                  $res = array( 'message_code' => 1000, 'data_text' => $output );
                                    
                             } 
                             catch(\Stripe\Error\Card $e) {
                              // Since it's a decline, \Stripe\Error\Card will be caught
                              $body = $e->getJsonBody();
                              $err  = $body['error'];

                              // print('Status is:' . $e->getHttpStatus() . "\n");
                              // print('Type is:' . $err['type'] . "\n");
                              // print('Code is:' . $err['code'] . "\n");
                              // // param is '' in this case
                              // print('Param is:' . $err['param'] . "\n");
                              // print('Message is:' . $err['message'] . "\n");
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                              $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $error . '") ';
                              $query_res = $db->query($base_queryerror);
                            } catch (\Stripe\Error\RateLimit $e) {
                              // Too many requests made to the API too quickly
                              $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                              
                            } catch (\Stripe\Error\InvalidRequest $e) {
                              // Invalid parameters were supplied to Stripe's API
                               $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                              $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $e->getMessage() . '") ';
                              $query_res = $db->query($base_queryerror);
                            } catch (\Stripe\Error\Authentication $e) {
                              // Authentication with Stripe's API failed
                              // (maybe you changed API keys recently)
                               $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                            } catch (\Stripe\Error\ApiConnection $e) {
                              // Network communication with Stripe failed
                               $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                            } catch (\Stripe\Error\Base $e) {
                              // Display a very generic error to the user, and maybe send
                              // yourself an email
                               $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                            } catch (Exception $e) {
                              // Something else happened, completely unrelated to Stripe
                               $body = $e->getJsonBody();
                              $err  = $body['error'];
                              $error = $err['message'];
                              $result = "declined";
                              $res = array( 'message_code' => 999, 'message_text' => $error);
                              $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $e->getMessage() . '") ';
                              $query_res = $db->query($base_queryerror);
                            }
                         echo $cnt;
                        //echo "<BR>Stripe Payment Status : ".$result;
                        //echo "<BR>Stripe Response : ";
                        
                    }
                
                }
                catch (Exception $e) {
                  // Something else happened, completely unrelated to Stripe
                   $body = $e->getJsonBody();
                  $err  = $body['error'];
                  $error = $err['message'];
                  $result = "declined";
                  
                  $base_queryerror = ' INSERT INTO tbl_payments_error (OrderId, ErrorMsg) VALUES( "' . $orderid . '" ,  "' . $error . '") ';
                  $query_res = $db->query($base_queryerror);
                  $res = array( 'message_code' => 999, 'message_text' => $error);
                }
            }
        	else
        	{
        		$res = array( 'message_code' => 999, 'message_text' => 'Payment details not found on these order.');
        	}
    	}
    	return $response->withJson( $res, 200 );
    
    }
    
    
   
    








