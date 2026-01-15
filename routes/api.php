<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;

// Public API routes
Route::prefix('v1')->group(function () {
    // Books
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Statistics
    Route::get('/stats', function () {
        return response()->json([
            'total_categories' => \App\Models\Category::where('status', 'active')->count(),
            'total_books' => \App\Models\Book::where('status', 'active')->count(),
        ]);
    });
});

// Protected API routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
    Route::get('/dashboard/student', [DashboardController::class, 'student']);
    Route::get('/dashboard/librarian', [DashboardController::class, 'librarian']);
});
