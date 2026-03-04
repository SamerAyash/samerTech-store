<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\user\AuthController;
use App\Http\Controllers\Api\user\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\MyFatoorahController;
use App\Http\Middleware\CurrencyMiddleware;
use App\Http\Middleware\OptionalSanctum;
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

Route::get('/navigation/categories', [CategoryController::class, 'getCategories']);
// Middleware to handle currency selection
// Home Page Content API Routes
Route::middleware([CurrencyMiddleware::class])->group(function () {
    Route::prefix('home')->group(function () {
        Route::get('slider/fall-winter', [HomeController::class, 'getFallWinterSlider']);
        Route::get('slider/spring-summer', [HomeController::class, 'getSpringSummerSlider']);
        Route::get('featured/a', [HomeController::class, 'getFeaturedA']);
        Route::get('featured/b', [HomeController::class, 'getFeaturedB']);
    });
    // Products API Routes
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'index']);
        Route::get('/filters', [ProductController::class, 'getFilters']);
        Route::get('/{refCode}', [ProductController::class, 'show']);
    });
    
    Route::prefix('posts')->group(function () {
        Route::get('/home', [PostController::class, 'home']);
        Route::get('/', [PostController::class, 'index']);
        Route::get('/{slug}', [PostController::class, 'show']);
    });
});

// User Authentication API Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
// Routes that require authentication
Route::get('orders/{order_number}/{token}', [OrderController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);    
    // User Profile API Routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::get('/orders', [UserController::class, 'getOrders']);
    });
});

// Cart and Checkout Routes (optional authentication - supports both authenticated users and guests)
Route::middleware([CurrencyMiddleware::class, OptionalSanctum::class])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update', [CartController::class, 'update']);
        Route::delete('/remove', [CartController::class, 'remove']);
        //Route::delete('/clear', [CartController::class, 'clear']);
    });
    // Checkout API Routes (optional authentication)
    Route::get('/checkout/information', [CheckoutController::class, 'checkout_information']);
    Route::post('/apply-discount', [CartController::class, 'applyDiscount']);
    Route::post('/checkout', [CheckoutController::class, 'createOrder']);
});

// MyFatoorah Payment Routes (public routes for callbacks)
Route::prefix('myfatoorah')->group(function () {
    Route::get('/callback', [MyFatoorahController::class, 'callback']);
    Route::post('/webhook', [MyFatoorahController::class, 'webhook']);
    Route::get('/verify/{orderId}', [MyFatoorahController::class, 'verifyPayment']);
});

