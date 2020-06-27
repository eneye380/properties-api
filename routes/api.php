<?php

/**
 * Route::prefix('v1')->group(
    function () {
        Route::resource(
            'property', 'PropertyController', [
            'except'=>['edit','create']
            ]
        );

        Route::resource(
            'property/assign', 'AssignController', [
            'only'=>['store','destroy']
            ]
        );

        Route::post(
            'user', [
            'uses'=>'AuthController@store'
            ]
        );

        Route::post(
            'user/login', [
            'uses'=>'AuthController@signin'
            ]
        );
    }
);
 */
Route::group(
    [
    'middleware'=>'api',
    'prefix'=>'v1'
    ], function () {
        Route::resource(
            'property', 'PropertyController', [
            'except'=>['edit','create']
            ]
        );

        Route::resource(
            'property/assign', 'AssignController', [
            'only'=>['store','destroy']
            ]
        );

        Route::post(
            'property/user/unassign', [
            'uses'=>'AssignController@unassign'
            ]
        );

        Route::resource(
            'asset', 'AssetController', [
            'except'=>['edit','create', 'update', 'destroy']
            ]
        );

        Route::resource(
            'property/asset', 'AssignAssetController', [
            'except'=>['edit','create', 'update', 'destroy']
            ]
        );

        Route::post(
            'property/asset/unassign', [
            'uses'=>'AssignAssetController@unassign'
            ]
        );

        Route::post(
            'user', [
            'uses'=>'AuthController@store'
            ]
        );

        Route::resource(
            'user', 'AuthController', [
                'only'=>['index','show']
            ]
        );

        Route::post(
            'user/login', [
            'uses'=>'AuthController@signin'
            ]
        );
    }
);



