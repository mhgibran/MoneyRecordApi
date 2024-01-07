<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'ensure_token'], function() use ($router) {
    $router->group(['prefix' => 'card'], function() use ($router) {
        $router->get('/', 'CardController@index');
        $router->post('/', 'CardController@store');
        $router->get('/{id}', 'CardController@show');
        $router->put('/{id}', 'CardController@update');
        $router->delete('/{id}', 'CardController@destroy');
    });
    
    $router->group(['prefix' => 'transaction-category'], function() use ($router) {
        $router->get('/', 'TransactionCategoryController@index');
        $router->post('/', 'TransactionCategoryController@store');
        $router->get('/{id}', 'TransactionCategoryController@show');
        $router->put('/{id}', 'TransactionCategoryController@update');
        $router->delete('/{id}', 'TransactionCategoryController@destroy');
    });
    
    $router->group(['prefix' => 'transaction'], function() use ($router) {
        $router->get('/', 'TransactionController@index');
        $router->post('/', 'TransactionController@store');
        $router->get('/{id}', 'TransactionController@show');
        $router->get('/type/{id}', 'TransactionController@index');
    });
});
