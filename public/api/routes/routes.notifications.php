<?php
 
$app->GET( '/user/{id:[0-9]+}/notifications', 'tbx_notification_list' );

$app->GET( '/user/notificationread/{id:[0-9]+}', 'tbx_notification_markasread' );

$app->GET( '/user/notificationdelete/{id:[0-9]+}', 'tbx_notification_markasdeleted' );

$app->GET( '/user/rating/{id:[0-9]+}/{rating:[0-5]+}', 'tbx_notification_updaterating' );