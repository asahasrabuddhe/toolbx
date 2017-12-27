<?php


$app->post('/user/ChkCardDetails', 'tbx_user_card'); //validate card details
$app->post('/user/orderpayment', 'tbx_user_payments'); //payment on order placing



//$app->post('/user/orderpaymenttesting', 'tbx_user_testing_payments'); //payment on order placing
//$app->get('/user/{orderid:[0-9]+}/CardDetails', 'tbx_payment_card_details_onorderid'); // getting card details on order id
