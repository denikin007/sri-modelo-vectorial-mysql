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

Route::get('/', function () {
    return view('buscador.index');
});
Route::get('/subirArchivo',function(){
    return view('buscador.subirArchivo');
});


Route::get('/calcular/{palabra}','WordController@vectorial');

Route::post('/indexar','IndexController@indexar');

Route::get('/document/{id}','DocumentController@getDoc');