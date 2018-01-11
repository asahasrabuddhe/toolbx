<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function database()
{
    date_default_timezone_set('America/Toronto');
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
    $results = $db->get_results("SELECT tbl_product.ProductName, tbl_product.ProductImage, tbl_order_details.Quantity, tbl_order_details.Rate, tbl_order_details.Amount, tbl_order.TaxAmount, tbl_order.DeliveryCharges, tbl_order.TotalAmount FROM `tbl_order_details` JOIN tbl_product ON tbl_order_details.ProductId = tbl_product.ProductId JOIN tbl_order ON tbl_order.OrderId = tbl_order_details.OrderId WHERE tbl_order_details.Available <> -1 AND tbl_order_details.OrderId = " . $order_id);

    $email = '';
    $total = 0;

    foreach($results as $result) {
        $total += $result->Rate * $result->Quantity;
    }

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
                        <span class="span" style="font-size: 17px; font-weight: bold; line-height: 20px;">$' . sprintf("%.2f", round(($result->Amount + $result->Amount *  0.1),2)) . '</span>
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
    $subtotal = round(($total + ($total * 0.1)),2);
    $taxes = current($results)->TaxAmount;
    $total = $subtotal + $taxes + current($results)->DeliveryCharges;
    return [ 
        'details' => $email,
        'subtotal' => sprintf("%.2f", $subtotal),
        'total' => sprintf("%.2f", $total),
        'shipping' => current($results)->DeliveryCharges,
        'taxes' => sprintf("%.2f", $taxes)
    ];
}

function send_order_email( $name, $email, $order_id )
{
    $subject = 'ToolBX - Order #TB-' . (181110 + $order_id);

    $order_info = getOrderDetails($order_id);

    $data = [
        [
            'name' => 'NAME',
            'content' => $name
        ],
        [
            'name' => 'ORDERID',
            'content' => 'TB-' . (181110 + $order_id)
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
}

function send_order_cancel_email( $name, $email, $order_id )
{
    $subject = 'ToolBX - Order #TB-' . (181110 + $order_id);

    $order_info = getOrderDetails($order_id);

    $data = [
        [
            'name' => 'ORDERID',
            'content' => 'TB-' . (181110 + $order_id)
        ],
        [
            'name' => 'ORDERDETAILS',
            'content' => $order_info['details'],
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
    ];

    return tbx_ajitem_order_cancel_mail($name, $email, $subject, $data);
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
}



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
            '$desktop_url'=>'https://itunes.apple.com/ca/app/keynote/id1320999107?mt=8',
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


function tbx_app_status(Request $request, Response $response) 
{
    $parameters = $request->getParams();

    if(null !== $parameters['dev'] && $parameters['dev'] == true) {
        return $response->withJson([
            'status' => true,
        ], 200);
    }
    // $objTimeZone = new DateTimeZone($parameters['timezone']);
    $objTimeZone = new DateTimeZone('America/Toronto');


    $objStartTime = new DateTime("now", $objTimeZone);
    $objStartTime->setTime(07,00);

    $objEndTime = new DateTime("now", $objTimeZone);
    $objEndTime->setTime(17,00);

    $objNow = new DateTime("now", $objTimeZone);

    if( $objNow > $objStartTime && $objNow < $objEndTime) {
        return $response->withJson([
            'status' => true,
        ], 200);
    } else {
        return $response->withJson([
            'status' => false,
        ], 200);
    }
}