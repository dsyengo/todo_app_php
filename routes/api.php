<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

// --- Auth (guest) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Password reset (guest) ---
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => __($status)], 200)
        : response()->json(['message' => __($status)], 422);
});

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
            $user->tokens()->delete();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => __($status)], 200)
        : response()->json(['message' => __($status)], 422);
});

// --- Protected ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('tasks', TaskController::class)->names('api.tasks');
    Route::apiResource('categories', CategoryController::class)->names('api.categories');
});
