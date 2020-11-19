<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//sia
$router->group(['prefix' => 'sia'], function () use ($router) {
    $router->post('login', ['uses' => 'userController@loginSia']);
    // 'middleware' => ['auth','role:sia']
    $router->group(['namespace' => 'SIA', 'middleware' => ['auth','role:1'], 'as' => 'sia'], function () use ($router) {
        $router->get('/', ['uses' => 'myController@index']);
        $router->group(['prefix' => 'unit', 'as' => 'unit'], function () use ($router){
            $router->get('/', ['as'=>'getList', 'uses' => 'unitController@getList']);
            $router->post('/syn', ['as' => 'syn','uses' => 'unitController@syn']);
        });
        $router->group(['prefix' => 'period', 'as' => 'period'], function () use ($router){
            $router->get('/', ['as'=>'getList', 'uses' => 'periodController@getList']);
            $router->post('/{id}/add-topic', ['as' => 'syn','uses' => 'periodController@addTopic']);
            $router->delete('/{id}/delete-topic', ['as' => 'syn','uses' => 'periodController@deleteTopic']);
            $router->post('/syn', ['as' => 'syn','uses' => 'periodController@syn']);
        });

        $router->group(['prefix' => 'news', 'as' => 'news'], function () use ($router) {
            $router->get('/', ['as'=>'getList', 'uses' => 'newsController@getList']);
            $router->get('/{id}', ['as'=>'getData', 'uses' => 'newsController@getData']);
            $router->get('/{id}/edit', ['as'=>'edit', 'uses' => 'newsController@edit']);
            $router->post('/save', ['as'=>'save', 'uses' => 'newsController@store']);
            $router->patch('/{id}/update', ['as'=>'update', 'uses' => 'newsController@update']);
            $router->delete('/{id}/delete', ['as'=>'delete', 'uses' => 'newsController@destroy']);
        });

        $router->group(['prefix' => 'topic', 'as' => 'topic'], function () use ($router) {
            $router->get('/', ['as'=>'getList', 'uses' => 'topicController@getList']);
            $router->get('/actif', ['as'=>'getListActif', 'uses' => 'topicController@getListActif']);
            $router->get('/{id}', ['as'=>'getData', 'uses' => 'topicController@getData']);
            $router->get('/{id}/edit', ['as'=>'edit', 'uses' => 'topicController@edit']);
            $router->post('/save', ['as'=>'save', 'uses' => 'topicController@store']);
            $router->patch('/{id}/update', ['as'=>'update', 'uses' => 'topicController@update']);
            $router->delete('/{id}/delete', ['as'=>'delete', 'uses' => 'topicController@destroy']);
        });
    });
});

$router->group(['prefix' => 'dosen'], function () use ($router) {
    $router->post('login', ['uses' => 'userController@loginDosen']);
    // 'middleware' => ['auth','role:dosen']
    $router->group(['namespace' => 'dosen', 'middleware' => ['auth','role:2']], function () use ($router) {
        $router->get('/', function () use ($router) {
            return "berhasil login";
        });
    });
});

$router->group(['prefix' => 'mahasiswa'], function () use ($router) {
    $router->post('login', ['uses' => 'userController@loginMahasiswa']);
    // 'middleware' => ['auth','role:mahasiswa']
    $router->group(['namespace' => 'mahasiswa', 'middleware' => ['auth','role:3']], function () use ($router) {
        $router->get('/', function () use ($router) {
            return "berhasil login";
        });
    });
});

$router->group(['prefix' => 'user'], function () use ($router) {

    $router->post('isLogin', ['uses' => 'userController@isLogin']);
    
    $router->group(['middleware' => ['auth']], function () use ($router) {
        $router->get('profile', ['uses' => 'userController@profile']);
        $router->patch('change-profile', ['uses' => 'userController@changeProfile']);
        $router->post('change-avatar', ['uses' => 'userController@changeAvatar']);
        $router->patch('change-password', ['uses' => 'userController@changePassword']);
        $router->post('logout', ['uses' => 'userController@logout']);
    });


});


