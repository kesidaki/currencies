<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
| GET (index)
| ~/currencies
|---------------------------------------------------
| GET (show)
| ~/currencies/{id}
|---------------------------------------------------
| POST (store)
| ~/currencies
|---------------------------------------------------
| PUT/PATCH (update)
| ~/currencies/{id}
|---------------------------------------------------
| DELETE (destroy)
| ~/currency/{id}
*/
Route::resource('currencies', 'Currency\CurrencyController')->except([
	'create',
	'edit'
]);

/*
| GET (index)
| ~/currency/{id}/ratios
|---------------------------------------------------
| GET (show)
| ~/currency/{id}/ratios/{id}
|---------------------------------------------------
| POST (store)
| ~/currency/{id}/ratios
*/
Route::resource('currencies.ratios', 'Currency\CurrencyRatioController')->only([
	'index',
	'store',
	'show'
]);


/*
| PUT/PATCh (update)
| ~/rates/{id}
|---------------------------------------------------
| DELETE (destroy)
| ~/rates/{id}
*/
Route::resource('rates', 'Rates\RatesController')->only([
	'update',
	'destroy'
]);

/*
| GET (index)
| ~/convert?base={base_currency_id}&target={target_currency_id}&value{value}
*/
Route::get('convert', 'ConverterController@index');