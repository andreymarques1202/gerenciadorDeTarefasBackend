<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get("/test", function () {
    return response()->json(["message" => "teste de api ok"]);
});

Route::post("/login", [UserController::class, "Login"]);
Route::post("/register", [UserController::class, "Register"]);
Route::middleware('auth:sanctum')->group(function() {

    Route::get("/user", function() {
        return response()->json(Auth::user());
    });

    Route::put('/user', [UserController::class, 'update']);

    Route::put('/user/password', [UserController::class, 'updatePassword']);

    
    Route::post("/logout", [UserController::class, "logout"]);

    Route::get("/user/tasks", [TaskController::class, "index"]);
    Route::get("/tasks/{id}", [TaskController::class, "show"]);
    Route::post("/tasks", [TaskController::class, "store"]);
    Route::put("/tasks/{id}", [TaskController::class, "update"]);
    Route::delete("/tasks/{id}", [TaskController::class, "destroy"]);
});


