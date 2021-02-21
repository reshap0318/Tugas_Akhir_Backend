<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    
    // $messaging = app('firebase.messaging');
    // $appInstance = $messaging->getAppInstance('c60QhTs6lNY:APA91bHTJV7ruSuGsNpzK822pOLMttCv7IFBphXzhd2ymxW5lzR7XzKoRK74dydZsqWrPQLnliyHbfVhZbgnBUFAeLTVDTsiiYdn3yf6ykI2wKnsPxs31H03ZX722JgWblYQrlbR50Oe');
    // $subscriptions = $appInstance->topicSubscriptions();
    // foreach ($subscriptions as $subscription) {
    //     echo "{$subscription->registrationToken()} is subscribed to {$subscription->topic()}\n";
    // }
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
            $router->delete('/{id}/delete-topic/{topicId}', ['as' => 'syn','uses' => 'periodController@deleteTopic']);
            $router->post('/syn', ['as' => 'syn','uses' => 'periodController@syn']);
        });

        $router->group(['prefix' => 'news', 'as' => 'news'], function () use ($router) {
            $router->get('/{id}/edit', ['as'=>'edit', 'uses' => 'newsController@edit']);
            $router->post('/save', ['as'=>'save', 'uses' => 'newsController@store']);
            $router->patch('/{id}/update', ['as'=>'update', 'uses' => 'newsController@update']);
            $router->delete('/{id}/delete', ['as'=>'delete', 'uses' => 'newsController@destroy']);
        });

        $router->group(['prefix' => 'topic', 'as' => 'topic'], function () use ($router) {
            $router->get('/', ['as'=>'getList', 'uses' => 'topicController@getList']);
            $router->get('/active', ['as'=>'getListActive', 'uses' => 'topicController@getListActive']);
            $router->get('/deactive', ['as'=>'getListActive', 'uses' => 'topicController@getListDeactive']);
            $router->post('/save', ['as'=>'save', 'uses' => 'topicController@store']);
            $router->patch('/{id}/update', ['as'=>'update', 'uses' => 'topicController@update']);
            $router->delete('/{id}/delete', ['as'=>'delete', 'uses' => 'topicController@destroy']);
        });
    });
});

$router->group(['prefix' => 'dosen'], function () use ($router) {
    $router->post('login', ['uses' => 'userController@loginDosen']);
    // 'middleware' => ['auth','role:dosen']
    $router->group(['middleware' => ['auth','role:2'], 'as' => 'dosen'], function () use ($router) {
        $router->group(['namespace' => 'Dosen'], function () use ($router){
            $router->get('/bimbingan', ['as'=>'getList', 'uses' => 'bimbinganController@getListData']);
            $router->get('/bimbingan/last-seen', ['as'=>'getAllListBimbingan', 'uses' => 'bimbinganController@lastSeen']);
            $router->get('/bimbingan/group-chat', ['as'=>'groupChat', 'uses' => 'bimbinganController@getGroupChat']);

            $router->post('/bimbingan/cetak/{receiverId}/{topicPeriodId}', ['as'=>'cetak', 'uses' => 'bimbinganController@cetakChatBimbingan']);
            $router->post('/bimbingan/cetak-period/{receiverId}/{periodId}', ['as'=>'cetak-period', 'uses' => 'bimbinganController@cetakChatBimbinganPeriod']);

            $router->post('/bimbingan/send', ['as'=>'send', 'uses' => 'bimbinganController@send']);
            $router->delete('/bimbingan/{chatId}/delete', ['as'=>'delete', 'uses' => 'bimbinganController@delete']);
            $router->post('/bimbingan/send-group-chat', ['as'=>'sendGroupChat', 'uses' => 'bimbinganController@sendGroupChat']);

            $router->get('/list-bimbingan/{mhsId}', ['as' => 'listBimbingan', 'uses' => 'bimbinganController@getListBimbingan']);
            $router->post('/list-bimbingan/{mhsId}/create', ['as' => 'listBimbingan', 'uses' => 'bimbinganController@createBimbingan']);
        });
        $router->group(['prefix'=>'mahasiswa/{nim}','namespace' => 'Mahasiswa'], function () use ($router){
            $router->get('', 'myController@getData');
            $router->get('list-semester', 'semesterController@getListData');
            $router->group(['prefix' => 'krs'], function () use ($router) {
                $router->get('', 'krsController@getListData');
                $router->get('/isCanChange', 'KrsController@isCanChange');
                $router->get('/{semester}', 'krsController@getListDataSemester');
                $router->post('/chage-status/{status}','KrsController@changeStatus'); //x
            });
            $router->get('sks/sum', 'sksController@getSumery');
            $router->group(['prefix' => 'transkrip'], function () use ($router) {
                $router->get('', 'transkripController@getListTranskrip');
                $router->get('staticA', 'transkripController@staticA');
                $router->get('staticB', 'transkripController@staticB');
            });
            $router->get('kelas/{klsId}', 'kelasController@getDetailKelas');
        });
    });
});

$router->group(['prefix' => 'mahasiswa'], function () use ($router) {
    $router->post('login', ['uses' => 'userController@loginMahasiswa']);
    // 'middleware' => ['auth','role:mahasiswa']
    $router->group(['namespace' => 'Mahasiswa', 'middleware' => ['auth','role:3']], function () use ($router) {
        $router->get('', 'myController@getData');
        $router->get('list-semester', 'semesterController@getListData');
        $router->group(['prefix' => 'krs'], function () use ($router) {
            $router->get('', 'krsController@getListData');
            $router->post('/entry', 'KrsController@entry');
            $router->delete('/delete/{krsdtId}', 'KrsController@deleteKrs');
            $router->get('/isCanEntry', 'KrsController@isCanEntry');
            $router->get('/{semester}', 'krsController@getListDataSemester');
        });
        $router->get('sks/sum', 'sksController@getSumery');
        $router->group(['prefix' => 'transkrip'], function () use ($router) {
            $router->get('', 'transkripController@getListTranskrip');
            $router->get('staticA', 'transkripController@staticA');
            $router->get('staticB', 'transkripController@staticB');
        });
        $router->group(['prefix' => 'bimbingan', 'as' => 'bimbingan'], function () use ($router) {
            $router->get('/', ['as'=>'getList', 'uses' => 'bimbinganController@getListBimbingan']);
            $router->get('/group-chat', ['as'=>'groupChat', 'uses' => 'bimbinganController@getGroupChat']);
            $router->get('/detail/{receiverId}/{topicPeriodId}', ['as'=>'detail', 'uses' => 'bimbinganController@getDetailChat']);
            $router->post('/create', ['as'=>'createBimbingan', 'uses' => 'bimbinganController@createBimbingan']);
            $router->post('/send', ['as'=>'send', 'uses' => 'bimbinganController@send']);
            $router->post('/send-group-chat', ['as'=>'sendGroupChat', 'uses' => 'bimbinganController@sendGroupChat']);
            $router->delete('/{chatId}/delete', ['as'=>'delete', 'uses' => 'bimbinganController@delete']);
        });
        
        $router->get('kelas', 'kelasController@getListKelas');
        $router->get('kelas/{klsId}', 'kelasController@getDetailKelas');
    });
});

$router->group(['prefix' => 'user'], function () use ($router) {

    $router->post('isLogin', ['uses' => 'userController@isLogin']);
    
    $router->group(['middleware' => ['auth']], function () use ($router) {
        $router->get('profile', ['uses' => 'userController@profile']);
        $router->get('semester-active', 'semesterController@active');
        $router->get('period', ['uses' => 'SIA\periodController@getList']);
        $router->group(['prefix' => 'news', 'as' => 'news'], function () use ($router) {
            $router->get('/', ['as'=>'getList', 'uses' => 'SIA\newsController@getList']);
            $router->get('/{id}', ['as'=>'getData', 'uses' => 'SIA\newsController@getData']);
        });
        $router->group(['prefix' => 'topic', 'as' => 'topic'], function () use ($router) {
            $router->get('/active', ['as'=>'getListActive', 'uses' => 'SIA\topicController@getListActive']);
        });
        $router->patch('change-profile', ['uses' => 'userController@changeProfile']);
        $router->post('change-avatar', ['uses' => 'userController@changeAvatar']);
        $router->patch('change-password', ['uses' => 'userController@changePassword']);
        $router->post('logout', ['uses' => 'userController@logout']);
    });


});


