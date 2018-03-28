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

Route::get('/', 'HomeController@index')->name('index');

Auth::routes();

Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

Route::get('/apply-tickets', 'Front\TicketsController@applyCheap')->name('apply-tickets');
Route::get('/generate-ryanair', 'Front\TicketsController@generateRyanair')->name('generate-ryanair');
Route::get('/generate-wizzair', 'Front\TicketsController@generateWizzair')->name('generate-wizzair');





/*Ajax*/
Route::get('/ajax/fligths/ganarate', 'Front\AjaxTicketsController@ajaxFlightGeneration')->name('ajax-fligths-generation');
Route::post('/ajax/tickets/fare-finder', 'Front\TicketsController@ajaxFareFinder')->name('ajax-fare-finder');
Route::post('/ajax/tickets/airport-finder', 'Front\TicketsController@ajaxAirportFinder')->name('ajax-airport-finder');
/*/Ajax*/



Route::get('/test', 'Front\TestsController@index')->name('test');
