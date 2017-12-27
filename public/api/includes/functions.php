<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function database()
{
	return new ezSQL_mysqli( USER, PASSWORD, DATABASE, HOST );
}

function generate_token( $user_id )
{
	$db = database();
	$api_key = hash( 'sha256', ( time() . $user_id . md5( uniqid( rand(), true ) ) . rand() ) );
	$created_on = date( 'Y-m-d H:i:s' );
	$db->query( 'DELETE FROM user_keys WHERE user_id = ' . $user_id );
	$db->query( 'INSERT INTO user_keys (user_id, token, created_on) VALUES (' . $user_id . ', "' . $api_key . '", "' . $created_on .'")' );
	return $api_key;
}

function verify_token( $user_id, $token )
{
	$db = database();
	$user_keys = $db->get_row( 'SELECT * FROM user_keys WHERE user_id = ' . $user_id );

	if( $user_keys->token == $token )
	{
		if( 1 ) // token expired check
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function random_password( $length = 8 )
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

function getOrderDetails($order_id)
{
    $db = database();
    $results = $db->get_results("SELECT tbl_product.ProductName, tbl_product.ProductImage, tbl_order_details.Quantity, tbl_order_details.Amount, tbl_order.TaxAmount, tbl_order.DeliveryCharges, (tbl_order.TotalAmount - tbl_order.TaxAmount-tbl_order.DeliveryCharges) AS SubTotal, tbl_order.TotalAmount FROM `tbl_order_details` JOIN tbl_product ON tbl_order_details.ProductId = tbl_product.ProductId JOIN tbl_order ON tbl_order.OrderId = tbl_order_details.OrderId WHERE tbl_order_details.OrderId =" . $order_id);

    $email = '';
    foreach($results as $result) {
        $email .= '<!-- Orders-list item-->
                <!--————————————————————————————-->
                <tr class="orders-list__item">
                    <td class="orders-list__image" height="100" style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;overflow:hidden;padding-right:20px;" valign="top" width="100"><img alt="HAY Design Outdoor Chair" src="' . $result->ProductImage .'" style="-ms-interpolation-mode: bicubic; border: 0 none; height: auto; line-height: 100%; outline: none; text-decoration: none; width: 100px;" width="100"></td>
                    <td class="orders-list__info" style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;padding-top:16px;" valign="top">
                        <p style="margin:0;"><span class="span" style="font-size: 17px; line-height: 20px;">' . $result->ProductName . '</span></p>
                        <div class="text-gray" style="color:#9a9a9a !important;">
                            <div class="h4" style="font-size:15px;line-height:20px;">
                                x ' . $result->Quantity . '
                            </div>
                        </div>
                    </td>
                    <td align="right" class="orders-list__price align-right" style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;padding-top:16px;" valign="top" width="60">
                        <span class="span" style="font-size: 17px; font-weight: bold; line-height: 20px;">$' . $result->Amount . '</span>
                        <div class="text-gray" style="color:#9a9a9a !important;">
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;mso-table-lspace:0pt;mso-table-rspace:0pt;" width="100%">
                                <tr>
                                    <td style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;font-size:20px;line-height:1;text-decoration:none !important;">&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;">
                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;mso-table-lspace:0pt;mso-table-rspace:0pt;" width="100%">
                            <tr>
                                <td style="border-collapse:collapse;font-family:Arial, Helvetica, sans-serif !important;font-size:25px;line-height:1;text-decoration:none !important;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr><!--————————————————————————————-->';
    }
    return [ 
        'details' => $email,
        'subtotal' => current($results)->TotalAmount - current($results)->TaxAmount - current($results)->DeliveryCharges,
        'total' => current($results)->TotalAmount,
        'shipping' => current($results)->DeliveryCharges,
        'taxes' => current($results)->TaxAmount
    ];
}

function send_order_email( $name, $email, $order_id )
{
    $subject = 'ToolBX - Order #' . $order_id;

    $order_info = getOrderDetails($order_id);

    $data = [
        [
            'name' => 'NAME',
            'content' => $name
        ],
        [
            'name' => 'ORDERID',
            'content' => $order_id
        ],
        [
            'name' => 'ORDERDETAILS',
            'content' => $order_info['details'],
        ],
        [
            'name' => 'SUBTOTAL',
            'content' => $order_info['subtotal'],
        ],
        [
            'name' => 'TAXES',
            'content' => $order_info['taxes'],
        ],
        [
            'name' => 'SHIPPING',
            'content' => $order_info['shipping'],
        ],
        [
            'name' => 'TOTAL',
            'content' => $order_info['total'],
        ],
        [
            'name' => 'SHIPPINGMETHOD',
            'content' => 'Physical Delivery by Truck',
        ],
        [
            'name' => 'PAYMENTMETHOD',
            'content' => 'CREDIT CARD',
        ],
        [
            'name' => 'SHIPPINGADDRESS',
            'content' => '',
        ],
        [
            'name' => 'BILLINGADDRESS',
            'content' => '',
        ],
    ];

    return tbx_ajitem_order_mail($name, $email, $subject, $data);
    // $to = $email;

    // $subject = "ToolBX Order#" . $order_id;

    // $message = "Dear " . $name . ", <br/><br/>";
    // $message .= "Your order number " .$order_id . " has been placed. " . "<br/><br/>";

    // $message .= "<br/><br/> Kind Regards,<br/><br/> ToolBX Admin <br/><br/>";

    // return SendSMTPMailCommon($to, $subject, $message);
}

function send_order_cancel_email( $name, $email, $order_id )
{
    $subject = 'ToolBX - Order #' . $order_id;

    $order_info = getOrderDetails($order_id);

    $data = [
        // [
        //     'name' => 'NAME',
        //     'content' => $name
        // ],
        [
            'name' => 'ORDERID',
            'content' => $order_id
        ],
        [
            'name' => 'ORDERDETAILS',
            'content' => $order_info['details'],
        ],
        // [
        //     'name' => 'SUBTOTAL',
        //     'content' => $order_info['subtotal'],
        // ],
        [
            'name' => 'TAXES',
            'content' => $order_info['taxes'],
        ],
        [
            'name' => 'SHIPPING',
            'content' => $order_info['shipping'],
        ],
        [
            'name' => 'TOTAL',
            'content' => $order_info['total'],
        ],
    ];

    return tbx_ajitem_order_cancel_mail($name, $email, $subject, $data);
    // $to = $email;

    // $subject = "ToolBX Order#" . $order_id;

    // $message = "Dear " . $name . ", <br/><br/>";
    // $message .= "Your order number " .$order_id . " has been cancelled. " . "<br/><br/>";

    // $message .= "<br/><br/> Kind Regards,<br/><br/> ToolBX Admin <br/><br/>";

    // return SendSMTPMailCommon($to, $subject, $message);
}

//$$SL
function send_password( $name, $email, $password )
{
    $subject = 'Your Password has been Reset.';

    $data = [
        [
            'name' => 'PASSWORD',
            'content' => $password
        ]
    ];

    return tbx_ajitem_reset_password_mail($name, $email, $subject, $data);
	// $to = $email;
	// $subject = "Your Password has been sent.";
	// //$subject = "Your Password has been Reset.";
 	
 // 	$message = "Dear " . $name . ",<br/><br/>";
	// $message .= "Your new password is " . $password . "<br/><br/>";
	
	// $message .= "<br/><br/> Kind Regards,<br/><br/> ToolBX Admin <br/><br/>";
	
    // $headers = "";
    // $headers .= "From: WIFI Metropolis <sitename@hostname.com> <br/>";
    // $headers .= "Reply-To:" . $from . "<br/>" ."X-Mailer: PHP/" . phpversion();
    // $headers .= 'MIME-Version: 1.0' . "<br/>";
    // $headers .= 'Content-type: text/html; charset=iso-8859-1' . "<br/>";   

	/*ini_set('smtp_host','mail.applinktest.in');
	ini_set('smtp_user','Support@applinktest.in');
	ini_set('smtp_pass','uIysicr0Hg2t'); */
	
	
    // 	ini_set('SMTP','mail.applinktest.in');
    // 	ini_set('sendmail_from','Support@applinktest.in');
    // 	//ini_set('smtp_user','Support@applinktest.in');
    // 	//ini_set('smtp_pass','uIysicr0Hg2t');
    // 	ini_set('username','Support@applinktest.in');
    // 	ini_set('password','uIysicr0Hg2t');
    // 	ini_set('smtp_port',25);

	//$result = mail( $to, $subject, $message, $header);
	
	//$from_email = "ToolBx Admin";
    //mail( $to, $subject, $message , "", "-f$from_email");

    return SendSMTPMailCommon($to, $subject, $message);
    
    /*if($data)
    {
        return true;
    }
    else
    {
        return false;
    }*/

    
}

function sendConfirmationMail( $name, $email )
{

    $subject = 'Welcome to ToolBX!';

    $data = [
        [
            'name' => 'FNAME',
            'content' => $name
        ]
    ];

    return tbx_ajitem_confirmation_mail($name, $email, $subject, $data);
		// $to = $email;
		// $subject = "Welcome to ToolBx!";
         
		// $message = "Dear " . $name . ",<br /><br/><br /><br/>";
		// $message .= "Your registration is now complete. Please login with your email address and password and enjoy the app.<br/><br/><br /><br/> Thanks and Regards <br /><br/> Mammaalert Admin";
         
		// $header = "From:ToolBX Admin <support@toolbx.com> <br/>";
		// $header .= "MIME-Version: 1.0<br/>";
		// $header .= "Content-type: text/html<br/>";
		//ini_set('smtp_user','mallika@giftjeenie.com');
		//ini_set('smtp_pass','Makhijani07');
		
		
		ini_set('smtp_host','mail.applinktest.in');
		ini_set('smtp_user','Support@applinktest.in');
		ini_set('smtp_pass','uIysicr0Hg2t');
		
 	
		$result = mail( $to, $subject, $message, $header );
}


// function send_confirmation_mail( $name, $email )
// {
// 	$to = $email;
// 	$subject = "Welcome to NuVO!";
     
// 	$message = "Dear " . $name . ",<br /><br/>";
// 	$message .= "Your registration is now complete.<br/>";
     
// 	$header = "From:ajitem@joshiinc.com <br/>";
// 	//$header .= "Cc:afgh@somedomain.com <br/>";
// 	$header .= "MIME-Version: 1.0<br/>";
// 	$header .= "Content-type: text/html<br/>";

// 	ini_set('smtp_host','mail.applinktest.in');	
// 	ini_set('smtp_user','Support@applinktest.in');
// 	ini_set('smtp_pass','uIysicr0Hg2t');
// 	$result = mail( $to, $subject, $message, $header );
// }



// function send_verification_mail( $url,$name,$email,$user_key )
// {
// 	$to = $email;
// 	$subject = "Welcome to Mammalert!";
     
// 	$message = "Dear " . $name . ",<br /><br/>";
// 	$message .= "Your registration is now completed, To verify your email address please click here. <br/>";
// 	$message .= "http://mammalert.theapptest.xyz/user/verify/".$user_key;
     
// 	$header = "From:Mammaalert Admin <support@mammaalert.com> <br/>";
// 	$header .= "MIME-Version: 1.0<br/>";
// 	$header .= "Content-type: text/html<br/>";

// 	ini_set('smtp_host','mail.applinktest.in');
// 	ini_set('smtp_user','Support@applinktest.in');
// 	ini_set('smtp_pass','uIysicr0Hg2t');
// 	$result = mail( $to, $subject, $message, $header );
// }


//$$SL
function send_invitation_mail( $url, $email, $user_id, $name, $temp_pass)
{
    $subject = 'Welcome to ToolBX!';

    $data = [
        [
            'name' => 'NAME',
            'content' => $name
        ],
        [
            'name' => 'URL',
            'content' => $url
        ]
    ];

    return tbx_ajitem_invitation_mail($name, $email, $subject, $data);
	// $to = $email;
	// $subject = "Welcome to ToolBX!";
     
	// $message = "Dear " . $name . ",<br/><br/>";
	// $message .= "You are invited to ToolBX app. Please join the app using following link<br/><br/><br/>";
	// $message .= $url;
	// $message .= "<br/><br/>Kind Regards,<br/>ToolBX Admin<br/><br/><br/>";
	
     
	/*$header = "From:ToolBX Admin <support@toolbx.com>";
	$header .= "MIME-Version: 1.0";
	$header .= "Content-type: text/html";*/

    // 	ini_set('SMTP','mail.applinktest.in');
    // 	ini_set('sendmail_from','Support@applinktest.in');
    // 	//ini_set('smtp_user','Support@applinktest.in');
    // 	//ini_set('smtp_pass','uIysicr0Hg2t');
    // 	ini_set('username','Support@applinktest.in');
    // 	ini_set('password','uIysicr0Hg2t');
    // 	ini_set('smtp_port',25);
	
	
	//mail( $to, $subject, $message, $header);
	//mail( $to, $subject, $message);
	
	//$from_email = "ToolBx Admin";
    //mail( $to, $subject, $message , "", "-f$from_email");

    return SendSMTPMailCommon($to,$subject,$message);
    
	
	
}

function get_current_user_id( $request )
{
	$headers = $request->getHeaders();
	return $headers['PHP_AUTH_USER'][0];
}

function get_current_user_token( $request )
{
	$headers = $request->getHeaders();
	return $headers['PHP_AUTH_PW'][0];
}	


function get_current_user_role( $request )
{
	$headers = $request->getHeaders();
	$db = database();
	$role = $db->get_var('SELECT role FROM user WHERE id = ' . $headers['PHP_AUTH_USER'][0] );
	return $role;
}


function cmp($a, $b)
{
    return ( $b->percentage - $a->percentage );
}

function getexpirytime($expiry_date)
{

	$expiry_date = new DateTime($expiry_date);
	$date_today = new DateTime("now");

	$difference = date_diff( $expiry_date, $date_today );

	//return $difference->format('%h hour(s) %i min(s)');
	return $difference->format('%h:%i');
}

function array_utf8_encode($dat)
{
    if (is_string($dat))
        return utf8_encode($dat);
    if (!is_array($dat))
        return $dat;
    $ret = array();
    foreach ($dat as $i => $d)
        $ret[$i] = array_utf8_encode($d);
    return $ret;
}

function time_passed($timestamp)
{
    //type cast, current time, difference in timestamps
    $timestamp      = (int) $timestamp;
    $current_time   = time();
    $diff           = $current_time - $timestamp;
    
    //intervals in seconds
    $intervals      = array (
        'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
    );
    
    //now we just find the difference
    if ($diff == 0)
    {
        return 'just now';
    }    

    if ($diff < 60)
    {
        return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
    }        

    if ($diff >= 60 && $diff < $intervals['hour'])
    {
        $diff = floor($diff/$intervals['minute']);
        return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
    }        

    if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
    {
        $diff = floor($diff/$intervals['hour']);
        return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
    }    

    if ($diff >= $intervals['day'] && $diff < $intervals['week'])
    {
        $diff = floor($diff/$intervals['day']);
        return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
    }    

    if ($diff >= $intervals['week'] && $diff < $intervals['month'])
    {
        $diff = floor($diff/$intervals['week']);
        return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
    }    

    if ($diff >= $intervals['month'] && $diff < $intervals['year'])
    {
        $diff = floor($diff/$intervals['month']);
        return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
    }    

    if ($diff >= $intervals['year'])
    {
        $diff = floor($diff/$intervals['year']);
        return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
    }
}

function generate_branch_link($user_id)
{
	$branch_key = 'key_live_kcxEc1hAnSlscTHoXpiaFkngvBbIZCRV'; // your branch key.
   	$ch = curl_init('https://api.branch.io/v1/url');
    $payload = json_encode([
        'branch_key'=> $branch_key,
        
        'data' => [
        	'user_id' => $user_id,
            //'$desktop_url'=>'http://toolbx.applinktest.in',
            '$desktop_url'=>'http://toolbx.applabb.ca',
            '$ios_url'=>'toolbx://',
            '$ipad_url'=>'toolbx://',
            '$android_url'=>'toolbx://']
        ]);
 
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    # Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    
   	
    
    return json_decode($result);
}




/*
* Firebase Notification
*/
	function send_notification($registration_ids, $message)
    {
        
        $fields = array(
            'to' => $registration_ids,
            'data' =>array('data'=>$message)
        );
        return sendPushNotification($fields);
    }
    
    /*
    * This function will make the actuall curl request to firebase server
    * and then the message is sent 
    */
    
    function sendPushNotification($fields) 
    {        
       
        //firebase server url to send the curl request
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        //building headers for the request
        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );

        //Initializing curl to open a connection
        $ch = curl_init();
 
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);

        //adding headers 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //adding the fields in json format 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        //finally executing the curl request 
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        //Now close the connection
        curl_close($ch);
 
        //and return the result 
        return json_decode($result);
    }



function SendSMTPMailCommon($To, $Subject, $Message )
{
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    
    // require_once("Mail.php");
    // $From = "Toolbx Support <info@mytoolbx.com>";
  

    // $headers = array ('From' => $From, 'To' => $To, 'Subject' => $Subject);

    // $smtp = Mail::factory('smtp', array(
    //     'host' => 'ssl://smtp.gmail.com',
    //     'port' => '465',
    //     'auth' => true,
    //     'debug' => true,
    //     'pipelining' => true,
    //     'username' => 'info@mytoolbx.com',
    //     'password' => 'toolbxinfo123'
    // ));

    // $mail = $smtp->send($To, $headers, $Message);

    // if(PEAR::isError($mail)) 
    // {
    //   return false;
    //   //echo $mail->getMessage();
    // } 
    // else 
    // {
    //   return true;
    //   //echo $mail->getMessage();
    // }
    //  //return false;

    error_reporting(0); //E_ALL
    ini_set('display_errors', '0');

    $mail = new PHPMailer(true); 
                                 // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'info@toolbx.com';                 // SMTP username
    $mail->Password = 'Toolbx123';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('info@mytoolbx.com', 'ToolBX Support');
    $mail->addAddress($To);     // Add a recipient

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $Subject;
    $mail->Body    = $Message;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    return 'Message has been sent';
    // return true;
} catch (Exception $e) {
    // echo 'Message could not be sent.';
    // echo 'Mailer Error: ' . $mail->ErrorInfo;
    return false;
}
}


/**
 * AJITEM EDIT: 02/01/2017 - Array to CSV Function
 */
if (!function_exists('str_putcsv')) {
    /**
     * Convert a multi-dimensional, associative array to CSV data
     * @param  array $data the array of data
     * @return string       CSV text
     */
    function str_putcsv($data) {
            # Generate CSV data from array
            $fh = fopen('php://temp', 'rw'); # don't create a file, attempt
                                             # to use memory instead
            fputs($fh, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            fputcsv($fh, array_keys(current($data)));
            foreach($data as $row) {
                fputcsv($fh, array_values($row));
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);

            return $csv;
    }
}
