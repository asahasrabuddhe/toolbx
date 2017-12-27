<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 
	/*
	* Get Companies data
	*/
	function tbx_get_all_company( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$base_query = 'SELECT CompanyName, Description FROM `tbl_companies` WHERE display= \'Y\' ORDER BY `CompanyId` DESC ';
		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			return $response->withJson( $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'data_text'=>'Company DEtails Not Found' ) );
		}
		return $response->withJson( $res, 200 );

	}

    function tbx_get_edit_company_admin( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	$id = $request->getAttribute('id');
    	$base_query = 'SELECT CompanyId, CompanyName FROM tbl_companies WHERE CompanyId= "' . $id . '" AND display = \'Y\' ';
    	$result = $db->get_row( $base_query );
    	if( $result )
    	{		
    		$res = array( 'message_code' => 1000, 'data_text' => $result );
    	}
    	else
    	{
    		$res = array( 'message_code' => 999, 'message_text' => 'Company details not found.');
    		
    	}
    	return $response->withJson( $res, 200 );
    } 

    function tbx_update_company_admin( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();
    	$body = $request->getParsedBody();
    	$id = $body['id'];
    	$Companyname = $body['CompanyName'];	
    	$lastmodified_by= $body['lastmodifiedby'];
    	$lastmodified_on= date('Y-m-d H:i:s');
    	$count = $db->get_var('SELECT COUNT(CompanyId) FROM `tbl_companies` WHERE LCASE(`CompanyName`) = LCASE("'.$Companyname.'")');		
		if( $count > 0 )
		{
			$res = array( 'message_code' => 999, 'message_text' => 'The Company is already Registered with ToolBX.' );
		}
		else
		{
	    	$base_query = ' UPDATE tbl_companies SET CompanyName= "' . $Companyname . '", LastModifiedBy = "'.$lastmodified_by.'", LastModifiedOn = "'.$lastmodified_on.'" WHERE CompanyId= "'.$id .'" ';
	    	$success = $db->query( $base_query );
	    	
	    	if($success)
	    	{
	                $res = array( 'message_code' => 1000, 'message_text' => 'Company updated successfully.');
	        } 
	        else
	        {
	           $res = array( 'message_code' => 999, 'message_text' => 'Failed to update company.');
	        }
        }	
    	return $response->withJson( $res, 200 );
    } 

	/*
	* admin company data for emp invitation and list
	*/
	function tbx_get_all_company_admin( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		// spacify company list if this is in registration table in where clause
		$base_query = 'SELECT CompanyId, CompanyName FROM `tbl_companies` WHERE display= \'Y\' ORDER BY `CompanyId` DESC ';
		//$base_query = 'SELECT DISTINCT(tr.CompanyId), tc.CompanyId, tc.CompanyName FROM `tbl_companies` AS tc JOIN tbl_registration AS tr ON tr.CompanyId = tc.CompanyId WHERE tc.display= \'Y\' ORDER BY tc.CompanyId DESC  ';
		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Company Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	}
 
	/* 
	* company Delete
	*/
	function tbx_company_delete( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$id = $request->getAttribute('id');
		$deleted_by = "1";
		$deletedON = date('Y-m-d H:i:s');
		$base_query = ' UPDATE `tbl_companies` SET `display`= \'N\', `DeletedOn`="'.$deletedON.'", `DeletedBy`="'.$deleted_by.'" WHERE `CompanyId` = "'.$id.'" ';
		if( $db->query( $base_query ))
		{
		    $base_query = 'UPDATE tbl_registration SET IsDeleted= \'Y\' where CompanyId = "'.$id.'" ';
		    $db->query( $base_query );
			$res = array( 'message_code' => 1000, 'message_text' => 'Company deleted successfully.');
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Company not found.');
		}
		return $response->withJson( $res, 200 );
	}
 
 
	/*
	* Get all owner data
	*/

	/*function tbx_owner_get_all( Request $request, Response $response )
	{
		$db = database();
		$base_query = 'SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo FROM `tbl_registration`  WHERE RegsitrationRoleId = 2  AND IsDeleted= \'N\' ORDER BY `RegistrationId` DESC ';
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
	*/

	function tbx_owner_get_all( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$cmpid = $request->getAttribute('cmpid');

		//$base_query = 'SELECT RegistrationId, RegistrationName,RegistrationEmail, RegistrationPhoneNo, CompanyId FROM `tbl_registration`  WHERE RegsitrationRoleId = 2  AND CompanyId = "'.$cmpid.'" AND IsDeleted= \'N\' ORDER BY `RegistrationId` DESC ';
		$base_query = 'SELECT tr.RegistrationId, tr.RegistrationName, tr.RegistrationEmail, tr.RegistrationPhoneNo, tc.CompanyId, tc.CompanyName 
						FROM `tbl_registration` AS tr 
						JOIN tbl_companies AS tc ON tc.CompanyId = tr.companyId
						WHERE tr.RegsitrationRoleId = 2  AND tr.CompanyId =  "'.$cmpid.'" AND tr.IsDeleted= \'N\' ORDER BY tr.`RegistrationId` DESC ';
		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		/*****/
		elseif ($result == 0) 
		{
			$base_query = '	SELECT CompanyId, CompanyName FROM tbl_companies WHERE CompanyId = "' . $cmpid . '" AND display=\'Y\' ';				
			$result = $db->get_results( $base_query );
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}

		/****/
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Owner Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	}

	/*
	* Get all employee data
	*/
	/*function tbx_employee_get_all( Request $request, Response $response )
	{
		$db = database();
		$base_query = 'SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo FROM `tbl_registration`  WHERE RegsitrationRoleId = 4  AND IsDeleted= \'N\' ORDER BY `RegistrationId` DESC ';
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

	}*/

	function tbx_employee_get_all( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
		
		$db = database();
		$cmpid = $request->getAttribute('cmpid');
		//$base_query = 'SELECT RegistrationId, RegistrationName, RegistrationEmail, RegistrationPhoneNo FROM `tbl_registration` WHERE RegsitrationRoleId = 4 AND IsDeleted= \'N\' AND CompanyId= "' . $cmpid . '"  ORDER BY `RegistrationId` DESC ';
		$base_query = 'SELECT tr.RegistrationId, tr.RegistrationName, tr.RegistrationEmail, tr.RegistrationPhoneNo, tc.CompanyName, tc.CompanyId FROM `tbl_registration` tr JOIN tbl_companies AS tc ON tc.CompanyId = tr.CompanyId WHERE RegsitrationRoleId = 4 AND IsDeleted= \'N\' AND tr.CompanyId= "' . $cmpid . '" ORDER BY RegistrationId DESC ';
		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result))
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Employee Details Not Found.');
		}
		return $response->withJson( $res, 200 );

	}


	/*
	* Get owner detail
	*/

	function tbx_owner_get_edit_single( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$id = $request->getAttribute('id');
		//$base_query = 'SELECT tr.RegistrationId, tr.RegistrationName, tr.RegistrationEmail, tr.RegsitrationRoleId, tr.RegistrationPhoneNo, tc.CompanyName FROM `tbl_registration` AS tr LEFT JOIN `tbl_companies` AS tc  ON tr.RegistrationId = tc.OwnerId WHERE tr.RegistrationId= "' . $id . '" AND tr.IsDeleted= \'N\' ';
		$base_query = 'SELECT tr.RegistrationId, tr.RegistrationName, tr.RegistrationEmail, tr.RegsitrationRoleId, tr.RegistrationPhoneNo, tr.CompanyId, tc.CompanyName FROM `tbl_registration` AS tr LEFT JOIN `tbl_companies` AS tc  ON tr.CompanyId = tc.CompanyId WHERE tr.RegistrationId= "' . $id . '" AND tr.IsDeleted= \'N\' ';
		$user = $db->get_row( $base_query );
		if( $user )
		{
			$user->Registration_Password = "";
			$res = array( 'message_code' => 1000, 'data_text' => $user );
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Owner details not found.');
			
		}
		return $response->withJson( $res, 200 );
	}

	/*
	* owner Update
	*/
	function tbx_owner_update( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$id = $body['id'];
		$name = $body['Name'];
		$contact_no = $body['PhoneNo'];
		$company = $body['Company'];
		$lastmodified_by= $body['lastmodifiedby'];
		$lastmodified_on= date('Y-m-d H:i:s');
		$base_query = ' UPDATE tbl_companies tc INNER JOIN tbl_registration tr ON tc.CompanyId = tr.CompanyId SET tr.RegistrationName="'.$name.'", tr.RegistrationPhoneNo = "'.$contact_no.'", tc.CompanyName = "'.$company.'", tr.LastModifiedBy = "'.$lastmodified_by.'", tr.LastModifiedOn = "'.$lastmodified_on.'" WHERE tr.RegistrationId= "'.$id .'" ';
		$success = $db->query( $base_query );
		
		if($success)
		{
	            $res = array( 'message_code' => 1000, 'message_text' => 'Owner profile updated successfully.');
	    } 
	    else
	    {
	       $res = array( 'message_code' => 999, 'message_text' => 'Failed to update Owner profile.');
	    }	
		return $response->withJson( $res, 200 );
	} 

	/*
	* Get employee detail
	*/
	function tbx_employee_get_edit_single( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$id = $request->getAttribute('id');
			
		$base_query = ' SELECT tr.RegistrationId, tr.RegistrationName, tr.RegistrationEmail, tr.RegsitrationRoleId, tr.RegistrationPhoneNo, tc.CompanyId, tc.CompanyName FROM `tbl_registration` tr , `tbl_companies` tc WHERE tc.CompanyId = tr.CompanyId AND tr.RegistrationId="' . $id . '" AND tr.IsDeleted= \'N\' ';
		$user = $db->get_row( $base_query );
		if( $user )
		{	
			$user->Registration_Password = "";
			$res = array( 'message_code' => 1000, 'data_text' => $user );
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Employee details not found.');
			
		}
		return $response->withJson( $res, 200 );
	}

	/*
	* employee Update
	*/
	function tbx_employee_update( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$id = $body['id'];
		$name = $body['Name'];
		$contact_no = $body['PhoneNo'];
		$lastmodified_by= $body['lastmodifiedby'];
		$lastmodified_on= date('Y-m-d H:i:s');
		$base_query ='UPDATE `tbl_registration` SET `RegistrationName`="'.$name.'", `RegistrationPhoneNo`= "'.$contact_no.'", `LastModifiedBy` = "'.$lastmodified_by.'", `LastModifiedOn` = "'.$lastmodified_on.'" WHERE RegistrationId= "'.$id .'" ';
		$success = $db->query( $base_query );
		
		if($success)
		{
	            $res = array( 'message_code' => 1000, 'message_text' => 'Emplopyee profile updated successfully.');
	    } 
	    else
	    {
	       $res = array( 'message_code' => 999, 'message_text' => 'Failed to update Emplopyee profile.');
	    } 
		
		return $response->withJson( $res, 200 );
	}

	/*
	* Employee Invitation from admin
	*/
	function tbx_emp_invitation( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$name = $body['Name'];
		$email = $body['Email'];
		$role_id = $body['RoleId'];;
		$contact_no = $body['PhoneNo'];

		$CreatedOn= date('Y-m-d H:i:s');

		if (!isset($body['CompanyId']))
			$company_id = "-";
		else
			$company_id = $body['CompanyId'];
		
		if (($name == null) || ($name==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Name cannot be blank.');
		else if (($email == null) || ($email==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Email cannot be blank.');
		else if (($contact_no == null) || ($contact_no==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Phone number cannot be blank.');
		else if ($role_id != 4)
			$res = array( 'message_code' => 999, 'message_text' => 'Role id does not match.');
		else
		{
			$temp_pass = random_password();
			//echo $temp_pass;
			//$password = password_hash( sha1( strtolower($email) . ':' . $temp_pass ), PASSWORD_BCRYPT );

			$count = $db->get_var('SELECT count(*) FROM `tbl_registration` WHERE LCASE(`RegistrationEmail`) = LCASE("' . $email . '") ');
		    //echo $count."Hi";exit;
			if( $count > 0 )
			{
				//$res = array( 'message_code' => 999, 'data_text'=>$count, 'message_text' => 'The email is already registered with ToolBX. Please login with your email and password.' );
				$res = array( 'message_code' => 999, 'message_text' => 'User Already Registered Contact support@toolbx.com if you require assistance.' );
				// User Already Registered. This user is already registered with Toolbx. Please contact support@mytoolbx.com if you require assistance.
			}
			else
			{ 

				/**20092017**/				
				$base_queryi = ' SELECT RegistrationId, CompanyId FROM tbl_registration WHERE CompanyId = "'.$company_id.'" AND RegsitrationRoleId = 2 ';
				$result = $db->get_row( $base_queryi );
				/****/
				
				$base_query = 'INSERT INTO tbl_registration (RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, CompanyId, CreatedBy, CreatedOn) VALUES("' . $name . '", "' . $email . '","' . $contact_no . '", "' . $temp_pass . '", "' . $role_id . '", "'.$company_id.'", "'.$result->RegistrationId.'", "'.$CreatedOn.'")';
				

				
				/*old query*///$base_query = 'INSERT INTO tbl_registration (RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, CompanyId,CreatedOn) VALUES("' . $name . '", "' . $email . '","' . $contact_no . '", "' . $temp_pass . '", "' . $role_id . '", "'.$company_id.'", "'.$CreatedOn.'")';
				
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
						
						//$res =  array( 'message_code' => 1000, 'message_text' => 'Invitation email is send to the Email: ' . $email );
						$res =  array( 'message_code' => 1000, 'message_text' => 'Your invitation has been sent ');
					}
				}
				else
				{
					$res = array( 'message_code' => 999, 'message_text' => 'Database error! User insertion failed.' );
				}
			}
		}
		
		return $response->withJson( $res, 200 );
	} 


	/*
	* Employee Invitation through App
	*/
	function tbx_emp_invitation_from_app( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$name = $body['Name'];
		$email = $body['Email'];
		$contact_no = $body['PhoneNo'];
		$createdby = $body['CreatedBy'];
		$role_id = 4;
		$CreatedOn= date('Y-m-d H:i:s');

		if (!isset($body['CompanyId']))
			$company_id = "-1"; 
		else
			$company_id = $body['CompanyId'];
		
		if (($name == null) || ($name==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Name cannot be blank.');
		else if (($email == null) || ($email==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Email cannot be blank.');
		else if (($contact_no == null) || ($contact_no==""))
			$res = array( 'message_code' => 999, 'message_text' => 'Phone number cannot be blank.');
		else
		{
			$temp_pass = random_password();
			$count = $db->get_var('SELECT count(*) FROM `tbl_registration` WHERE LCASE(`RegistrationEmail`) = LCASE("' . $email . '") ');
		
			if( $count > 0 )
			{
				//$res = array( 'message_code' => 999, 'message_text' => 'The email is already Registered with ToolBX. Please login with your email and password.' );
$res = array( 'message_code' => 999, 'message_text' => 'User Already Registered Contact support@toolbx.com if you require assistance.' );
				//User Already Registered. This user is already registered with Toolbx. Please contact support@mytoolbx.com if you require assistance.
			}
			else
			{
				$base_query = 'INSERT INTO tbl_registration (RegistrationName, RegistrationEmail, RegistrationPhoneNo, RegistrationPassword, RegsitrationRoleId, CompanyId, CreatedBy, CreatedOn) VALUES("' . $name . '", "' . $email . '","' . $contact_no . '", "' . $temp_pass . '", "' . $role_id . '", "'.$company_id.'", "'.$createdby.'", "'.$CreatedOn.'")';
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
						
						//$res =  array( 'message_code' => 1000, 'message_text' => 'Invitation email is send to the Email: ' . $email );
						$res =  array( 'message_code' => 1000, 'message_text' => 'Your invitation has been sent' );
					}
				}
				else
				{
					$res = array( 'message_code' => 999, 'message_text' => 'Database error! User insertion failed.' );
				}
			}
		}
		
		return $response->withJson( $res, 200 );
	} 


	//24/04/2017
	/*
	* Add Job Site
	*/
	function tbx_add_jobsite( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$sitename = $body['JobSiteName'];
		$address = $body['Address'];
		$postalcode = $body['PostalCode'];
		$city= $body['City'];
		$provience= $body['Province'];
		$country= $body['Country'];
		$notes= $body['Notes'];
		$ownerid= $body['OwnerId'];	

		$CreatedOn = date('Y-m-d H:i:s');

		$base_query = ' INSERT INTO tbl_jobsite (JobSiteName, Address, PostalCode, City, Province, Country, Notes, OwnerId,CreatedBy,CreatedOn)	VALUES("' . $sitename . '", "' . $address . '","' . $postalcode . '", "' . $city . '", "' . $provience . '", "'.$country.'", "'.$notes.'", "'.$ownerid.'","2","'.$CreatedOn.'") ';
		if($db->query( $base_query))
		{
		 	$last_jobsiteid = $db->insert_id;
			$base_query = 'SELECT JobSiteId, JobSiteName, Address, PostalCode, City, Province, Country, Notes, OwnerId FROM `tbl_jobsite` WHERE JobSiteId="'.$last_jobsiteid.'" AND `display`= \'Y\' ';
			$user = $db->get_row( $base_query );
			if($user)
            	$res = array( 'message_code' => 1000, 'data_text' => $user);
            else
            	$res = array( 'message_code' => 999, 'message_text' => 'Job sites Not Found.');
	    } 
	    else
	    {
	       $res = array( 'message_code' => 999, 'message_text' => 'Error While Adding Job sites.');
	    } 
		
		return $response->withJson( $res, 200 );
	}

	/*
	* List Jobsite
	*/
	function tbx_jobsitelist(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		//$id = $request->getAttribute('id');
		$base_query = 'SELECT JobSiteId, JobSiteName, Address, PostalCode, City, Province, Country, Notes, OwnerId FROM `tbl_jobsite` WHERE `display`= \'Y\' ';
		$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
		    
			$res = array( 'message_code' => 999, 'message_text' => 'Job Sites not found.');
			
		}
		return $response->withJson( $res, 200 );
	}


	/*
	* List Jobsite On REgistration id
	*/
	function tbx_jobsitelist_onid(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$role_id = $body['RoleId']; 
		
		if($role_id == 2)
		{
			$base_query = 'SELECT tj.JobSiteId, tj.JobSiteName, tj.Address, tj.PostalCode, tj.City, tj.Province, tj.Country, tj.Notes, tj.OwnerId  FROM `tbl_jobsite` tj  WHERE tj.display = \'Y\' AND tj.OwnerId= '.$id.' ';
			$result = $db->get_results( $base_query );
			if( isset( $result ) && !empty( $result ) )
			{
				$res = array( 'message_code' => 1000, 'data_text' => $result );
			}
			else
			{
				$res = array( 'message_code' => 999, 'message_text' => 'Job Sites not found.');
			}
		}
		elseif ($role_id == 4) 
		{
			$base_query = 'SELECT tj.JobSiteId, tj.JobSiteName, tj.Address, tj.PostalCode, tj.City, tj.Province, tj.Country, tj.Notes, tj.OwnerId FROM `tbl_jobsite` tj, `tbl_registration` tr WHERE tr.CreatedBy = tj.OwnerId AND tr.RegistrationId='.$id.' AND `display`= \'Y\' ';
			$result = $db->get_results( $base_query );
			if( isset( $result ) && !empty( $result ) )
			{
				$res = array( 'message_code' => 1000, 'data_text' => $result );
			}
			else
			{
			    $base_que = ' SELECT tr.RegistrationName, tr.RegistrationPhoneNo, tr.RegistrationEmail FROM `tbl_registration` AS tr RIGHT JOIN `tbl_companies` AS tc ON tc.CreatedBy = tr.RegistrationId WHERE tr.CompanyId = (SELECT CompanyId FROM `tbl_registration` WHERE RegistrationId = '.$id.' AND IsDeleted=\'N\') ';
			    $result1 = $db->get_results( $base_que );
				$res = array( 'message_code' => 999, 'message_text' => $result1);
				
			}
		}
		else
			$res = array( 'message_code' => 999, 'message_text' => 'Job Sites not found.');
		return $response->withJson( $res, 200 );
	}



	/*
	* Edit Jobsite (dont't remove any field from select query affected on runner jobsite details)
	*/
	function tbx_get_edit_single_jobsite( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$id = $request->getAttribute('id');
			
		$base_query = ' SELECT JobSiteId, JobSiteName, Address, PostalCode, City, Province, Country, Notes FROM `tbl_jobsite` WHERE JobSiteId = "' . $id . '"  '; //AND `display`= \'Y\'
		$user = $db->get_row( $base_query );
		if( $user )
		{			
			$res = array( 'message_code' => 1000, 'data_text' => $user );
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'This Job Sites not found.');
			
		}
		return $response->withJson( $res, 200 );
	}


	/*
	* Update Jobsite
	*/
	function tbx_update_jobsite( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		
		$body = $request->getParsedBody();
		$id = $body['id'];
		$sitename = $body['JobSiteName'];
		$address = $body['Address'];
		$postalcode = $body['PostalCode'];
		$city = $body['City'];
		$provience = $body['Province'];
		$country = $body['Country'];
		$notes = $body['Notes'];
		$lastmodified_on= date('Y-m-d H:i:s');

		$base_query = ' UPDATE `tbl_jobsite` SET JobSiteName = "'.$sitename.'", Address = "'.$address.'", PostalCode = "'.$postalcode.'", City = "'.$city.'", Province = "'.$provience.'", Country = "'.$country.'", Notes = "'.$notes.'", LastModifiedBy = "2", LastModifiedOn="'.$lastmodified_on.'"  WHERE JobSiteId= "'.$id .'" ';
		$success = $db->query( $base_query );		
		if($success)
		{
	        $res = array( 'message_code' => 1000, 'message_text' => 'Job sites updated successfully.');
	    } 
	    else
	    {
	       $res = array( 'message_code' => 999, 'message_text' => 'Failed to update Job sites');
	    } 
		
		return $response->withJson( $res, 200 );
	} 
	
	/*delete jobsite*/
	function tbx_jobsite_delete_onid( Request $request, Response $response )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$body = $request->getParsedBody();
		$jobsiteid = $request->getAttribute('jobsiteid');
		//$deleted_by = "1";
		$deletedON = date('Y-m-d H:i:s');
		$base_query = ' UPDATE `tbl_jobsite` SET `display`= \'N\', `DeletedOn`="'.$deletedON.'" WHERE `JobSiteId` = "'.$jobsiteid.'" ';
		if( $db->query( $base_query ))
		{	
			$res = array( 'message_code' => 1000, 'message_text' => 'Jobsite deleted successfully.');
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Uneble to delete jobsite.');
		}
		return $response->withJson( $res, 200 );
	}

/************************************/
	
	/* backend order list */

	/*function tbx_order_details(Request $request, Response $response)
	{
		$db = database();
						
		$base_query = '	SELECT ot.OrderId, ot.OrderDate, tod.Amount, tp.ProductName, tp.ProductDetails  FROM `tbl_order` AS ot 
						LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId=ot.OrderId
						LEFT JOIN `tbl_product` AS tp ON tp.ProductId=tod.ProductId
						WHERE ot.display=\'Y\' AND tp.display=\'Y\' ORDER BY ot.OrderId DESC ';				
		$result = $db->get_results( $base_query );
	    if( isset( $result ) && !empty( $result ) )
		{
			//return $response->withJson( $result );
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Order Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	} */	
	 
	function tbx_order_details(Request $request, Response $response)
	{ 
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	   
		$db = database();
		$cmpid = $request->getAttribute('CmpId');	
						
		// $base_query = '	SELECT ot.OrderId, ot.OrderDate, tod.Amount, tp.ProductName, tp.ProductDetails, tc.CompanyName,tc.CompanyId FROM `tbl_order` AS ot LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId=ot.OrderId LEFT JOIN `tbl_product` AS tp ON tp.ProductId=tod.ProductId LEFT JOIN `tbl_companies` AS tc ON tc.CompanyId = ot.CompanyId WHERE tc.CompanyId= " ' . $cmpid. ' " AND ot.display=\'Y\' AND tp.display=\'Y\' ORDER BY ot.OrderId DESC ';				

		//$base_query = '	SELECT ot.OrderId, ot.OrderDate, ot.TotalAmount as Amount, tc.CompanyName, tc.CompanyId FROM `tbl_order` AS ot LEFT JOIN `tbl_companies` AS tc ON tc.CompanyId = ot.CompanyId WHERE tc.CompanyId= " ' . $cmpid. ' " AND ot.display=\'Y\' ORDER BY ot.OrderId DESC ';
	 	$base_query = '	SELECT tj.JobSiteName,tj.Address,ot.OrderId, ot.OrderDate, ot.TotalAmount AS Amount, tc.CompanyName, tc.CompanyId 
						FROM `tbl_order` AS ot 
						LEFT JOIN tbl_companies AS tc ON tc.CompanyId = ot.CompanyId 
						LEFT JOIN tbl_jobsite AS tj ON tj.JobSiteId = ot.JobSiteId  WHERE tc.CompanyId= " ' . $cmpid. ' " AND ot.display=\'Y\' ORDER BY ot.OrderId DESC ';
		//echo $base_query; 
		$result = $db->get_results( $base_query );
	    if( isset( $result ) && !empty( $result ) )
		{
			//return $response->withJson( $result );
			$newresult  =array();
			$i=0;
			foreach ($result as $value) {
				$base_query = "select ProductName from tbl_product as p, tbl_order_details as od where od.productid = p.productid and od.orderid = " . $value->OrderId;
					//echo $base_query;
				$result1 = $db->get_results( $base_query );
				$produtname = "<ul>";
				if (isset($result1))
				{
					foreach ($result1 as $value1) {
						$produtname = $produtname . "<li class='clsbullet'>" . $value1->ProductName . "</li>";
					}
				}
				$produtname = $produtname . "</ul>";
				$result[$i]->ProductName = $produtname;
				$i++;	
			}



			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		elseif ($result == 0) 
		{
			$base_query = '	SELECT CompanyId, CompanyName FROM tbl_companies WHERE CompanyId = "' . $cmpid . '" AND display=\'Y\' ';				
			$result = $db->get_results( $base_query );
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Order Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	} 	 



		
	/*
	* order View
	*/

	function tbx_order_view_single_detaile(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	   	$id = $request->getAttribute('id');
		$db = database();
		
		/*$base_query = '	SELECT ot.OrderId, ot.OrderDate, ot.CompanyId, tod.OrderDetailId, tod.ProductId, tod.Quantity, tod.Rate, tod.Amount,  tp.ProductName, tp.ProductDetails  FROM `tbl_order` AS ot 
						LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId = ot.OrderId
						LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId
						WHERE ot.OrderId = "'.$id.'" AND ot.display=\'Y\' AND tp.display=\'Y\' ORDER BY ot.OrderId DESC ';*/
		
		$base_query = '	SELECT ot.OrderId, ot.OrderDate, ot.CompanyId, tod.OrderDetailId, tod.ProductId, tod.Quantity, tod.Rate, tod.Amount,  tp.ProductName, tp.ProductDetails,tj.Address  
						FROM `tbl_order` AS ot 
						LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId = ot.OrderId
						LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId
						INNER JOIN `tbl_jobsite` AS tj ON tj.JobsiteId = ot.JobsiteID
						WHERE ot.OrderId = "'.$id.'" AND ot.display= \'Y\' ORDER BY ot.OrderId DESC '; /*AND tp.display= \'Y\'*/
						
		$result = $db->get_results( $base_query );

		if( isset( $result ) && !empty( $result ) )
		{
			//return $response->withJson( $result );
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Order Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	}

	/*
	* order view head data
	*/
	function tbx_order_view_headsec_detail(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	   	$id = $request->getAttribute('id');
		$db = database();
		//$base_query = ' SELECT ot.OrderId, ot.OrderDate,  sum(tod.Amount) as totalamount FROM `tbl_order` AS ot LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId=ot.OrderId WHERE tod.OrderId="'.$id.'" AND ot.display=\'Y\' ORDER BY ot.OrderId DESC ';
		
		/*$base_query = ' SELECT tj.Address, ot.OrderId, ot.OrderDate,  sum(tod.Amount) as amount, ot.TotalAmount AS totalamount FROM `tbl_order` AS ot 
		LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId=ot.OrderId 
		LEFT JOIN `tbl_jobsite` AS tj ON tj.JobsiteId = ot.JobsiteId
		WHERE tod.OrderId="'.$id.'" AND ot.display=\'Y\' ORDER BY ot.OrderId DESC ';
		$result = $db->get_results( $base_query );*/

		$base_query = ' SELECT tj.Address, ot.OrderId, ot.OrderDate,  sum(tod.Amount) as amount, ot.TotalAmount AS totalamount FROM `tbl_order` AS ot 
    		LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId=ot.OrderId 
    		LEFT JOIN `tbl_jobsite` AS tj ON tj.JobsiteId = ot.JobsiteId
    		WHERE tod.OrderId="'.$id.'" AND ot.display=\'Y\' ORDER BY ot.OrderId DESC ';

    	$result = $db->get_results( $base_query );
		if( isset( $result ) && !empty( $result ) )
		{
			//return $response->withJson( $result );
			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Order Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	}

	 /*
    * order view pdf
    */
    function pdf_Order_view_data(Request $request, Response $response)
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

		$db = database();
		$OrderId = $request->getAttribute('OrderId');

		$base_query = ' SELECT tp.ProductImage, ot.OrderId, ot.OrderDate, ot.CompanyId, ot.TaxAmount, ot.DeliveryCharges,ot.TotalAmount, tod.OrderDetailId, tod.ProductId, tod.Quantity, tod.Rate, tod.Amount,  tp.ProductName, tp.ProductDetails,tj.Address  
                    FROM `tbl_order` AS ot 
                    LEFT JOIN `tbl_order_details` AS tod ON tod.OrderId = ot.OrderId
                    LEFT JOIN `tbl_product` AS tp ON tp.ProductId = tod.ProductId
                    INNER JOIN `tbl_jobsite` AS tj ON tj.JobsiteId = ot.JobsiteId
                    WHERE ot.orderid= "' . $OrderId . '" ';

        $result1 = $db->get_results( $base_query );
        
		if(isset( $result1 ) && !empty( $result1 ))
		{
			$base_query =" SELECT MAX(RunnerOrderId) AS  RunnerOrderId FROM tbl_runner_order WHERE orderid = '". $OrderId ."'  ORDER BY runnerOrderId DESC  ";
			$result = $db->get_row( $base_query );
			
			$base_query = " SELECT RegistrationId, RegistrationName FROM tbl_registration AS tr
							JOIN `tbl_runner_order` AS tro ON tro.RunnerId = tr.RegistrationId 
							WHERE tro.RunnerOrderId = '". $result->RunnerOrderId ."'  "; //AND tro.RunnerId = '". $result->RunnerId ."' 
			
			$result = $db->get_results( $base_query);
			
			$res = array( 'message_code' => 1000, 'data_text' => $result1, 'runner_text' => $result ); 
		}
		return $response->withJson( $res, 200 );
	}
	
	/*
	* runner Order status
	*/
    function tbx_runner_orders_status(Request $request, Response $response)
	{  
		$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
	   
		$db = database();
		$runnerid = $request->getAttribute('RunnerId');	
		
			
		$base_query = '	SELECT ot.OrderId,tro.RunnerOrderId, ot.OrderDate, ot.TotalAmount AS Amount, tc.CompanyName, tc.CompanyId, ot.IsAccepted AS statusA, ot.Delivered AS statusD 
						FROM `tbl_order` AS ot 
						LEFT JOIN tbl_companies AS tc ON tc.CompanyId = ot.CompanyId 
						LEFT JOIN tbl_runner_order AS tro ON tro.OrderId= ot.OrderId 
						WHERE tro.runnerId = "'. $runnerid .'"  AND ot.display = \'Y\' ORDER BY ot.OrderId DESC ';
	 
		$result = $db->get_results( $base_query );
	    if( isset( $result ) && !empty( $result ) )
		{
			$newresult  =array();
			$i=0;
			foreach ($result as $value) 
			{
				$base_query = "select ProductName from tbl_product as p, tbl_order_details as od where od.productid = p.productid and od.orderid = " . $value->OrderId;
				$result1 = $db->get_results( $base_query );
				$produtname = "<ul>";
				if (isset($result1))
				{
					foreach ($result1 as $value1) {
						$produtname = $produtname . "<li class='clsbullet'>" . $value1->ProductName . "</li>";
					}
				}
				$produtname = $produtname . "</ul>";
				$result[$i]->ProductName = $produtname;
				$i++;	
			}

			$res = array( 'message_code' => 1000, 'data_text' => $result );
		}
		else
		{
			return $response->withJson( array( 'message_code' => 900, 'message_text' => 'Order Details Not Found.') );
		}
		return $response->withJson( $res, 200 );
	} 

