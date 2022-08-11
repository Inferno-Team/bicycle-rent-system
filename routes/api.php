<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Esp32Controller;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [UserController::class, 'login']);
Route::post('/signup', [UserController::class, 'signUp']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['auth:sanctum']], function ($route) {
    // manager apis [ post ]
    $route->post('/create_style', [ManagerController::class, 'createStyle']);
    $route->post('/create_stand', [ManagerController::class, 'createStand']);
    $route->post('/create_bicycle', [ManagerController::class, 'createBicycle']);
    $route->post('/ban_user', [ManagerController::class, 'banUser']);
    // manager  apis [ get ]
    $route->get('/get_current_user/{id}', [ManagerController::class, 'getCurrentUser']);
    $route->get('/get_bicycle_current_location/{id}', [ManagerController::class, 'getBicycleCurrentLocation']);
    // manager + customer apis [ get ]
    $route->get('/get_all_bicycle', [ManagerController::class, 'getAllBicycle']);
    $route->get('/get_all_stands', [ManagerController::class, 'getAllStands']);
    $route->get('/get_stand_bicycles/{id}', [ManagerController::class, 'getStandBicycle']);
    $route->get(
        '/get_avaliable_bicycle_in_stand/{id}',
        [ManagerController::class, 'getAvaliableBicycleInStand']
    );
    $route->get('/recent_events', [ManagerController::class, 'recentEvents']);
    $route->get('/get_user_history', [ManagerController::class, 'getUserHistory']);
    $route->get('/get_all_styles', [ManagerController::class, 'getStyles']);
    $route->post('/logout', [UserController::class, 'logout']);
    $route->post('/delete_bick', [ManagerController::class, 'deleteBick']);
    $route->get('/get_all_banned_users', [ManagerController::class, 'getAllBannedUsers']);
});

Route::group(['middleware' => ['auth:sanctum']], function ($route) {
    // customer apis [ post ]
    $route->post('/rent_bicycle', [CustomerController::class, 'rentBicycle']);
    $route->post('/update_current_location', [CustomerController::class, 'updateLocation']);
    $route->post('/return_bicycle', [CustomerController::class, 'returnBicycle']);
    $route->post('/check_user_if_renting', [CustomerController::class, 'checkIfRenting']);
    $route->get('/get_my_history', [CustomerController::class, 'getMyHistory']);
    $route->get('/get_bicycle_by_ip/{ip}', [CustomerController::class, 'getBicycleByIP']);
    $route->post('/get_user', [CustomerController::class, 'getUser']);
    $route->post('/edit_user', [CustomerController::class, 'editUser']);
    $route->post('reset_password', [CustomerController::class, 'resetPassword']);
});

Route::group(['middleware' => ['auth:sanctum']], function ($route) {
    // esp32 nodeMCU apis [ post ]
    $route->post('/update_current_user_location', [Esp32Controller::class, 'updateLocation']);
});
