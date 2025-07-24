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
    AnalyticsController,
    ProductImageController,
    ProductVariantController,
    UserReportController,
    DeliveryOptionController,
    CartController,
    MessageAttachmentController,
    CategoryController,
    NotificationController
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
Route::get('/regions/{region}/cities', [RegionController::class, 'Cities']);
Route::apiResource('cities', CityController::class);
Route::get('/cities/{region}', [CityController::class, 'findByRegion']);

// category routes
Route::apiResource('categories', CategoryController::class);

// Product routes public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ProductController::class, 'getReviews']);

// Product routes private routes
Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('products', ProductController::class);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::get('/seller/{seller}/products', [ProductController::class, 'indexProductBySeller']);
    Route::delete('/seller/{seller}/products/{product}', [ProductController::class, 'deleteProductBySeller']);

    Route::post('/products/{product}/review', [ProductController::class, 'addReview']);
    Route::delete('/products/{product}/review', [ProductController::class, 'removeReview']);
    Route::apiResource('products.images', ProductImageController::class);
    Route::post('/products/{product}/images/{image}/main', [ProductImageController::class, 'setMainImage']);
    Route::apiResource('products.variants', ProductVariantController::class);
    Route::put('/products/{product}/variants/{variant}/stock', [ProductVariantController::class, 'updateStock']);
    Route::post('/products/{product}/variants', [ProductController::class, 'createVariant']);
    Route::put('/products/{product}/variants/{variant}', [ProductController::class, 'updateVariant']);
    Route::delete('/products/{product}/variants/{variant}', [ProductController::class, 'deleteVariant']);
    Route::get('/products/{product}/reviews', [ProductController::class, 'getReviews']);
    Route::get('/seller/{seller}/products', [ProductController::class, 'indexProductBySeller']);
   
    // User Favorite Routes
    Route::get('/user/favorites', [FavoriteController::class, 'index']);
    Route::post('/user/favorites', [FavoriteController::class, 'store']);
    Route::delete('/user/favorites/{product}', [FavoriteController::class, 'destroy']);
    Route::get('/user/favorites/check/{product}', [FavoriteController::class, 'checkFavorite']);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'countUnread']);
    Route::get('/notifications/types', [NotificationController::class, 'types']);
    Route::get('/notifications/filter', [NotificationController::class, 'filter']);

    // Cart Routes
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{item}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{item}', [CartController::class, 'removeItem']);
    Route::delete('/cart', [CartController::class, 'clear']);
    Route::get('/cart/total', [CartController::class, 'calculateTotal']);
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon']);

    // Message Routes
    Route::apiResource('conversations.messages', MessageController::class);
    Route::post('/conversations/{conversation}/messages/{message}/attachments', [MessageAttachmentController::class, 'store']);
    Route::delete('/conversations/{conversation}/messages/{message}/attachments/{attachment}', [MessageAttachmentController::class, 'destroy']);
    Route::get('/conversations/{conversation}/messages/{message}/attachments/{attachment}/download', [MessageAttachmentController::class, 'download']);

    // User Report Routes
    Route::get('/users/{user}/reports', [UserReportController::class, 'index']);
    Route::post('/users/{user}/reports', [UserReportController::class, 'store']);
    Route::put('/user-reports/{report}', [UserReportController::class, 'update']);
    Route::delete('/user-reports/{report}', [UserReportController::class, 'destroy']);
    Route::get('/user/reports', [UserReportController::class, 'userReports']);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    // Delivery Routes
    Route::apiResource('delivery-options', DeliveryOptionController::class);
    Route::post('/delivery-options/calculate', [DeliveryOptionController::class, 'calculateShipping']);

    // Cart Routes
    Route::apiResource('carts', CartController::class);
    Route::post('/carts/{cart}/items', [CartController::class, 'addItem']);
    Route::put('/carts/{cart}/items/{item}', [CartController::class, 'updateItem']);
    Route::delete('/carts/{cart}/items/{item}', [CartController::class, 'removeItem']);

    // Order Routes
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/{order}/items', [OrderController::class, 'addItem']);
    Route::put('/orders/{order}/items/{item}', [OrderController::class, 'updateItem']);
    Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'removeItem']);

    // Review Routes
    Route::apiResource('reviews', ReviewController::class);
    Route::post('/reviews/{review}/images', [ReviewController::class, 'addImage']);
    Route::delete('/reviews/{review}/images/{image}', [ReviewController::class, 'removeImage']);
});

// Order routes
Route::middleware('auth:sanctum')->group(function () {
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
