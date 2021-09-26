<?php

Route::get('/log-reader/index', 'LogReaderController@index');
Route::get('/log-reader/view', 'LogReaderController@view');
Route::get('/log-reader/tail', 'LogReaderController@tail');
Route::get('/log-reader/download', 'LogReaderController@download');
Route::get('/log-reader/delete', 'LogReaderController@delete');
