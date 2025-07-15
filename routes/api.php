<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AuthController,
    UserController,
    RoleController,
    TypeController,
    RegionController,
    CityController,
    ProductController,
    OrderController,
    ReviewController,
    ConversationController,
    MessageController,
    FavoriteController,
    PaymentController,
    SubscriptionController,
    AnalyticsController
};

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

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('auth:sanctum');

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('/user', [UserController::class, 'showAuthenticated']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::get('/user/settings', [UserController::class, 'getSettings']);
    Route::put('/user/settings', [UserController::class, 'updateSettings']);
    Route::get('/user/subscriptions', [UserController::class, 'getSubscriptions']);
    Route::post('/user/verify-email', [UserController::class, 'verifyEmail']);
    Route::post('/user/verify-phone', [UserController::class, 'verifyPhone']);
});

// Roles and Types
Route::apiResource('roles', RoleController::class);
Route::apiResource('types', TypeController::class);

// Location routes
Route::apiResource('regions', RegionController::class);
Route::apiResource('cities', CityController::class);
Route::get('/cities/{region}', [CityController::class, 'findByRegion']);

// Product routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('/products/{product}/images', [ProductController::class, 'uploadImage']);
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage']);
    Route::post('/products/{product}/variants', [ProductController::class, 'createVariant']);
    Route::put('/products/{product}/variants/{variant}', [ProductController::class, 'updateVariant']);
    Route::delete('/products/{product}/variants/{variant}', [ProductController::class, 'deleteVariant']);
    Route::get('/products/{product}/reviews', [ProductController::class, 'getReviews']);
});

// Order routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/{order}/items', [OrderController::class, 'getItems']);
    Route::post('/orders/{order}/payments', [OrderController::class, 'createPayment']);
});

// Review routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reviews', ReviewController::class);
    Route::post('/reviews/{review}/verify', [ReviewController::class, 'verifyPurchase']);
});

// Chat routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('conversations', ConversationController::class);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'getMessages']);
    Route::apiResource('messages', MessageController::class);
});

// Favorite routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('favorites', FavoriteController::class);
    Route::get('/favorites/check/{product}', [FavoriteController::class, 'checkFavorite']);
});

// Payment routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payment-methods', PaymentController::class);
    Route::post('/payment-api-keys', [PaymentController::class, 'createApiKey']);
    Route::put('/payment-api-keys/{key}', [PaymentController::class, 'updateApiKey']);
});

// Subscription routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/checkout', [SubscriptionController::class, 'checkout']);
});

// Analytics routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/analytics/traffic', [AnalyticsController::class, 'getTraffic']);
    Route::get('/analytics/sales', [AnalyticsController::class, 'getSales']);
    Route::get('/analytics/products', [AnalyticsController::class, 'getProductStats']);
});
