<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//require 'stripe/Stripe.php';

/*
* Get all Runner data
*/
function tbx_runner_get_all( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$base_query = 'SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo FROM `tbl_registration`  WHERE RegsitrationRoleId = 3  AND IsDeleted= \'N\' ORDER BY `RegistrationId` DESC ';
	$result = $db->get_results( $base_query );
	if( isset( $result ) && !empty( $result ) )
	{
		return $response->withJson( $result );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 999 ,'message_text' => 'Runner details not found.') );
	}
	return $response->withJson( $res, 200 );

}


/*
* Get runner detail
*/

function tbx_user_get_edit_single( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	$db = database();
	$id = $request->getAttribute('id');
	$base_query = 'SELECT RegistrationId, RegsitrationRoleId,RegistrationName, RegistrationPhoneNo, RegistrationEmail FROM `tbl_registration` WHERE `RegistrationId` = "'.$id.'" AND `IsDeleted`= \'N\' ';
	$user = $db->get_row( $base_query );
	if( $user )
	{
		$user->Registration_Password = "";
		$res = array( 'message_code' => 1000, 'data_text' => $user );
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'User details not found.');
		
	}
	return $response->withJson( $res, 200 );
}


/* 
* Runner Delete
*/
function tbx_user_delete( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	$db = database();
	$body = $request->getParsedBody();
	//$user_id = get_current_user_id( $request );
	$id = $request->getAttribute('id');
	$deleted_by = "1";
	$deletedON = date('Y-m-d H:i:s');
	$base_query = ' UPDATE `tbl_registration` SET `IsDeleted`= "Y", `DeletedOn`="'.$deletedON.'", `DeletedBy`="'.$deleted_by.'" WHERE `RegistrationId` = "'.$id .'" ';
	
	if( $db->query( $base_query ))
	{
		$res = array( 'message_code' => 1000, 'message_text' => 'User deleted successfully.');
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'User not found.');
	}

	return $response->withJson( $res, 200 );
}

/*
* Runner Update
*/

function tbx_runner_update( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	$db = database();
	$body = $request->getParsedBody();

	$id = $body['id'];
	$name = $body['Name'];
	$contact_no = $body['PhoneNo'];
	$lastmodified_by= $body['lastmodifiedby'];
	$lastmodified_on= date('Y-m-d H:i:s');

	$base_query = 'UPDATE `tbl_registration` SET `RegistrationPhoneNo`= "'.$contact_no.'", `RegistrationName`= "'.$name.'", `LastModifiedBy`= "'.$lastmodified_by.'", `LastModifiedOn`= "'.$lastmodified_on.'" WHERE `RegistrationId` = "'.$id .'" ';
	$success = $db->query( $base_query );
	
	if($success)
	{
            $res = array( 'message_code' => 1000, 'message_text' => 'Runner profile updated successfully.');
    } 
    else
    {
       $res = array( 'message_code' => 999, 'message_text' => 'Failed to update Runner profile.');
    } 
	
	return $response->withJson( $res, 200 );
} 
//Mobile user API
/*
* $$SL: Invitation Send by Admin for Owner and Runner.
*/
function tbx_user_invitation( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$name = $body['Name'];
	$email = $body['Email'];
	$role_id = $body['Role'];
	$contact_no = $body['PhoneNo'];

	$createdon = date('Y-m-d H:i:s');

	if (!isset($body['CompanyName']))
		$company_name = "-";
	else
		$company_name = $body['CompanyName'];
	
	if (($name == null) || ($name==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Name cannot be blank.');
	else if (($email == null) || ($email==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Email cannot be blank.');
	else if (($contact_no == null) || ($contact_no==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Phone number cannot be blank.');
	else
	{
		$temp_pass = random_password();
		//echo $temp_pass;
		//$password = password_hash( sha1( strtolower($email) . ':' . $temp_pass ), PASSWORD_BCRYPT );
	
		$count = $db->get_var('SELECT count(*) FROM `tbl_registration` WHERE LCASE(`RegistrationEmail`) = LCASE("' . $email . '") ');
	
		if( $count > 0 )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'The email is already registered with ToolBX. Please login with your email and password.' );
		}
		else
		{

			if ($company_name != "-")
			{
				//insert inbto compnay
				// $base_query = 'INSERT INTO `tbl_companies`(CompanyName,CreatedBy,CreatedOn) VALUES ("'.$company_name.'", "1", "'.$createdon.'")';
				// if($db->query($base_query))
				// {
				// 	$company_id = $db->insert_id;
				// }
				$company_id = $company_name;

			}
			else
				$company_id = -1;

			$base_query = 'INSERT INTO tbl_registration (RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, CompanyId, CreatedOn) VALUES("' . $name . '", "' . $email . '","' . $contact_no . '", "' . $temp_pass . '", "' . $role_id . '", "'.$company_id.'", "'.$createdon.'")';

			if( $db->query( $base_query ) )
			{ 
				$user_id = $db->insert_id;		
				$url = generate_branch_link($user_id);
				$invitation_date = date('Y-m-d H:i:s');
				
				/*$Subject = "Welcome to ToolBX!";
				$Message = "Dear " . $name . ",\r\n\r\n";
				$Message .= "You are invited to ToolBX app. Please join the app using following link\r\n\r\n\r\n";
				$Message .= $url;
				$Message .= "\r\n\r\nKind Regards,\r\nToolBX Admin\r\n\r\n\r\n";
				SendSMTPMailCommon($email, $Subject, $Message );*/

				$a = send_invitation_mail( $url->url, $email, $user_id, $name, $temp_pass);
				return $response->withJson( $a, 200 );
				
				$upd_inv = 'UPDATE tbl_registration SET InvitationDate="' . $invitation_date . '", InvitationLink = "' . $url->url . '", 	InvitationActivated=0 where RegistrationId= ' . $user_id;
	
				$success = $db->query($upd_inv);
				if($success)
				{
					
					//$res =  array( 'message_code' => 1000, 'message_text' => 'Your invitation has been sent: ' . $email );
					$res =  array( 'message_code' => 1000, 'message_text' => 'Your invitation has been sent');
				}
			}
			else
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Database error! User insertion failed.', 'q' => $base_query);
			}
		}
	}
	
	return $response->withJson( $res, 200 );
}


/* check if company not exist then send invitation*/

function tbx_user_owner_invitation( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$name = $body['Name'];
	$email = $body['Email'];
	$role_id = 2;//$body['RoleId'];
	$contact_no = $body['PhoneNo'];	
	$company_name = $body['CompanyName'];
	$createdon = date('Y-m-d H:i:s');

	if (($name == null) || ($name==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Name cannot be blank.');
	else if (($email == null) || ($email==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Email cannot be blank.');
	else if (($contact_no == null) || ($contact_no==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Phone number cannot be blank.');
	else if (($company_name == null) || ($company_name==""))
		$res = array( 'message_code' => 999, 'message_text' => 'Company name cannot be blank.');
	else
	{ 
		$temp_pass = random_password();
		$count = $db->get_var('SELECT count(*) FROM `tbl_registration` WHERE LCASE(`RegistrationEmail`) = LCASE("' . $email . '") ');
	
		if( $count > 0 )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'The email is already registered with ToolBX. Please login with your email and password.' );
		}
		else
		{
			$count = $db->get_var('SELECT COUNT(CompanyId) FROM `tbl_companies` WHERE LCASE(`CompanyName`) = LCASE("'.$company_name.'")');		
			if( $count > 0 )
			{
				$res = array( 'message_code' => 999, 'message_text' => 'The Company is already Registered with ToolBX.' );
			}
			else
			{
				$base_query = 'INSERT INTO `tbl_companies`(CompanyName,CreatedBy,CreatedOn) VALUES ("'.$company_name.'", "1", "'.$createdon.'")';
				if($db->query($base_query))
				{
					$company_id = $db->insert_id;
				}

				$base_query = 'INSERT INTO tbl_registration (RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, CompanyId, CreatedOn) VALUES("' . $name . '", "' . $email . '","' . $contact_no . '", "' . $temp_pass . '", "' . $role_id . '", "'.$company_id.'", "'.$createdon.'")';
				
				if( $db->query( $base_query ) )
				{ 
					$user_id = $db->insert_id;		
					$url = generate_branch_link($user_id);
					$invitation_date = date('Y-m-d H:i:s');
					send_invitation_mail( $url->url, $email, $user_id, $name, $temp_pass);
					
					$upd_inv = 'UPDATE tbl_registration SET InvitationDate="' . $invitation_date . '", InvitationLink = "' . $url->url . '", 	InvitationActivated=0 where RegistrationId= ' . $user_id;
		
					$success = $db->query($upd_inv);
					if($success)
					{
						//$res = array( 'message_code' => 1000, 'message_text' => 'Invitation email is sent to the Email: ' . $email );
						$res = array( 'message_code' => 1000, 'message_text' => 'Your invitation has been sent' );
					}
				}
				else
				{
					$res = array( 'message_code' => 999, 'message_text' => 'Server error! User insertion failed.' );
				}

			}
			
		}
	}
	
	return $response->withJson( $res, 200 );
}



/*
* $$SL: Invitation Link clicked by Owner/Employee/Runner for activation.
*/
function tbx_user_invitation_data( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	
	$user_id = $request->getAttribute('id');

	//$$YA TempPass == 'Y'
	$base_query = $db->get_row('SELECT TempPass FROM `tbl_registration` WHERE RegistrationId= ' . $user_id);
	if($base_query->TempPass == 'Y')
	{

		$base_query = $db->get_row('SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, InvitationActivated, CompanyId FROM `tbl_registration` WHERE RegistrationId= ' . $user_id);
		
		if(!$base_query )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Invalid registration link. Please contact administrator.');
		}
		else
		{
			if ($base_query->InvitationActivated == "1")
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Link is already used. Please login with your details or contact administrator for further assitance.');
			}
			else
				$res =  array( 'message_code' => 1000, 'data_text' => $base_query);
			
		}
	}
	else
	{
		$temp_pass = random_password();
		$base_query = $db->get_row('SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegsitrationRoleId, InvitationActivated, CompanyId FROM `tbl_registration` WHERE RegistrationId= ' . $user_id);
		$base_query->RegistrationPassword = $temp_pass;

		sendConfirmationMail($base_query->RegistrationName, $base_query->RegistrationEmail);
		$res =  array( 'message_code' => 1000, 'data_text' => $base_query);
	}
	return $response->withJson( $res, 200 );
}


/*
* $$SL: Owner/Employee/Runner Forgot Password.
*/
function tbx_user_forgotpassword( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$email = $body["Email"];
	
	$lastmodified_on = date('Y-m-d H:i:s');
	$base_query = $db->get_row('SELECT IsDeleted FROM `tbl_registration` WHERE LCASE(RegistrationEmail)= LCASE("' . $email . '")' );
	if(!$base_query )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'This email address is not registred with ToolBx. Please contact administrator.');
	}
	else if($base_query->IsDeleted == "Y")
	{
		$res = array( 'message_code' => 999, 'message_text' => 'This email address is deactivated from ToolBx. Please contact administrator.');
	}
	else
	{
		$base_query = $db->get_row('SELECT RegistrationId, RegistrationName FROM `tbl_registration` WHERE LCASE(RegistrationEmail)= LCASE("' . $email . '")' );
		if(!$base_query )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'This email address is not registred with ToolBx. Please contact administrator.');
		}
		else
		{
			$password = random_password();
			/*echo $password;
			$new_password = password_hash( sha1( strtolower($email) . ':' . $password ), PASSWORD_BCRYPT );
			echo $new_password;*/
			$db->query( 'UPDATE tbl_registration SET  LastModifiedOn = "'.$lastmodified_on.'" ,TempPass =\'Y\', RegistrationPassword = "' . $password . '" WHERE RegistrationId = ' . $base_query->RegistrationId );
			send_password( $base_query->RegistrationName, $email, $password );
			$res = array( 'message_code' => 1000, 'message_text' => 'Password reset successfully. New password is sent via email to you.');
		}
	}
	return $response->withJson( $res, 200 );
}


/*YA*/
function tbx_user_changepassword( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	
	$user_id = $request->getAttribute('id');
	$body = $request->getParsedBody();
	$password = $body['password'];

	$lastmodified_on = date('Y-m-d H:i:s');

	if(($password == null) || ($password==""))
	$res = array( 'message_code' => 999, 'message_text' => 'Password cannot be blank.');
	else
	{
    	$base_query = $db->get_row('SELECT RegistrationId, RegistrationName FROM `tbl_registration` WHERE IsDeleted= \'N\' AND RegistrationId=' . $user_id);
    	if(!$base_query )
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'Invalid registration id. Cannot update new password. Please contact administrator.');
    	}
    	else
    	{
    		//$new_password = password_hash( sha1( strtolower($email) . ':' . $password ), PASSWORD_BCRYPT );
    		$db->query( 'UPDATE tbl_registration SET LastModifiedOn = "'.$lastmodified_on.'", TempPass =\'N\' , RegistrationPassword = "' . $password . '" WHERE RegistrationId = '. $base_query->RegistrationId );
    		$res = array( 'message_code' => 1000, 'message_text' => 'Password reset successfully.');
    	}
	}
	return $response->withJson( $res, 200 );
}

/*
* Change/Reset password (on user profile)
*/
function tbx_user_profileresetpassword( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();	

	$body = $request->getParsedBody();
	$user_id = $body['id'];
	$old_password = $body["OldPassword"];
	$new_password = $body["NewPassword"];

	$lastmodified_on = date('Y-m-d H:i:s');	

	$base_query = $db->get_row('SELECT RegistrationId, RegistrationPassword FROM `tbl_registration` WHERE RegistrationPassword= "'.$old_password.'" AND RegistrationId=' . $user_id);
	
	if($base_query )
	{
		$db->query( 'UPDATE tbl_registration SET LastModifiedOn = "'.$lastmodified_on.'", RegistrationPassword = "' . $new_password . '" WHERE RegistrationId = ' . $base_query->RegistrationId );
		$res = array( 'message_code' => 1000, 'message_text' => 'Password reset successfully.');
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Invalid old password. Cannot update new password.');
	}
	return $response->withJson( $res, 200 );
}
    
    

    function tbx_user_login( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	$body = $request->getParsedBody();
    	$email = $body["Email"];
    	$password = $body["Password"];
    	$phoneno = $body["PhoneNo"];
    	
    	//$source = $body["Source"];
    
    	/*$base_query = $db->get_row(' SELECT tord.RegistrationId, tord.RegistrationName, tord.RegistrationEmail,tord.TempPass,tbo.Orderid,tj.JobSiteId,tj.JobSiteName,
                                    tord.RegistrationPassword, tord.RegistrationPhoneNo, tord.RegsitrationRoleId, tord.CompanyId,tord.InvitationActivated,
                                    tord.IsDeleted, tord.CurrentOrderId,treg.IsLeaving,tbo.Delivered, tbo.IsCancel
                                    FROM `tbl_registration` tord
                                    LEFT JOIN tbl_order treg ON tord.currentorderid = treg.OrderId
                                    LEFT JOIN tbl_order tbo ON tbo.registrationid = tord.registrationid
                                    LEFT JOIN tbl_jobsite tj ON tj.JobSiteId = tbo.JobSiteId
                                    WHERE LCASE(tord.RegistrationEmail)= LCASE("' . $email . '") AND tord.RegistrationPhoneNo= "'.$phoneno.'" AND tord.IsDeleted=\'N\' ORDER BY orderid DESC
                                    LIMIT 1  ');*/

		$base_query = $db->get_row(' SELECT tord.RegistrationId, tord.RegistrationName, tord.RegistrationEmail,tord.TempPass,tbo.Orderid,
									tord.RegistrationPassword, tord.RegistrationPhoneNo, tord.RegsitrationRoleId, tord.CompanyId,tord.InvitationActivated,
									tord.IsDeleted, tord.CurrentOrderId,treg.IsLeaving,tbo.Delivered, tbo.IsCancel,

									CASE WHEN tj.display = \'Y\' THEN tj.JobSiteId END AS JobSiteId,
									CASE WHEN tj.display = \'Y\' THEN tj.JobSiteName END AS JobSiteName

									FROM `tbl_registration` tord
									LEFT JOIN tbl_order treg ON tord.currentorderid = treg.OrderId
									LEFT JOIN tbl_order tbo ON tbo.registrationid = tord.registrationid
									LEFT JOIN tbl_jobsite tj ON tj.JobSiteId = tbo.JobSiteId  

									WHERE LCASE(tord.RegistrationEmail) = LCASE("' . $email . '") AND tord.RegistrationPhoneNo= "'.$phoneno.'" 
                                    AND tord.IsDeleted=\'N\'  ORDER BY orderid DESC
                                    LIMIT 1  ');

    	
    	if(!$base_query )
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'This email address and mobile no. combination is not registred with ToolBx. Please contact administrator.');
    	}
    	else 
    	{
    	    if ($base_query->IsDeleted == "Y")
    	        $res = array( 'message_code' => 999, 'message_text' => 'This email address and mobile no. combination is deactivated by Toolbx Administrator. Please contact administrator.');
            else
            {
        		if ($base_query->RegistrationPassword == $password) 
        	   	{
        		  $base_query->token = generate_token( $base_query->RegistrationId);
        		  $base_query->RegistrationPassword = ""; 
        		    
        		    //displaying flag , but if null bydefault it sent 'N'
        		    if( ($base_query->IsLeaving == null) || ($base_query->IsLeaving == ""))
	    		    {  
				      $base_query->IsLeaving = "N";
	    		  	}
	    		  	// if order canceled
	    		  	if($base_query->IsCancel == "Y")
	    		  	{
	    		  		$base_query->Orderid = "";	    		  		
        		  	}
        		    
        		  $res = array( 'message_code' => 1000, 'data_text' => $base_query);
        		}
        	      
        		else
        			$res = array( 'message_code' => 999, 'message_text' => 'Password is not valid. Please reset password or contact administrator.');
                }
                
        }
    	
    	return $response->withJson( $res, 200 );
    }
    

/*
* Get User Profile Data
*/

function user_profiledata( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	$db = database();
	$id = $request->getAttribute('id');
	$base_query = 'SELECT RegistrationId, RegsitrationRoleId,RegistrationName, RegistrationPhoneNo, RegistrationEmail, CompanyId FROM `tbl_registration` WHERE `RegistrationId` = "'.$id.'" AND `IsDeleted`= \'N\' ';
	$user = $db->get_row( $base_query );
	if( $user )
	{
		$user->Registration_Password = "";
		$res = array( 'message_code' => 1000, 'data_text' => $user );
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'User details not found.');
		
	}
	return $response->withJson( $res, 200 );
}

/*
* Owner/Employee Profile Data (owner can insert credit information)
*/

function tbx_user_account_credit_details( Request $request, Response $response )
{
	require_once("./includes/Stripe/init.php");

	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	
	$db = database();
	$id = $request->getAttribute('id');	

	$base_query = $db->get_row( 'SELECT RegistrationId, RegsitrationRoleId FROM `tbl_registration`  WHERE RegistrationId = "'.$id.'" AND IsDeleted= \'N\' ' );
	if($base_query->RegsitrationRoleId == 2)
	{
		//CardHolderName, CardNumber, CardExpiryDate, CVC
		$base_query = 'SELECT RegistrationId, RegsitrationRoleId, RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, CompanyId, CardStripeTokenId  FROM `tbl_registration`  WHERE RegistrationId= "'.$id.'" AND IsDeleted= \'N\' ';
		$result = $db->get_results( $base_query );
		if (isset($result) && !empty($result))
		{
			foreach ($result as $var)
			{
				$var->CardNumber = "";
				$var->CardExpiryDate = "";
				$var->CVC = "";
				$var->CardHolderName = $var->RegistrationName;
				if ($var->CardStripeTokenId != null && $var->CardStripeTokenId != "")
				{
					\Stripe\Stripe::setApiKey(STRIPE_PUBLISHED_KEY); //Replace with your Secret Key

					 try 
                     {
						$customer = \Stripe\Customer::Retrieve(
						  array("id" => $var->CardStripeTokenId, "expand" => array("default_source"))
						);

						$var->CardNumber = "XXXXXXXXXXXX" . $customer->default_source->last4;
						$var->CardExpiryDate = $customer->default_source->exp_month . "/" . $customer->default_source->exp_year;
						$var->CVC = "XXX";
						$var->CardHolderName = $var->RegistrationName;
					}
					catch (Exception $e)
                    {
                      // Something else happened, completely unrelated to Stripe
                      $body = $e->getJsonBody();
                      $err  = $body['error'];
                      $error = $err['message'];
                      $res = array( 'message_code' => 999, 'message_text' => $error);
                    }

				}
				
			}
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
			$res = array( 'message_code' => 999, 'message_text' => 'No Data Found.' );
	}
	else
	{
		$base_query = 'SELECT RegistrationId, RegsitrationRoleId, RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword FROM `tbl_registration`  WHERE RegistrationId= "'.$id.'" AND IsDeleted= \'N\' ';
		$result = $db->get_results( $base_query );
		if (isset($result) && !empty($result))
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		else
			$res = array( 'message_code' => 999, 'message_text' => 'No Data Found.' );
	}
	return $response->withJson( $res, 200 );
}

/*
* update owner and employuee data
*/
function tbx_user_account_credit_details_update( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$id = $request->getAttribute('id');
	$body = $request->getParsedBody();
	$roleid = $body["RegsitrationRoleId"];
	$registrationname = $body["RegistrationName"];
	$cardholdername = $body["CardHolderName"];
	$phoneno = $body["RegistrationPhoneNo"];
	// $cardno = $body["CardNumber"];
	// $cardexpdate = $body["CardExpiryDate"];
	// $cvc = $body["CVC"];

	
	$lastmodifiedON = date('Y-m-d H:i:s');

	if($roleid == 2)
	{
		//, `CardNumber`="'.$cardno.'", `CardExpiryDate`="'.$cardexpdate.'", `CVC`="'.$cvc.'",

		$base_query = $db->query(' UPDATE `tbl_registration` SET `RegistrationName` = "'.$registrationname.'", `CardHolderName`="'.$cardholdername.'", `RegistrationPhoneNo`="'.$phoneno.'", `LastModifiedOn` = "'.$lastmodifiedON.'" WHERE `RegistrationId` = "'.$id.'" AND IsDeleted = \'N\' ');
				
		if ($base_query === FALSE) 
    	{
       	    $res = array( 'message_code' => 999, 'message_text' => 'Try again.');
        }
        else if ($base_query == 0)
        {
    		$res = array( 'message_code' => 1000, 'data_text' => 'No any changes');
        }
    	else
    	{
    	    $res = array( 'message_code' => 1000, 'message_text' => 'Profile updated successfully.');
    	}
	}
	else
	{
		$base_query = $db->query(' UPDATE `tbl_registration` SET `RegistrationName` = "'.$registrationname.'", `RegistrationPhoneNo` = "'.$phoneno.'", `LastModifiedOn` = "'.$lastmodifiedON.'" WHERE `RegistrationId` = "' . $id .'" AND IsDeleted = \'N\' ');
		
		if(  $base_query)
		{
			$res = array( 'message_code' => 1000, 'message_text' => 'Profile updated successfully.');
		}
		else if ($base_query == 0)
        {
    		    $res = array( 'message_code' => 1000, 'data_text' => 'No any changes');
        }
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Error while updating profile.');
		}
	}

	return $response->withJson( $res, 200 );
}

    /*
    * Runner Online offline
    */
    
    function tbx_runner_onlineoffline( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	$registration_id = $request->getAttribute('id');
    	$body = $request->getParsedBody();
    	$status = $body["IsOnline"];
    
    	$lastmodified_on = date('Y-m-d H:i:s');

	    if(($status == null) || ($status==""))
		   	$res = array( 'message_code' => 999, 'message_text' => 'IsOnline(Y/N) value can not be blank.');
    	else
    	{
        	$base_query = ' UPDATE `tbl_registration` SET LastModifiedOn="'. $lastmodified_on .'",`IsOnline` = "'.$status.'" WHERE `RegistrationId` = "'.$registration_id.'" ';
        	$result = $db->query( $base_query );
        	if ($result === FALSE) 
        	{
           	    $res = array( 'message_code' => 999, 'message_text' => 'Try again.');
            }
            else if ($result == 0)
            {
                   	$base_query = $db->get_row('SELECT IsOnline FROM `tbl_registration` WHERE RegistrationId= "'.$registration_id.'" ');
        		    $res = array( 'message_code' => 1000, 'data_text' => $base_query);
            }
        	else
        	{	
        		$base_query = $db->get_row('SELECT IsOnline FROM `tbl_registration` WHERE RegistrationId= "'.$registration_id.'" ');
        		$res = array( 'message_code' => 1000, 'data_text' => $base_query);
        	}
    	}
    	return $response->withJson( $res, 200 );
    }


    /* 070617
    * All  online offline used when owner/employee place any order after 5pm
    */
    
    function tbx_allrunner_onlineoffline( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    
       	$base_query = $db->get_row(' SELECT IsOnline FROM `tbl_registration` WHERE IsDeleted = \'N\' AND IsOnline=\'Y\'  and RegsitrationRoleId =3 ');
       
       	//$data = $db->get_row($base_query);
        if(!$base_query)
        {
            $res = array( 'message_code' => 999, 'data_text' => 'Network conjection.');
        }
        if($base_query)
        {
            $res = array( 'message_code' => 1000, 'data_text' => $base_query);
        }
        else
        {
            $base_que = $db->get_row(' SELECT IsOnline FROM `tbl_registration` WHERE IsDeleted = \'N\' AND IsOnline=\'N\'  and RegsitrationRoleId =3');
            $res = array( 'message_code' => 1000, 'data_text' => $base_que);
        } 
    	return $response->withJson( $res, 200 );
    }
    
   
    
    function tbx_user_card_details_available( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    	
    	$db = database();
    	$id = $request->getAttribute('id');	
    
    	$base_query = $db->get_row( 'SELECT RegistrationId, RegsitrationRoleId, CompanyId FROM `tbl_registration`  WHERE RegistrationId = "'.$id.'" AND IsDeleted= \'N\' ' );
    	if($base_query->RegsitrationRoleId == 2)
    	{
    		$CardStripeTokenId = $db->get_var('select CardStripeTokenId from  tbl_registration where RegistrationId =' . $id);

    		if (isset($CardStripeTokenId) && !empty($CardStripeTokenId) && $CardStripeTokenId != null)
    			$result1['available'] = "Y";
    		else
    			$result1['available'] = "N";
    		
    		$res = array( 'message_code' => 1000, 'data_text' => $result1 );
    		// $base_query = 'select least(ifnull(CardNumber,0),ifnull(CVC,0),ifnull(CardExpiryDate,0)) as Resultant from  tbl_registration where RegistrationId = "'.$id.'"  ';
    		// $result = $db->get_row( $base_query );
    		// if(!$result )
    		// {
    		// 	$res = array( 'message_code' => 999, 'message_text' => 'Data Not Found.');
    		// }
	     //    if($result->Resultant > 0)
    		// {
    		//     $result1['available'] = "Y";
    		//     $res = array( 'message_code' => 1000, 'data_text' => $result1 );
    		// }
    		// else
    		// {
    		//     $result2['available'] = "N";
    		// 	$res = array( 'message_code' => 1000, 'data_text' => $result2 );
    		// }
    	}
    	else
    	{
    	   
    		$base_queryres = 'SELECT tr.RegistrationId FROM tbl_registration AS tr RIGHT JOIN tbl_companies as tc ON tc.CompanyId=tr.CompanyId WHERE tr.CompanyId = "'.$base_query->CompanyId.'" ';
    		$baseresult = $db->get_row($base_queryres);
    		

    		$CardStripeTokenId = $db->get_var('select CardStripeTokenId from  tbl_registration where RegistrationId =' . $baseresult->RegistrationId);

    		if (isset($CardStripeTokenId) && !empty($CardStripeTokenId) && $CardStripeTokenId != null)
    			$result1['available'] = "Y";
    		else
    			$result1['available'] = "N";
    		
    		$res = array( 'message_code' => 1000, 'data_text' => $result1 );

    		// $base_query2 = 'select least(ifnull(CardNumber,0),ifnull(CVC,0),ifnull(CardExpiryDate,0))as Resultant from  tbl_registration where RegistrationId = "'.$baseresult->RegistrationId.'"  ';
    		// $result = $db->get_row( $base_query2 );
    		// if(!$result )
    		// {
    		// 	$res = array( 'message_code' => 999, 'message_text' => 'Data Not Found.');
    		// }
    		//  if($result->Resultant > 0)
    		// {
    		//     $result1['available'] = "Y";
    		//     $res = array( 'message_code' => 1000, 'data_text' => $result1 );
    		// }
    		// else
    		// {
    		//     $result2['available'] = "N";
    		// 	$res = array( 'message_code' => 1000, 'data_text' => $result2 );
    		// }
    	}
    	return $response->withJson( $res, 200 );
    }
    











