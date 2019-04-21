<?php

use Illuminate\Support\Facades\Route;

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

Route::namespace('Api\V1')
     ->name('api.v1.')
     ->prefix('v1')
     ->group(function () {
         Route::post('files', 'UploadFile')->name('upload-file');
         Route::get('files/{token}', 'DownloadFile')->name('download-file');
     });
