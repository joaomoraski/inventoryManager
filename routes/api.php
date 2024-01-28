<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;x
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);


Route::group([
    "middleware" => ["auth:api"]
], function () {
    // Auth
    Route::get("profile", [AuthController::class, "profile"]);
    Route::get("refresh", [AuthController::class, "refreshToken"]);
    Route::get("logout", [AuthController::class, "logout"]);
    Route::get("validate", [AuthController::class, "validateToken"]);

    // User
    Route::post("/user/create", [UserController::class, "create"]);
    Route::get("/user/listUsers", [UserController::class, "listUsers"]); // Listar
    Route::get("/user/{id}", [UserController::class, "findById"]); // Localizar
    Route::put("/user/{id}", [UserController::class, "update"]); // Update
    Route::delete("/user/{id}", [UserController::class, "delete"]); // Delete
    // TODO: criar as rotas de alterar a senha.
});

