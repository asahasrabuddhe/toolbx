<?php
 

$app->get( '/runner/listrunners', 'tbx_runner_get_all');
$app->get( '/runner/{id:[0-9]+}/edit', 'tbx_user_get_edit_single');
$app->post('/user/update_user', 'tbx_runner_update');
$app->delete('/user/{id:[0-9]+}/delete_user', 'tbx_user_delete');

//4th aprl 
$app->get( '/company/companylistadmin', 'tbx_get_all_company_admin');

$app->get( '/company/{id:[0-9]+}/edit_comapny', 'tbx_get_edit_company_admin');
$app->post( '/company/update', 'tbx_update_company_admin');



$app->get( '/company/companylist', 'tbx_get_all_company');
$app->delete('/company/{id:[0-9]+}/delete_company', 'tbx_company_delete');
$app->get('/owner/{cmpid:[0-9]+}/ownerlist', 'tbx_owner_get_all');


$app->get( '/owner/{id:[0-9]+}/edit', 'tbx_owner_get_edit_single'); 
$app->post('/owner/update', 'tbx_owner_update'); 

$app->get('/employee/{cmpid:[0-9]+}/employeelist', 'tbx_employee_get_all');
$app->get( '/employee/{id:[0-9]+}/edit', 'tbx_employee_get_edit_single');
$app->post( '/employee/update', 'tbx_employee_update');
$app->post('/employee/invitation', 'tbx_emp_invitation'); //function.company


$app->get( '/category/category_list', 'tbx_category_list');//->add($auth);//Category Name
$app->get( '/category/{id:[0-9]+}/category', 'tbx_subcategory_list');//->add($auth); //From category getting subcategory list
//$app->get( '/category/all', 'tbx_subcategory_listAll'); //From category getting subcategory list All


$app->get('/user/{id:[0-9]+}/user_profiledata', 'user_profiledata');


/********** ADMIN PRODUCTS API (MENU) ****/

$app->get('/product/product_list', 'tbx_product_list'); //product list
$app->post('/product/addproduct', 'tbx_add_products');
$app->delete('/product/{id:[0-9]+}/deleteproduct', 'tbx_product_delete');//->add($auth);
$app->get('/product/{id:[0-9]+}/productedit', 'tbx_product_get_edit_single');
$app->post('/product/update', 'tbx_product_update_data');

/****************************************/



//Mobile User
$app->post( '/user/login', 'tbx_user_login' );

$app->post( '/user/invitation', 'tbx_user_invitation' ); //function.apiuser 
$app->post('/owner/invitation', 'tbx_user_owner_invitation'); // check company then send invitation

$app->get( '/user/{id:[0-9]+}/invitation', 'tbx_user_invitation_data'); // user all details
$app->post( '/user/forgotpassword', 'tbx_user_forgotpassword');
$app->post( '/user/{id:[0-9]+}/changepassword', 'tbx_user_changepassword' );


$app->post('/employee/app/invitation', 'tbx_emp_invitation_from_app'); //function.company

$app->get( '/category/{id:[0-9]+}/subcategory', 'tbx_subcategory_list');//->add($auth); //From category getting subcategory list

$app->get( '/category/categorylist', 'tbx_category_list');//->add($auth);//Category Name
$app->get( '/category/catandsubcat', 'tbx_category_menus'); //category and subcategory list

/* 17/04/2017 */
$app->get('/product/{id:[0-9]+}/product_list', 'tbx_product_details');
$app->get('/product/{id:[0-9-]+}/productlist', 'tbx_product_details');

// 21/04/2017
$app->post('/product/order', 'tbx_order'); // order by owner or employee
$app->post('/product/productorder', 'tbx_order_product'); //order by Runner

//24/04/2017
$app->get('/user/{id:[0-9]+}/useraccountcreditprofile', 'tbx_user_account_credit_details'); // Get profile account and credit information
$app->post('/user/{id:[0-9]+}/useraccountcreditprofileupdate', 'tbx_user_account_credit_details_update'); // Get profile for update/edit account and credit information
  
$app->post('/user/addjobsite', 'tbx_add_jobsite');
$app->get('/user/jobsitelist', 'tbx_jobsitelist');
$app->get('/user/{id:[0-9]+}/jobsiteedit', 'tbx_get_edit_single_jobsite');
$app->post('/user/jobsitesupdate', 'tbx_update_jobsite');

$app->post('/user/{id:[0-9]+}/jobsitelist', 'tbx_jobsitelist_onid'); // showing related to registrationid/user_id (Owner & Emp)
$app->post('/user/{jobsiteid:[0-9]+}/deleteJobsite', 'tbx_jobsite_delete_onid'); // delete jobsite on jobsite id

// 25/04/2017
$app->get('/runner/orderlist', 'tbx_runner_order_list');
$app->get('/runner/{id:[0-9]+}/orderdetails', 'tbx_runner_order_details');
$app->get('/owner/{id:[0-9]+}/orderdetails', 'tbx_owner_order_details');


$app->post('/runner/{id:[0-9]+}/acceptederror', 'tbx_runner_accept_order');
$app->post('/runner/{id:[0-9]+}/leavingstore', 'tbx_runner_leaving_store');
$app->post('/runner/{id:[0-9]+}/ordewrdeliver', 'tbx_runner_deliverorder');

$app->post('/runner/{id:[0-9]+}/updateitem', 'tbx_runner_update_item');

$app->post('/runner/{id:[0-9]+}/onlineoffline', 'tbx_runner_onlineoffline');
//02/05/2017
$app->post('/runner/{id:[0-9]+}/CancelOrder', 'tbx_runner_cancel_order');
//27/04/2017
$app->post('/product/cartitemcount', 'tbx_cart_item_count');
$app->post('/product/cartshowitam', 'tbx_cart_show');
$app->post('/product/cartitamedit', 'tbx_cart_edititam');

$app->post('/user/userresetpasswordonprofile', 'tbx_user_profileresetpassword');

//28/04/2017
$app->post('/product/createproductorder', 'tbx_order_create');

//22/05/2017
$app->post('/runner/{id:[0-9]+}/UpdateCurrentOrderId', 'tbx_current_order_id');

//23/05/2017
//$app->post('/user/notification', 'tbx_Notifications'); //single

$app->post('/user/SaveNotificationToken', 'tbx_notification_token_save'); //owner/employee
$app->post('/runner/{id:[0-9]+}/RunnerNotificationOnOrder', 'tbx_order_notification_all');
$app->post('/user/{id:[0-9]+}/employeeordernotification', 'employee_creating_order_notification');

$app->get('/product/{SubCatId:[0-9]+}/SubCategoryProductList', 'tbx_sub_CategoryProductList');

$app->get( '/order/{CmpId:[0-9]+}/listorders', 'tbx_order_details');
 


$app->get('/order/{id:[0-9]+}/viewsingleorders', 'tbx_order_view_single_detaile'); 
$app->get('/order/{id:[0-9]+}/vieworderdetails', 'tbx_order_view_headsec_detail'); 

//admin payment list
$app->get('/company/{cmpid:[0-9]+}/paymentlist', 'tbx_payment_details');

//070617
$app->get('/product/{RegistrationId:[0-9]+}/ReceantPurchasesList', 'tbx_recent_purchases');
$app->get('/user/OnlineRunnerList', 'tbx_allrunner_onlineoffline');

$app->get('/user/{id:[0-9]+}/CardDetailsAvailability', 'tbx_user_card_details_available');

$app->get('/user/{registrationid:[0-9]+}/OrderStatus', 'tbx_order_status'); //owner/employee order status acceptedor not

/*08/08/2017**/
//search
$app->post('/search/SearchSubcategory', 'tbx_search_subcategory'); // search subcategory 
$app->post('/search/SearchProduct', 'tbx_search_product'); // search subcategory 
$app->post('/search/SearchProductNameOnCatSubcatId', 'tbx_search_product_name'); // search subcategory 

$app->post('/Product/{CatId:[0-9-]+}/LazyLoading', 'tbx_product_details_lazyloading'); // lazyloading

$app->post('/user/{RegId:[0-9]+}/OwnerEmployeeCancelOrder', 'tbx_owner_Employee_cancel_order');
  

$app->get('/runner/{RunnerId:[0-9]+}/OrdersDetails', 'tbx_runner_orders_status'); // runner Orders on admin runner view
$app->get('/order/{OrderId:[0-9]+}/OrderViewDataPdf', 'pdf_Order_view_data');


$app->post('/Product/{SubcatId:[0-9-]+}/SubCatLazyLoading', 'tbx_product_details_lazyloading_subcategory');



$app->get('/user/smtpmail',"sendsmtpmail");

//$app->get('/user/email',"SendSMTPMailCommon");

$app->post('/users/StringCartInsert',"tbx_cartitem");
$app->get('/users/{id:[0-9]+}/clearcart',"tbx_cartclear");


$app->post('/user/{id:[0-9]+}/orderhistory','tbx_owneremployee_order_history');

$app->get('/user/{id:[0-9]+}/employeelist','tbx_order_employee_list');

$app->get("/user/{orderid:[0-9]+}/orderpdf","tbx_order_export_PDF");
$app->get("/user/{orderid:[0-9]+}/orderinvoice","tbx_order_export_PDF_invoice_send");

$app->get('/user/{orderid:[0-9]+}/orderstatusword', 'tbx_order_status_words');

/**
 * AJITEM EDIT: 27/10/2017 - Orders API
 */

// GET ALL ORDERS

$app->get('/orders', 'tbx_orders_list_all');
$app->get('/orders/{orderId:[0-9]+}', 'tbx_orders_details');
$app->get('/orders/csv', 'tbx_orders_export');

/** AJITEM EDIT END */

$app->get('/user/productjson', 'tbx_product_json');
$app->post('/product/jsonupdated', 'tbx_product_jsonupdated');


$app->get('/user/getthresold', 'tbx_product_priceThresold');

$app->post('/runner/{id:[0-9]+}/insertitem', 'tbx_runner_insert_item');

$app->get('/app/status', 'tbx_app_status');