<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require('simple_html_dom.php');
/*
* Admin login    
*/
function tbx_admin_login( Request $request, Response $response )
{


	$db = database();
	$body = $request->getParsedBody();
	$email = $body['Email'];
	$password = $body['Password'];
	
	
	$base_query = $db->get_row('SELECT * FROM `tbl_administrator`  WHERE tbl_administrator.admin_email= "' . $email. '"');
	
	if( $base_query )
	{
		
		
		if( password_verify($password, $base_query->admin_password) )
		{  		
			$base_query->admin_password = "";
			$base_query->token = generate_token( $base_query->admin_id);
			
			$res = array( 'message_code' => 1000, 'data_text' => $base_query );
		}
		else
		{
			$res = array( 'message_code' => 999, 'message_text' => 'Login failed. Password did not match.');
		}
	}
	else
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Login failed. Email is not registered in system.');
	}

	return $response->withJson( $res, 200 );
}

/*
* Admin Change Password.
*/
function tbx_admin_changepass( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();	

	$body = $request->getParsedBody();
	$user_id = $body['id'];
	$old_password = $body["old_password"];
	$new_password = $body["new_password"];
    $hash_pass = password_hash($new_password, PASSWORD_BCRYPT);

	$base_query = $db->get_row('SELECT admin_id, admin_password FROM `tbl_administrator` WHERE admin_id=' . $user_id);
	
	if(!password_verify($old_password, $base_query->admin_password))
	{
		$res = array( 'message_code' => 999, 'message_text' => 'Invalid old password. Cannot update new password.');
	}
	else
	{
		$db->query( 'UPDATE tbl_administrator SET admin_password = "' . $hash_pass . '" WHERE admin_id = ' . $base_query->admin_id );
		$res = array( 'message_code' => 1000, 'message_text' => 'Password reset successfully.');
	}
	
	
	return $response->withJson( $res, 200 );
}

/*
*  Admin Forgot Password.
*/
function tbx_admin_forgotpassword( Request $request, Response $response )
{
	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );

	$db = database();
	$body = $request->getParsedBody();
	$email = $body["Email"];
	
	$base_query = $db->get_row('SELECT admin_id, admin_name FROM `tbl_administrator` WHERE `admin_email` = LCASE("' . $email . '") AND active= 1' );
	if(!$base_query )
	{
		$res = array( 'message_code' => 999, 'message_text' => 'This email address is not registred with ToolBx. Please contact administrator.');
	}
	else
	{
		$password = random_password();
        $hash_pass = password_hash($password, PASSWORD_BCRYPT);
		
		$db->query( 'UPDATE tbl_administrator SET admin_password = "' . $hash_pass . '" WHERE admin_id = ' . $base_query->admin_id );
		send_password( $base_query->admin_name, $email, $password );
		$res = array( 'message_code' => 1000, 'message_text' => 'Password reset successfully. New password is sent via email to you.');
	}
	return $response->withJson( $res, 200 );
}


    /*
    * Admin update account info.
    */
    function tbx_admin_account_info_update( Request $request, Response $response )
    {
    	$res = array( 'message_code' => 999, 'message_text' => 'Functional part is commented.' );
    
    	$db = database();	
    
    	$body = $request->getParsedBody();
    	$user_id = $body['id'];
    	$email = $body["Email"];
    	$name = $body["Name"];
    	//$contactNo = $body["PhoneNumber"];
    	$lastmodifiedby = $body["lastmodifiedby"];
    	
    	if (($name == null) || ($name==""))
		    $res = array( 'message_code' => 999, 'message_text' => 'Name cannot be blank.');
    	else
    	{
            
        	$base_query = $db->query('UPDATE `tbl_administrator` SET admin_name="'.$name.'", admin_email= "'.$email.'", modified_by="'.$lastmodifiedby.'" WHERE display=\'Y\' AND admin_id=' . $user_id);
        	
        	if($base_query === false)
        	{
        		$res = array( 'message_code' => 999, 'message_text' => 'Please try later.');
        	}
        	elseif($base_query == 0)
        	{
        		$res = array( 'message_code' => 1000, 'message_text' => 'Account information updated successfully.');
        	}
        	else
        	{
        		$res = array( 'message_code' => 1000, 'message_text' => 'Account information updated successfully.');
        	}
    	} 
    	
    	return $response->withJson( $res, 200 );
    }


function tbx_pull_product(Request $request, Response $response)
{
    
    //http://www.homedepot.ca/en/home/categories/tools/power-tools/combo-kits.html?pageSize=96
    $res = array( 'message_code' => 999, 'message_text' => 'Functionality is not implemented yet.');
    $counter = 0;
    $db = database();
	$body = $request->getParsedBody();
	$url = $body["url"];
    $ToolBxCategory = $body["CategoryId"];
    $ToolBxSubCategory = $body["SubCategoryId"];
    $PageStart = $body["PageStart"];
    $PageEnd = $body["PageEnd"];

    if (intval($PageEnd)==0)
        $PageEnd = $PageStart;

    echo "<br/>URL: " . $url  . "<br/>";
    $arrtemp = explode("/",$url);
    echo "Category: " . $arrtemp[sizeof($arrtemp) - 3] . "<br/>";
    echo "Sub Category: " . $arrtemp[sizeof($arrtemp) - 2] . "<br/>";
    echo "Product Type: " . substr($arrtemp[sizeof($arrtemp) - 1],0,strpos($arrtemp[sizeof($arrtemp) - 1], ".html"))  . "<br/>";
   
    $failedCounter = 0;
    $failedProducts = "";
    $counter = 0;
    $query = "SELECT MAX(ProductId) as id from `tbl_homedepot_new`";
    $id = $db->get_var($query);
    

    for ($i=intval($PageStart); $i<=intval($PageEnd);$i++)
    {

        echo $url . "?page=" . $i . "<br/>";
    $header = get_web_page($url . "?page=" . $i, '');
    
    $doc = new DOMDocument();
    $htmlstr = html_entity_decode($header['content']); 
    $htmlobj = str_get_html($htmlstr);
    
    $pName = "";
    $pDetails = "";
    $pPrice = "0.00";
    $pImage = "";
    $ImageFullPath = "";
    
    $pDet = "Category:" . $arrtemp[sizeof($arrtemp) - 3] . ", Sub Category:" . $arrtemp[sizeof($arrtemp) - 2] . ", Product Type:" . substr($arrtemp[sizeof($arrtemp) - 1],0,strpos($arrtemp[sizeof($arrtemp) - 1], ".html"));
       // echo $pDet;
        
        


    foreach($htmlobj->find('article') as $article)
    {

        $pDetails = $pDet;
        $pName = "";
        $pPrice = "0.00";
        $pImage = "";
        $ImageFullPath = "";
        
        foreach($article->find('div') as $product)
        {   
            if ($product->class=="product-title")
            {
                $pDetails = $pDetails . "-" . trimall(str_replace("'","\'", $product->innertext));

                //preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/","",$product->innertext);
                $pName = $pName . trimall(str_replace("'","\'", $product->innertext));
                //preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/","",$product->innertext);
            }
            else if ($product->class=="product-name")

                $pName = $pName . "-" . trimall(str_replace("'","\'", $product->innertext));

                //echo "0" . $pName . "1<br/>";

                //preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/","",$product->innertext);
            else if ($product->class=="product-price")
            {
                foreach($product->find('span') as $sp)
                { 
                    if ($sp->class=="product-display-price")
                        $pPrice = $sp->innertext;
                }
            }
            else if ($product->class=="product-image")
            {
                foreach($product->find('img') as $img)
                { 
                    if (stripos($img->src,"$plpProduct$")>0)
                    {
                         $pImage = $img->src;
                         $ImageFullPath = $img->src;

                         $ImageFullPath = trimall(substr($ImageFullPath,0,strripos($ImageFullPath,"?")));
                         //echo strlen($pImage) . " == " . strripos($pImage,"?") . " " . strripos($pImage,"/");
                        $pImage = substr($pImage,0,strripos($pImage,"?"));
                        //echo "Full URL: " . $pImage . "<br/>";
                        $pImage = substr($pImage, strripos($pImage,"/")+1, strlen($pImage)-strripos($pImage,"/"));
                        //echo $pImage . "<br/>";
                    }
                }
            }
            else if ($product->class=="product-model")
                $pDetails = $pDetails . $product->innertext;
            else if ($product->class=="product-model storeSKU")
            {
                $pDetails = $pDetails . trimall(str_replace("'","\'",$product->innertext));

                $SKU = str_replace("Store SKU","",$product->innertext);
                $SKU = str_replace("Â ","",$SKU);
                $SKU = trimall(str_replace("'","\'", $SKU ));
                
                //preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/","",$SKU);
            }
        }
        
        //echo $pPrice . "<br/>";
        if ($pPrice =="-")
            $pPrice = "0.00";
       
       $query = "SELECT count(*) as cnt from `tbl_homedepot_new` WHERE SKUNumber='" . $SKU ."'";
       $pCnt = $db->get_var($query);

       //echo $query . "<br/>";
       
       if ($pCnt == 0)
       {
            if ($pPrice != "0.00")
                $pPrice = str_replace("$","",$pPrice);
            $id++;

            $query = "INSERT INTO `tbl_homedepot_new` (`ProductId`, `ProductName`, `ProductDetails`, `Rate`, `CategoryId`, `SubCategoryId`, `ProductImage`, `DeletedBy`, `DeletedOn`, `CreatedBy`, `CreatedOn`, `LastModifiedBy`, `LastModifiedOn`, `display`, `SKUNumber`, `ImageFullPath`) VALUES (" . $id . ", '" . $pName . "', '" . $pDetails . "', '" . $pPrice . "', '" . $ToolBxCategory . "', '" . $ToolBxSubCategory . "', '" . $pImage . "', '', NULL, '1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, 'Y', '" . $SKU . "',' " . $ImageFullPath . "')";
            //echo $query . "<br/>";
            if ($db->query($query))
                $counter++;
            else
                echo "Error: " . $query . "-" . $db->error . "<br/>";
        // }
        //     else{
        //         $failedProducts = $failedProducts . $pName . " | ";
        //         $failedCounter++;
        //         echo $pName . "<br/>";
        //     }
       }
          
    }   

    }

    $res = array( 'message_code' => 1000, 'message_text' => $counter . ' product(s) imported and ' . $failedCounter . ' product(s) failed.');
    return $response->withJson( $res, 200 );
}

function trimall($str)
{
    $str = trim($str);
    $str = trim($str,"\t");
    $str = trim($str,"\0");
    $str = trim($str,"\n");
    $str = trim($str,"\x0B");
    $str = trim($str,"\r");

    return $str;
  
}

function get_web_page( $url, $cookiesIn = '' ){
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,     //return headers in addition to content
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_COOKIE         => $cookiesIn
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $rough_content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header_content = substr($rough_content, 0, $header['header_size']);
        $body_content = trim(str_replace($header_content, '', $rough_content));
        $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m"; 
        preg_match_all($pattern, $header_content, $matches); 
        $cookiesOut = implode("; ", $matches['cookie']);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['headers']  = $header_content;
        $header['content'] = $body_content;
        $header['cookies'] = $cookiesOut;
        
    return $header;
}


function send_mail(Request $request, Response $response)
{
    $to = "SLimje@Torinit.com";
    $subject = "Welcome to ToolBX!";
     
    $message = "Dear Sunil,\r\n\r\n";
    $message .= "You are invited to ToolBX app. Please join the app using following link\r\n\r\n\r\n";
    $message .= "<a href='https://www.google.com'>Go to Google</a>";
    $message .= "\r\n\r\nKind Regards,\r\nToolBX Admin\r\n\r\n\r\n";
    
     
    $header = "From:ToolBX Admin <support@toolbx.com>";
    $header .= "MIME-Version: 1.0";
    $header .= "Content-type: text/html";

    //  ini_set('SMTP','mail.applinktest.in');
    //  ini_set('sendmail_from','Support@applinktest.in');
    //  //ini_set('smtp_user','Support@applinktest.in');
    //  //ini_set('smtp_pass','uIysicr0Hg2t');
    //  ini_set('username','Support@applinktest.in');
    //  ini_set('password','uIysicr0Hg2t');
    //  ini_set('smtp_port',25);
    
    
    //mail( $to, $subject, $message, $header);
    //mail( $to, $subject, $message);
    
    $from_email = "ToolBx Admin";
    mail( $to, $subject, $message , $header, "-f$from_email");
    echo "done";
    return "done";
}
