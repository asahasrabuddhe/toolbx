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

Route::view('/admin/user/list_users', 'admin.users.list_users');
Route::view('/admin/user/invite', 'admin.users.invite');
Route::view('/admin/user/{id}/view', 'admin.users.view');
Route::view('/admin/employee/{id}/view', 'admin.users.view');
Route::view('/admin/user/{id}/edit', 'admin.users.edit');
Route::view('/admin/employee/{id}/edit', 'admin.users.edit');
Route::view('/admin/owner/{id}/edit', 'admin.users.edit');

Route::post('/admin/user/invite', 'RunnerController@invite');
Route::get('/runners', 'RunnerController@getAllRunners');
Route::get('/runners/{id}', 'RunnerController@getRunner');
Route::post('/runners/{id}/update', 'RunnerController@updateRunner');
Route::post('/employee/{id}/update', 'RunnerController@updateRunner');
Route::post('/owner/{id}/update', 'RunnerController@updateRunner');
Route::get('/admin/user/{id}/delete', 'RunnerController@deleteRunner');
Route::get('/admin/employee/{id}/delete', 'RunnerController@deleteRunner');
Route::get('/admin/owner/{id}/delete', 'RunnerController@deleteRunner');

Route::view('/admin/company/list_companies', 'admin.company.list_companies');
Route::view('/admin/owner/invite', 'admin.company.invite');
Route::view('/admin/company/{id}/view', 'admin.company.view');
Route::view('/admin/company/{id}/edit', 'admin.company.edit');

Route::post('/admin/owner/invite', 'RunnerController@invite');
Route::get('/companies', 'CompaniesController@getAllCompanies');
Route::get('/company/{id}', 'CompaniesController@getOwner');
Route::post('/company/{id}/update', 'CompaniesController@updateOwner');
Route::get('/admin/company/{id}/delete', 'CompaniesController@deleteOwner');
Route::get('/company/{id}/orders', 'CompaniesController@getOrders');
Route::get('/company/{id}/employees', 'CompaniesController@getEmployees');
Route::get('/company/{id}/owners', 'CompaniesController@getOwners');
Route::get('/company/{id}/payments', 'CompaniesController@getPayments');

Route::view('/admin/product/list_products', 'admin.product.list_products');
Route::view('/admin/product/add', 'admin.product.add');
Route::view('/admin/product/{id}/view', 'admin.product.view');
Route::view('/admin/product/{id}/edit', 'admin.product.edit');
Route::post('/products', 'ProductController@addProduct');

Route::get('/products', 'ProductController@getAllProducts');
Route::get('/categories', 'CategoryController@getAllCategories');
Route::get('/categories/{id}/sub_categories', 'CategoryController@getAllSubCategories');

Route::view('/admin/order/list_orders', 'admin.order.list_orders');
Route::get('/orders', 'OrdersController@getAllOrders');
