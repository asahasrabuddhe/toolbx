<?php

function tbx_ajitem_invitation_mail($name, $email, $subject, $data)
{
	$template_name = 'Account Invite';

	$result = tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data);

}

function tbx_ajitem_confirmation_mail($name, $email, $subject, $data)
{
	$template_name = 'Toolbx Welcome Email';

	$result = tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data);

}

function tbx_ajitem_reset_password_mail($name, $email, $subject, $data)
{
	$template_name = 'Password Reset';

	$result = tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data);
}

function tbx_ajitem_order_mail($name, $email, $subject, $data)
{
	$template_name = 'Thanks For Your Order!';

	$result = tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data);
}

function tbx_ajitem_order_cancel_mail($name, $email, $subject, $data)
{
	$template_name = 'Cancelled Order';

	$result = tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data);
}

function tbx_ajitem_mandrill_send($template_name, $name, $email, $subject, $data)
{
	try {
		$mandrill = new Mandrill('0Wp8x9iolGgoQkTvKPgbfw');
		
		$template_content = array(
	        array(
	            'name' => 'example name',
	            'content' => 'example content'
	        )
	    );
	    $message = array(
	        'subject' => $subject,
	        'from_email' => 'info@toolbx.com',
	        'from_name' => 'ToolBX Support',
	        'to' => array(
	            array(
	                'email' => $email,
	                'name' => $name,
	                'type' => 'to'
	            )
	        ),
	        'return_path_domain' => null,
	        'merge' => true,
	        'merge_language' => 'mailchimp',
	        'global_merge_vars' => $data
	    );

	    $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
	   
	} catch(Mandrill_Error $e) {
	    // Mandrill errors are thrown as exceptions
	    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
	    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
	    throw $e;
	}	
}