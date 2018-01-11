<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/*
*
*  Save notification token owner/employee
*/

    function tbx_notification_token_save(Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	$body = $request->getParsedBody();
    
    	$registrationid = $body['RegistrationId'];
    	$token = $body['Token'];
    	
        $lastmodified_on = date('Y-m-d H:i:s');

    	if (($registrationid == null) || ($registrationid==""))
		    $res = array( 'message_code' => 999, 'message_text' => 'Registration id cannot be blank.');
	    else if (($token == null) || ($token==""))
		    $res = array( 'message_code' => 999, 'message_text' => 'Token cannot be blank.');
    	else
    	{
    		$base_query = 'UPDATE tbl_registration SET Token= "" WHERE Token= "' . $token . '"';
        	$success = $db->query( $base_query );
    	
        	$base_query = 'UPDATE tbl_registration SET Token= "' . $token . '", LastModifiedOn="'. $lastmodified_on .'" WHERE RegistrationId = ' . $registrationid;
         	$success = $db->query( $base_query );
        // 	if($success)
        // 	{
                $res = array( 'message_code' => 1000, 'message_text' => 'Token saved successfully.');
            // } 
            // else
            // {
            //   $res = array( 'message_code' => 999, 'message_text' => 'Failed to save Token.');
            // }
    	}
    	return $response->withJson( $res, 200 );
    } 

	
	
    /*
    *
    * All Notification Message
    */
	
	function tbx_order_notification_all( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
		$db = database();
		$order_id = $request->getAttribute('id'); //orderid
		$body = $request->getParsedBody();
    	$OrderStatus = $body['Type'];
    	
    	if (($OrderStatus == null) || ($OrderStatus==""))
		    $res = array( 'message_code' => 999, 'message_text' => 'Order type cannot be blank.');
    	else
    	{
    		$query_result = $db->get_row(' SELECT RegistrationId FROM `tbl_order`  WHERE OrderId="'.$order_id.'" ');
    		
             $RunnerId = $db->get_var("SELECT RunnerId from tbl_runner_order where OrderId=" . $order_id);

    		if(!$query_result)
    		{
    			$res = array( 'message_code' => 999, 'message_text' => 'Please try again' );
    		}
    		elseif ($OrderStatus == 'accept') 
    		{
    		   //echo $OrderStatus . "1234<br/>";
    		    $query = "SELECT tr.Token AS token, tr.CompanyId as CompanyId, tbo.JobsiteId as JobsiteId  FROM tbl_order AS tbo, tbl_registration AS tr WHERE tr.RegistrationId = tbo.RegistrationId AND tbo.RegistrationId = " . $query_result->RegistrationId . " AND tbo.OrderId=" . $order_id . " AND tbo.IsAccepted='Y'";
    			 //echo $query . "<br/>";
    			$base_query = $db->get_row($query);
    		 		
    			$registration_ids = $base_query->token;
    		    //echo $registration_ids . "<br/>";
    			
    			$message = array(
    								'title'=>'ToolBX',
    								'image'=>'',
    								'message'=>'Your order has been received by the driver and will arrive within 2 hours.'
    							);

                notification_insert($base_query->CompanyId, $query_result->RegistrationId, $RunnerId, $order_id, $base_query->JobsiteId, "A RUNNER HAS ACCEPTED YOUR ORDER.", 0);
    			
    			$data = send_notification($registration_ids,$message);
    			$res = array('message_code' => 1000, 'message_text' => $data);
    		}
    		elseif ($OrderStatus=='cancel') 
    		{
    			$base_query = $db->get_row(' SELECT tr.Token AS token, tr.CompanyId as CompanyId, tbo.JobsiteId as JobsiteId   FROM `tbl_order` AS tbo JOIN `tbl_registration` AS tr WHERE tr.RegistrationId = tbo.RegistrationId AND tbo.RegistrationId = "'.$query_result->RegistrationId.'" AND tbo.OrderId="'.$order_id.'" AND tbo.IsAccepted=\'N\' ');
    			$registration_ids = $base_query->token;
    			$message = array(
    								'title'=>'ToolBX',
    								'image'=>'',
    								'message'=>'The driver has cancelled the order. Another Driver will take the order.'
    							);
    			
                notification_insert($base_query->CompanyId, $query_result->RegistrationId, $RunnerId, $order_id, $base_query->JobsiteId, "A RUNNER HAS CANCELLED YOUR ORDER. ANOTHER RUNNER WILL TAKE THE ORDER.", 0);

    			$data = send_notification($registration_ids, $message);
    			$res = array('message_code' => 1000, 'message_text' => $data);
    		}
    		elseif ($OrderStatus=='leaving') 
    		{
    			$base_query = $db->get_row(' SELECT tr.Token AS token, tr.CompanyId as CompanyId, tbo.JobsiteId as JobsiteId  FROM `tbl_order` AS tbo JOIN `tbl_registration` AS tr WHERE tr.RegistrationId = tbo.RegistrationId AND tbo.RegistrationId = "'.$query_result->RegistrationId.'" AND tbo.OrderId="'.$order_id.'" AND tbo.IsLeaving=\'Y\' ');
    			$registration_ids = $base_query->token;
    			$message = array(
    								'title'=>'ToolBX',
    								'image'=>'',
    								'message'=>'Your delivery has left the store and it\'s on its way.'
    							);

                notification_insert($base_query->CompanyId, $query_result->RegistrationId, $RunnerId, $order_id, $base_query->JobsiteId, "YOUR RUNNER IS LEAVING THE STORE.", 0);

    			$data = send_notification($registration_ids,$message);
    			$res = array('message_code' => 1000, 'message_text' => $data);
    		}
    		elseif ($OrderStatus=='delivered') 
    		{
    			
        			/*$base_query = $db->get_row(' SELECT tr.Token AS token, tj.JobSiteName FROM `tbl_order` AS tbo JOIN `tbl_registration` AS tr join tbl_jobsite as tj WHERE tj.JobSiteId = tbo.JobSiteId AND tr.RegistrationId = tbo.RegistrationId AND tbo.RegistrationId = "'.$query_result->RegistrationId.'" AND tbo.OrderId= "'.$order_id.'" AND tbo.Delivered = \'Y\' ');
        			$registration_ids = $base_query->token;
        			$jobsiteName = $base_query->JobSiteName;
        			$message = array(
        								'title'=>'ToolBX',
        								'image'=>'',
        								'message'=>'Your order from Toolbx has been delivered to '.$jobsiteName.'.' 
        							);
        			
        			$data = send_notification($registration_ids, $message);
        			$res = array('message_code' => 1000, 'message_text' => $data);*/
        			
        			/************* owner employee notification *******************/
        			
        			
        			    $base_query = $db->get_row(' SELECT tr.CompanyId,tbo.JobSiteId FROM tbl_registration AS tr INNER JOIN tbl_order AS tbo ON tbo.RegistrationId = tr.RegistrationId WHERE tbo.Orderid = "' . $order_id . '" ');
                                                    
                        $result_trj1 = ' SELECT tr.RegistrationId, tr.RegistrationEmail, tj.JobSiteName, tr.token,tbo.CreatedBy FROM tbl_registration AS tr 
                                JOIN tbl_jobsite AS tj ON tj.OwnerId = tr.RegistrationId
                                JOIN tbl_order AS tbo ON tbo.JobSiteId = tj.JobSiteId
                                WHERE  tr.RegsitrationRoleId = 2 AND tj.JobSiteId = "'. $base_query->JobSiteId .'" AND tr.CompanyId= "'. $base_query->CompanyId .'" AND tbo.Delivered = \'Y\' and tbo.Orderid = "' . $order_id . '"'; 
                                
                        $result_trj =  $db->get_row($result_trj1);

                        if(($result_trj == null) || ($result_trj == " "))
                        {
                            $res = array('message_code' => 1000, 'message_text' => 'Values are null.');
                        }
                        else
                        {

                            $registration_ids = $result_trj->token;
                            $ntoken = $registration_ids;
                            $jobsiteName = $result_trj->JobSiteName;
                            $message = array(
                                                'title'=>'ToolBX',
                                                'image'=>'',
                                                'message'=>' An order with ToolBx has been delivered to '.$result_trj->JobSiteName.'.'
                                                
                                            );


                            $data = send_notification($registration_ids,$message);
                            $res = array('message_code' => 1000, 'message_text' => $data);
                        }

                        notification_insert($base_query->CompanyId, $result_trj->CreatedBy, $RunnerId, $order_id, $base_query->JobSiteId, "YOUR ORDER HAS BEEN DELIVERED SUCCESSFULLY.", 1);

                        /****************/

                        $base_query = $db->get_row(' SELECT tr.Token AS token, tj.JobSiteName FROM `tbl_order` AS tbo 
                                                    JOIN `tbl_registration` AS tr join tbl_jobsite as tj WHERE tj.JobSiteId = tbo.JobSiteId 
                                                    AND tr.RegistrationId = tbo.RegistrationId 
                                                    AND tbo.RegistrationId = "'.$query_result->RegistrationId.'" AND tbo.OrderId= "'.$order_id.'" 
                                                    AND tbo.Delivered = \'Y\' ');
                        
                        $registration_ids = $base_query->token;
                        $jobsiteName = $base_query->JobSiteName;
                        $message = array(
                                            'title'=>'ToolBX',
                                            'image'=>'',
                                            'message'=>' Your order from Toolbx has been delivered to '.$jobsiteName.'.' 
                                        );
                        

                        $data = send_notification($registration_ids, $message);
                        $res = array('message_code' => 1000, 'message_text' => $data);
     			
        			 tbx_order_export_PDF_invoice($order_id, $result_trj->RegistrationEmail);
                    

        			
        			/************************************************************/
        			
        			
        			
    		}
    		else
    			$res = array('message_code' => 999, 'message_text' => 'Somthing went wrong! please try later.');
    	}
		return $response->withJson( $res, 200 );
	}
	
	/*
	* Employee placeing order notification
	*/
	function employee_creating_order_notification(Request $request, Response $response)
	{
	    $res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
		$db = database();
		$order_id = $request->getAttribute('id');
	    
	    if (($order_id == null) || ($order_id==""))
		    $res = array( 'message_code' => 999, 'message_text' => 'Order id cannot be blank.');
    	else
    	{
            $base_query = $db->get_row(' SELECT tbo.RegistrationId, tr.RegsitrationRoleId, tbo.TotalAmount FROM `tbl_order` AS tbo join tbl_registration AS tr WHERE tbo.RegistrationId = tr.RegistrationId AND tbo.OrderId = "'.$order_id.'" ');
    		//echo $base_query;
    		if($base_query->RegsitrationRoleId == 4)
    		{
    		   
    		    $result_createdby = $db->get_row(' SELECT tr.RegistrationId, tr.createdby FROM `tbl_registration` as tr
                                RIGHT JOIN `tbl_order` AS tbo ON tbo.RegistrationId = tr.RegistrationId
                                WHERE tbo.orderid = "'.$order_id.'" ');
                                
    		    $result_trj = $db->get_row(' SELECT tr.token, tr.RegistrationId, tj.JobSiteName FROM `tbl_jobsite` as tj
                                LEFT JOIN `tbl_registration` AS tr ON tr.RegistrationId = tj.OwnerId
                                WHERE tr.RegistrationId = '.$result_createdby->createdby);
                                
    		    if(($result_trj == null) || ($result_trj == " "))
    		    {
    		        $res = array('message_code' => 999, 'message_text' => 'Values are null.');
    		    }
    		    else
    		    {
    		        $registration_ids = $result_trj->token;
        			$jobsiteName = $result_trj->JobSiteName;
        			$message = array(
        								'title'=>'ToolBX',
        								'image'=>'',
        								'message'=>'An order with Toolbx has been placed for $ '.$base_query->TotalAmount.'.'
        								
        							);
        			$data = send_notification($registration_ids,$message);
        			$res = array('message_code' => 1000, 'message_text' => $data);
    		    }
    		}
    		else
    		{
    		    $res = array('message_code' => 999, 'message_text' => 'These user not a employee.');
    		}
	   }
	   return $response->withJson( $res, 200 );
	}
	


    function notification_insert($CompanyId, $RegistrationId, $RunnerId, $Orderid, $JobsiteId, $Messageline, $NotificationsType)
    {
         $db = database();
        $sSQL = "INSERT INTO tbl_notifications(CompanyId, RegistrationId, RunnerId, Orderid, JobsiteId, Messageline, NotificationsUnixTime, ReadStatus, DeleteStatus, NotificationsType, OrderRating) VALUES (" . $CompanyId . "," . $RegistrationId . "," . $RunnerId . "," . $Orderid . "," . $JobsiteId . ",'" . $Messageline . "', UNIX_TIMESTAMP(Now()),0,0," . $NotificationsType . ",0)";

        //echo $sSQL . "<br/>"; 

        if ($db->query($sSQL))
            return true;
        else
            return false;
    }

    
    function tbx_notification_list(Request $request, Response $response)
    {
        $res = array('message_code'=>999, 'message_text'=>'Functional part is commented.');
        $db = database();
        $RegistrationId = $request->getAttribute('id');
        
        if (($RegistrationId == null) || ($RegistrationId==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Please provide the user Id.');
        else
        {
            $userRole = $db->get_var("SELECT RegsitrationRoleId FROM tbl_registration WHERE RegistrationId =" . $RegistrationId);

            $sSQL = "SELECT id, tn.CompanyId, tn.RegistrationId, RunnerId, Orderid, tn.JobsiteId, Messageline, ReadStatus, DeleteStatus, NotificationsType, OrderRating, tr.RegistrationName, tj.JobSiteName, tj.Address, tj.PostalCode, NotificationsUnixTime  FROM tbl_notifications AS tn, tbl_registration AS tr, tbl_jobsite AS tj WHERE tr.RegistrationId = tn.RegistrationId AND tj.JobSiteId = tn.JobsiteId AND DeleteStatus !=1";

            //if ($userRole == 4)
                $sSQL =  $sSQL . " and tn.RegistrationId =" . $RegistrationId;
            // else if ($userRole == 2)
            //     $sSQL = $sSQL . " and tn.CompanyId =" . $RegistrationId;

            $sSQL = $sSQL . " Order by id DESC LIMIT 0, 20";

            //echo $sSQL . "<br/>";
            $result = $db->get_results($sSQL);

            if ( isset( $result ) && !empty( $result ) )
                $res = array( 'message_code' => 1000, 'data_text' => $result);
            else
                $res = array( 'message_code' => 999, 'message_text' => 'No notifications for you.');
        }
       
       return $response->withJson( $res, 200 );
    }


     function tbx_notification_markasread(Request $request, Response $response)
    {
        $res = array('message_code'=>999, 'message_text'=>'Functional part is commented.');
        $db = database();
        $id = $request->getAttribute('id');
        
        if (($id == null) || ($id==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Please provide the notification Id.');
        else
        {
            
            $sSQL = "UPDATE tbl_notifications SET ReadStatus=1 WHERE id =" . $id;

            $result = $db->query($sSQL);
            $res = array( 'message_code' => 1000, 'message_text' => "Notification marked as read.");
        }
       
       return $response->withJson( $res, 200 );
    }

    function tbx_notification_markasdeleted(Request $request, Response $response)
    {
        $res = array('message_code'=>999, 'message_text'=>'Functional part is commented.');
        $db = database();
        $id = $request->getAttribute('id');
        
        if (($id == null) || ($id==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Please provide the notification Id.');
        else
        {
            
            $sSQL = "UPDATE tbl_notifications SET DeleteStatus=1 WHERE id =" . $id;

            $result = $db->query($sSQL);
            $res = array( 'message_code' => 1000, 'message_text' => "Notification is deleted.");
        }
       
       return $response->withJson( $res, 200 );
    }
    
    function tbx_notification_updaterating(Request $request, Response $response)
    {
        $res = array('message_code'=>999, 'message_text'=>'Functional part is commented.');
        $db = database();
        $id = $request->getAttribute('id');
        $rating = $request->getAttribute('rating');
        
        
        if (($id == null) || ($id==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Please provide the notification Id.');
        else if (($rating == null) || ($rating==""))
            $res = array( 'message_code' => 999, 'message_text' => 'Please provide the order rating.');
        else
        {
            
            $sSQL = "UPDATE tbl_notifications SET OrderRating=" . $rating . " WHERE id =" . $id;

            $result = $db->query($sSQL);
            $res = array( 'message_code' => 1000, 'message_text' => "Order delivery rating updated.");
        }
       
       return $response->withJson( $res, 200 );
    }

    


