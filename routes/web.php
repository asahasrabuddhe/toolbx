<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'index');
Route::view('/login', 'index');
Route::post('/login', 'Auth\LoginController@login');
Route::view('/forgot_password', 'forgot_password');

Route::post('/forgot_password', 'Auth\LoginController@forgotPassword');
Route::get('/admin/logout', 'Auth\LoginController@logout')->middleware('checksession');

Route::view('/admin/user/list_users', 'admin.users.list_users')->middleware('checksession');
Route::view('/admin/user/invite', 'admin.users.invite')->middleware('checksession');
Route::view('/admin/user/{id}/view', 'admin.users.view')->middleware('checksession');
Route::view('/admin/employee/{id}/view', 'admin.users.view')->middleware('checksession');
Route::view('/admin/user/{id}/edit', 'admin.users.edit')->middleware('checksession');
Route::view('/admin/employee/{id}/edit', 'admin.users.edit')->middleware('checksession');
Route::view('/admin/owner/{id}/edit', 'admin.users.edit')->middleware('checksession');
Route::view('/admin/account', 'admin.users.edit')->middleware('checksession');
Route::view('/admin/change_password', 'admin.change_password')->middleware('checksession');

Route::post('/admin/user/invite', 'RunnerController@invite')->middleware('checksession');
Route::get('/runners', 'RunnerController@getAllRunners')->middleware('checksession');
Route::get('/runners/{id}', 'RunnerController@getRunner')->middleware('checksession');
Route::post('/runners/{id}/update', 'RunnerController@updateRunner')->middleware('checksession');
Route::post('/employee/{id}/update', 'RunnerController@updateRunner')->middleware('checksession');
Route::post('/owner/{id}/update', 'RunnerController@updateRunner')->middleware('checksession');
Route::get('/admin/user/{id}/delete', 'RunnerController@deleteRunner')->middleware('checksession');
Route::get('/admin/employee/{id}/delete', 'RunnerController@deleteRunner')->middleware('checksession');
Route::get('/admin/owner/{id}/delete', 'RunnerController@deleteRunner')->middleware('checksession');
Route::post('/admin/update', 'RunnerController@updateAdmin')->middleware('checksession');
Route::post('/admin/change_password', 'RunnerController@changeAdminPassword')->middleware('checksession');

Route::view('/admin/company/list_companies', 'admin.company.list_companies')->middleware('checksession');
Route::view('/admin/owner/invite', 'admin.company.invite')->middleware('checksession');
Route::view('/admin/company/{id}/view', 'admin.company.view')->middleware('checksession');
Route::view('/admin/company/{id}/edit', 'admin.company.edit')->middleware('checksession');

Route::post('/admin/owner/invite', 'CompaniesController@invite')->middleware('checksession');
Route::get('/companies', 'CompaniesController@getAllCompanies')->middleware('checksession');
Route::get('/company/{id}', 'CompaniesController@getOwner')->middleware('checksession');
Route::post('/company/{id}/update', 'CompaniesController@updateOwner')->middleware('checksession');
Route::get('/admin/company/{id}/delete', 'CompaniesController@deleteOwner')->middleware('checksession');
Route::get('/company/{id}/orders', 'CompaniesController@getOrders')->middleware('checksession');
Route::get('/company/{id}/employees', 'CompaniesController@getEmployees')->middleware('checksession');
Route::get('/company/{id}/owners', 'CompaniesController@getOwners')->middleware('checksession');
Route::get('/company/{id}/payments', 'CompaniesController@getPayments')->middleware('checksession');

Route::view('/admin/product/list_products', 'admin.product.list_products')->middleware('checksession');
Route::view('/admin/product/add', 'admin.product.add')->middleware('checksession');
Route::view('/admin/product/{id}/view', 'admin.product.view')->middleware('checksession');
Route::view('/admin/product/{id}/edit', 'admin.product.edit')->middleware('checksession');
Route::post('/products', 'ProductController@addProduct')->middleware('checksession');

Route::get('/products', 'ProductController@getAllProducts')->middleware('checksession');
Route::post('/product/{id}/update', 'ProductController@updateProduct')->middleware('checksession');
Route::get('/admin/product/{id}/delete', 'ProductController@deleteProduct')->middleware('checksession');
Route::get('/categories', 'CategoryController@getAllCategories')->middleware('checksession');
Route::get('/categories/{id}/sub_categories', 'CategoryController@getAllSubCategories')->middleware('checksession');

Route::view('/admin/order/list_orders', 'admin.order.list_orders')->middleware('checksession');
Route::view('/admin/order/{id}/view', 'admin.order.view')->middleware('checksession');
Route::get('/orders', 'OrdersController@getAllOrders')->middleware('checksession');
Route::get('/orders/{id}/details', 'OrdersController@getOrder')->middleware('checksession');

Route::get('/admin/companies/export', 'CompaniesController@getAllCompaniesCsv')->middleware('checksession');
Route::get('/admin/orders/export', 'OrdersController@getAllOrdersCsv')->middleware('checksession');
Route::get('/admin/order/{id}/export', 'OrdersController@exportOrderPdfInvoice');
Route::get('/admin/order/{id}/invoice', 'OrdersController@exportOrderPdfInvoice');

Route::view('/admin/employee/invite', 'admin.company.invite')->middleware('checksession');
Route::post('/admin/employee/invite', 'RunnerController@invite')->middleware('checksession');
