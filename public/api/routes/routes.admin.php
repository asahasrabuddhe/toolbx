<?php
 

//Admin User
$app->post( '/admin/adminlogin', 'tbx_admin_login' );
$app->post( '/admin/changepassword', 'tbx_admin_changepass' );
$app->post( '/admin/forgotpassword', 'tbx_admin_forgotpassword' );
$app->post( '/admin/updateaccountinfo', 'tbx_admin_account_info_update' );



$app->post( '/admin/pullproduct', 'tbx_pull_product' );
$app->get( '/admin/sendmail', 'send_mail' );

$app->get('/admin/productjson','tbx_product_json');