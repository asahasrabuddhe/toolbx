<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//require 'stripe/Stripe.php';

/*
* Get all province detail
*/
function addnewMember( Request $request, Response $response )
{
	$db = database();
	$body = $request->getParsedBody();
	
	$app_number = $body['app_number'];
	$first_name = $body['first_name'];
	$middle_name = $body['middle_name'];
	$gender = $body['gender'];
	$marrital_status = $body['marrital_status'];
	$dob = $body['dob'];
	$add_line1 = $body['add_line1'];
	$add_line2 = $body['add_line2'];
	$city = $body['city'];
	$area = $body['area'];
	$mobile_number = $body['mobile_number'];
	$email = $body['email'];
	$pancard = $body['pancard'];
	$idproof = $body['idproof'];
	$add_proof = $body['add_proof'];
	$joining_date = date('Y-m-d H:i:s');
	
	$base_query = 'INSERT INTO at_member_details(app_number, first_name, middle_name, gender, marrital_status, dob, add_line1, add_line2, city,area, mobile_number,email,pancard, idproof, add_proof, joining_date) VALUES ("' . $app_number . '", "' . $first_name . '", "' . $middle_name . '","' . $gender . '", "' . $marrital_status . '", "' . $dob . '","' . $add_line1 . '", "' . $add_line2 . '", "' . $city . '","' . $area . '", "' . $mobile_number . '", "' . $email . '","' . $pancard . '", "' . $idproof . '", "' . $add_proof . '","' . $joining_date . '")';

	if( $db->query( $base_query ) )
	{
		$result = array( 'message_code' => 150);
		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}
}

/*
* Get all province detail
*/
function hub_get_province( Request $request, Response $response )
{
	$db = database();
	$query = 'SELECT * FROM province as p WHERE p.is_deleted = 1 ';
	$result = $db->get_results( $query );
	if( $result )
	{
		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}
}

/*
* Get all country detail
*/
function hub_get_country( Request $request, Response $response )
{
	$db = database();
	$query = 'SELECT * FROM country as c WHERE c.is_deleted = 1 ';
	$result = $db->get_results( $query );
	if( $result )
	{
		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}
}

/*
* Hun user register
*/
function hub_user_register( Request $request, Response $response )
{

	$db = database();
	$body = $request->getParsedBody();
	$first_name = $body['first_name'];
	$last_name = $body['last_name'];
	$email = $body['email'];
	$password = password_hash( sha1( $email . ':' . $body['password'] ), PASSWORD_BCRYPT );
	$country = $body['country'];
	$province = $body['province'];
	$number_of_kids = $body['number_of_kids'];
	$age_group = $body['age_group'];
	$type_of_car = '';
	$created_by= '';
    $status = 1;
	$created_on = date('Y-m-d H:i:s');
	
	$count = $db->get_var( 'SELECT count(*) FROM user WHERE email = "' . $email . '" AND is_deleted = 1' );

	if( $count > 0 )
	{
		$res = array( 'message_code' => 104 );
	}
	else
	{
		$cnt_user = $db->get_var( 'SELECT count(*) FROM user WHERE email = "' . $email . '" ' );
       
        //permanatly delete user if user deactivate and existing 
        if( $cnt_user > 0 )
		{	
			$delete_user ='DELETE FROM user WHERE email = "'.$email.'" ';
			$db->query( $delete_user );
    	}

		$base_query = 'INSERT INTO user (first_name, last_name, email, password, country_id,province_id, number_of_kids,age_group,type_of_car,created_on) VALUES ("' . $first_name . '", "' . $last_name . '", "' . $email . '", "' . $password . '", "' . $country . '", "' . $province . '", "' . $number_of_kids . '", "' . $age_group . '", "' . $type_of_car . '", "' . $created_on . ' ")';
		
		//cho $base_query;
		
		if( $db->query( $base_query ) )
		{ 
			$user_id = $db->insert_id;
			$url ="http://mammalert.theapptest/user/verify/";
			send_verification_mail( $url,$first_name, $email ,$user_id);
			//sendConfirmationMail( $first_name, $email );
			$res =  array( 'message_code' => 150, 'user_id' => $user_id );
		}
		else
		{
			$res = array( 'message_code' => 100 );
		}
	}
	//send_confirmation_mail( $first_name . ' ' . $last_name, $email );
	return $response->withJson( $res, 200 );
}
/***** Yogesh *****/
function roll_get_data( Request $request, Response $response )
{
	/*$db = database();
	//$query = 'SELECT * FROM country as c WHERE c.is_deleted = 1 ';
	$query = "SELECT * FROM `tbl_userroles` WHERE display='Y'";
	$result = $db->get_results( $query );
	if( $result )
	{
		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}*/

	$db = database();
	$id = $request->getAttribute('id');
	$query = 'SELECT * FROM `tbl_userroles` WHERE roll_id = "' . $id .'" ';
	$result = $db->get_row( $query );
	
	echo json_encode($result);
	/*if( $result )
	{
		echo "Yogesh";
		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}*/
}
/**** y ** end ***/
/*
* User login
*/ 
function hub_user_login( Request $request, Response $response )
{
	$db = database();
	$body = $request->getParsedBody();
	$email = $body['email'];
	$password = $body['password'];

	/*$data = array('email'=>$email,'password'=>$password);
	print_r($data);*/
	

	$user = $db->get_row('SELECT u.id,u.email, u.password, u.role,u.country_id,c.country,u.province_id, p.province,u.number_of_kids, u.age_group,u.status, u.first_name, u.last_name FROM user AS u LEFT JOIN province AS p ON u.province_id = p.id LEFT JOIN country AS c ON u.country_id = c.id LEFT JOIN codes AS age ON u.age_group = age.code_id WHERE u.email = "'.$email.'" and u.is_deleted = 1');
	//$user = $db->get_row('SELECT u.id,u.email, u.password, u.role,u.country_id,c.country,u.province_id, p.province,u.number_of_kids, u.age_group,age.code_value AS age_group_name, u.status, u.first_name, u.last_name FROM user AS u LEFT JOIN province AS p ON u.province_id = p.id LEFT JOIN country AS c ON u.country_id = c.id LEFT JOIN codes AS age ON u.age_group = age.code_id  WHERE u.email = "' . $email . '" and u.is_deleted = 1');
	//$user = $db->get_row("SELECT tu.roll_id,tu.roll_name,ta.admin_id,ta.admin_email,ta.admin_password FROM `tbl_userroles` AS tu LEFT JOIN `tbl_administrator` AS ta ON ta.admin_id=tu.roll_id WHERE ta.display='Y'");
	//print_r(json_encode($user));
	if( $user )
	{
		if( $user->password == sha1($email . ':' . $password ))
		//if( $user->password == $password)
		//if( password_verify(sha1( $email . ':' . $password ),  $user->password))
		{  
			if( $user->status == 1) // User Active / Inactive check
			{
				$token = generate_token( $user->id );
				$res =  array( 'message_code' => 152,'user_id' => $user->id,'email'=>$user->email,'first_name'=>$user->first_name,'last_name'=>$user->last_name, 'role' =>$user->role, 'token' => $token,'country_id' =>$user->country_id,'country' =>$user->country,'province_id' =>$user->province_id,'province' =>$user->province,'number_of_kids' =>$user->number_of_kids,'age_group' =>$user->age_group);
				//$res =  array( 'message_code' => 152,'admin_id' =>$user->admin_id,'admin_email'=>$user->admin_email,'roll_name' =>$user->roll_name, 'token' => $token);
				//return $res;
			}
			else
			{
				$res = array( 'message_code' => 104 ); //User is not active
			}
		}
		else
		{
			$res = array( 'message_code' => 105 ); //Password did not match
		}
	}
	else
	{
		$res = array( 'message_code' => 103 ); //Email did not match
	}

	return $response->withJson( $res, 200 );
}

/*
* Hub user update
*/
function hub_user_update( Request $request, Response $response )
{
	$db = database();
	$body = $request->getParsedBody();
	$id = $body['id'];
	$first_name = $body['first_name'];
	$last_name = $body['last_name'];
	$email = $body['email'];
	$country = $body['country'];
	$province = $body['province'];
	$number_of_kids = $body['number_of_kids'];
	$age_group = $body['age_group'];
	$type_of_car = '';
	$created_by= '';
    $status = 1;
	$created_on = date('Y-m-d H:i:s');
    	//update user table  
		$base_query = 'UPDATE user  SET first_name = "' . $first_name . '", last_name = "' . $last_name . '" , email = "' . $email . '", country_id = "' . $country . '", province_id = "' . $province . '", number_of_kids = "' . $number_of_kids . '", age_group = "' . $age_group . '", created_on = "' . $created_on . '" WHERE id = ' . $id;
		
		$success1 = $db->query( $base_query );
		//$role = $db->get_var( 'SELECT role FROM user WHERE id = ' . $id );

	if($success1)
	{
		//Get user detail
		$user = $db->get_row( 'SELECT u.id,u.email, u.password, u.role,u.country_id,c.country,u.province_id, p.province,u.number_of_kids, u.age_group,age.code_value AS age_group_name, u.status, u.first_name, u.last_name FROM user AS u LEFT JOIN province AS p ON u.province_id = p.id LEFT JOIN country AS c ON u.country_id = c.id LEFT JOIN codes AS age ON u.age_group = age.code_id  WHERE u.id = "' . $id . '"');
        
        if($user)
        {	

        					//Get age group names
							$age_group_name = '';
							if($user->age_group !='')
							{	
									$data = explode(',', $user->age_group);
									$count = 0;				
									foreach ($data as $key) 
									{
										$query = 'SELECT age.ageGroup AS age_group_name FROM tblAgeGroup AS age WHERE "'.$key.'" = age.id';
										$age_group_name.= $db->get_var( $query );
											if($count < sizeof($data)-1)
											{	
											$age_group_name.= ',';
											}
										$count ++;
									}
							}	

        	$user_token = get_current_user_token ($request);  //get user token

			$res =  array( 'message_code' => 151,'user_id' => $user->id,'email'=>$user->email,'first_name'=>$user->first_name,'last_name'=>$user->last_name, 'role' =>$user->role,'country_id' =>$user->country_id,'country' =>$user->country,'province_id' =>$user->province_id,'province' =>$user->province,'number_of_kids' =>$user->number_of_kids,'age_group' =>$user->age_group,'age_group_name' =>$age_group_name,'token' =>$user_token);

			return $response->withJson( $res, 200 );
		}
		else
		{	
			return $response->withJson( array( 'message_code' => 151 ) );
		}	
	}	
	else
		return $response->withJson( array( 'message_code' => 100 ) );
}

/*
* Change user password
*/
function hub_change_password (Request $request,Response $response)
{
    $db = database();   
    $body = $request->getParsedBody();
    $old_password = $body['password'];
    $email = $body['email'];
    $new_password = password_hash( sha1( $email . ':' . $body['new_password'] ), PASSWORD_BCRYPT );
	$confirm_password = $body['confirm_password'];
    
    $user = $db->get_row( 'SELECT id, password, role, status, first_name, last_name FROM user WHERE email = "' . $email . '"');
         
    $result = password_verify( sha1( $email . ':' . $old_password), $user->password );
         
    if($result)
    {
	    $base_query = 'UPDATE `user` SET `password`= "'.$new_password.'" WHERE `id`= "'.$user->id .'" ';
        
        if( $db->query( $base_query ) )
        {
            return $response->withJson( array( 'message_code' => 170 ));
        } 
        else
        {
            return $response->withJson( array( 'message_code' => 106 ));
        }  
       
     }
     else
     {
            return $response->withJson( array( 'message_code' => 105 ));
     }  
    
}

/*
* Hub user forgot password
*/
function hub_user_forgotpassword( Request $request, Response $response )
{
	$body = $request->getParsedBody();

	$email = $body['email'];
	//$role = $body['role'];

	$db = database();

	$exists = $db->get_row( 'SELECT id, first_name,last_name FROM user WHERE email = "' . $email . '" AND is_deleted = 1' );

	if( $exists->id )
	{
			$password = random_password();
			$new_password = password_hash( sha1( $email . ':' . $password ), PASSWORD_BCRYPT );
			$db->query( 'UPDATE user SET password = "' . $new_password . '" WHERE id = ' . $exists->id );
			send_password( $exists->first_name, $email, $password );
			return $response->withJson( array( 'message_code' => 153  ) );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 106 ) );
	}

}

/*
* Get user detail
*/

function hub_user_get( Request $request, Response $response )
{
	$db = database();
	$id = $request->getAttribute('id');
		$query = 'SELECT u.first_name, u.last_name, u.email,u.country_id,c.country, u.province_id,p.province, u.number_of_kids,u.age_group, u.created_on, u.last_modified, u.status FROM user as u LEFT JOIN province AS p ON u.province_id = p.id LEFT JOIN country AS c ON u.country_id = c.id LEFT JOIN tblAgeGroup AS age ON u.age_group = age.id WHERE u.id = "' . $id .'" AND u.is_deleted = 1 ';

		

	$result = $db->get_row( $query );
	
	if( $result )
	{
		if($result->age_group !='')
		{	
				$data = explode(',', $result->age_group);
				$age_group_name = '';
                
                $count = 0;				
				foreach ($data as $key) {
					
					$query = 'SELECT age.ageGroup AS age_group_name FROM tblAgeGroup AS age WHERE "'.$key.'" = age.id';
					$age_group_name.= $db->get_var( $query );
						if($count < sizeof($data)-1)
						{	
						$age_group_name.= ',';
						}
					$count ++;

				}
				
				$result->age_group_name = $age_group_name;
		}		

		return $response->withJson( $result, 200 );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ), 200 );
	}
}


/*
* Hub user delete
*/
function hub_user_delete( Request $request, Response $response )
{
	$db = database();
	
	$user_id = get_current_user_id( $request );
	
	$id = $request->getAttribute('id');

	$base_query = 'UPDATE user  SET is_deleted = 0 WHERE id = ' . $id;
	
	if( $db->query( $base_query ))
	{
		$res = array( 'message_code' => 160 );
	}
	else
	{
		$res = array( 'message_code' => 100 );
	}

	return $response->withJson( $res, 200 );
}


/*
* Get all user data
*/
function hub_user_get_all( Request $request, Response $response )
{
	$db = database();
	//echo "test";exit();
	$query = 'SELECT u.id,u.first_name, u.last_name, u.email,u.country_id, u.province_id, u.number_of_kids,u.age_group, u.created_on, u.last_modified, u.status FROM user as u WHERE u.is_deleted = 1 AND u.role = 1';
	
//echo $query;die;
	$result = $db->get_results( $query );

	$base_query = 'SELECT COUNT(u.id) AS users_count FROM user as u WHERE u.is_deleted = 1 AND u.role = 1 ';
	
	$count = $db->get_var( $base_query );

	if( isset( $result ) && !empty( $result ) )
	{
		$result[0]->users_count = $count;
		return $response->withJson( $result );
	}
	else
	{
		return $response->withJson( array( 'message_code' => 111 ) );
	}
}


function hub_user_logout( Request $request, Response $response )
{
	$id = $request->getAttribute('id');

	$db = database();

	$db->query( 'UPDATE user SET online = 8 WHERE id = ' . $id );
	$db->query( 'DELETE FROM user_keys WHERE user_id = ' . $id );

	return $response->withJson( array( 'message_code' => 154 ) );
}

function sendsmtpmail( Request $request, Response $response )
{
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once("Mail.php");
	$from = "Toolbx Support <info@mytoolbx.com>";
	$to = "Sunil Limje <SLimje@Torinit.com>";
	//$to = "anupom.gogoi@gmail.com";
	$subject = "Test message from ToolBx";
	$body = "Test message content for Toolbx Mail";

	$host = "tls://smtp.gmail.com";
	$username = "info@mytoolbx.com";
	$password = "toolbxinfo123";

	//echo $body;

	$headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);

	echo "<pre>Headers: ". print_r($headers, true) . "</pre>";
	echo "1";
	//echo "<br/>";

	//$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => 587, 'auth' => true, 'username' => $username, 'password' => $password));
	$smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'debug' => true,
        'pipelining' => true,
        'username' => 'anupomgogoi.test@gmail.com',//'info@mytoolbx.com',
        'password' => 'admin.1234'//'toolbxinfo123'
    ));

	//echo "<pre>SMTP: ". print_r($smtp, true) . "</pre>";

	$mail = $smtp->send($to, $headers, $body);

	//echo "<pre>MAIL: ". print_r($mail, true) . "</pre>";

	if (PEAR::isError($mail)) {
	  echo("<p>" . $mail->getMessage() . "</p>");
	 } else {
	  echo("<p>Message successfully sent!</p>");
	 }

	return $response->withJson( array( 'message_code' => 154 ) );
}



/*
* Get all province detail
*/
function tbx_order_employee_list( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	
	$RegistrationId = $request->getAttribute("id");

	$sSQL = "SELECT RegistrationId, RegistrationName FROM tbl_registration WHERE RegsitrationRoleId=4 and CompanyId in (SELECT CompanyId from tbl_registration where RegistrationId = " . $RegistrationId . ") order by RegistrationName ";

	//echo $sSQL . "<br/>";

	$result = $db->get_results($sSQL);

	if ( isset($result) && (!empty($result) ))
		$res = array( 'message_code' => 1000, 'data_text' => $result );
	else
		$res = array( 'message_code' => 1000, 'data_text' => "Employee list is not available." );

	return $response->withJson( $res, 200 );
}
