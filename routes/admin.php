<?php 

////////////////////////////// Admin Dashboard Routes //////////////////////////

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\SliderProductController;
use App\Http\Controllers\Admin\FeaturedProductController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Middleware\AdminLogin;
use App\Http\Middleware\IsLogin;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=> IsLogin::class,'prefix'=>'romano'],function (){
Route::get('login',[AdminAuthController::class,'login'])->name('login');
Route::post('login',[AdminAuthController::class,'dologin'])->name('dologin');
Route::get('forgot/password',[AdminAuthController::class,'forgetPassword'])->name('forgotPassword');
Route::post('forgot/password',[AdminAuthController::class,'resetPassword'])->name('resetPassword');
Route::get('reset/password/{token}',[AdminAuthController::class,'resetPasswordWithToken'])->name('resetPasswordToken');
Route::post('update/{token}',[AdminAuthController::class,'updatePassword'])->name('updatePassword');
});


///////////////////////////////////////////////////////////
Route::group(['middleware'=> AdminLogin::class,'prefix'=>'romano'],function (){

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    //Route::resource('/contact', ContactController::class)->except('store')->names('admin.contact');
    Route::post('/logout',[AdminAuthController::class,'logout'])->name('logout');
    Route::get('/settings',[AdminAuthController::class,'setting'])->name('setting');
    Route::post('/settings/email',[AdminAuthController::class,'setting_email'])->name('setting_email');
    Route::post('/settings/password',[AdminAuthController::class,'setting_password'])->name('setting_password');
    
    // Site Settings Routes
    Route::get('/content-settings', [SiteSettingController::class, 'index'])->name('content-settings.index');
    Route::put('/content-settings', [SiteSettingController::class, 'update'])->name('content-settings.update');

    Route::get('categories/sort', [CategoryController::class, 'sort'])->name('categories.sort');
    Route::get('categories/sort-children', [CategoryController::class, 'sortChildren'])->name('categories.sortChildren');
    Route::get('categories/sort-children/{parentId}', [CategoryController::class, 'sortChildrenOf'])->name('categories.sortChildrenOf');
    Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.updateOrder');
    Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::resource('categories', CategoryController::class)->names('categories');
    // Users Management Routes
    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verifyEmail');
    Route::post('users/{user}/verify-phone', [UserController::class, 'verifyPhone'])->name('users.verifyPhone');
    Route::resource('users', UserController::class)->except('store', 'create', 'edit', 'update')->names('users');

    // Contact Messages Routes
    Route::get('contact/data', [ContactController::class, 'data'])->name('contact.data');
    Route::put('contact/{contact}/status', [ContactController::class, 'updateStatus'])->name('contact.updateStatus');
    Route::resource('contact', ContactController::class)->except('store', 'create', 'edit', 'update')->names('contact');

    // Products Management Routes
    Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{ref_code}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{ref_code}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{ref_code}', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/{ref_code}/upload-image', [ProductController::class, 'uploadImage'])->name('products.uploadImage');
    Route::put('products/{ref_code}/images/{image}/attributes', [ProductController::class, 'updateImageAttributes'])->name('products.updateImageAttributes');
    Route::get('products/{ref_code}/attributes', [ProductController::class, 'getProductAttributes'])->name('products.getAttributes');
    Route::delete('products/{ref_code}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::post('products/{ref_code}/images/{image}/set-main', [ProductController::class, 'setMainImage'])->name('products.setMainImage');
    Route::post('products/{ref_code}/images/{image}/set-secondary', [ProductController::class, 'setSecondaryImage'])->name('products.setSecondaryImage');

    // Product Types and Dynamic Attributes
    Route::get('product-types', [ProductTypeController::class, 'index'])->name('product-types.index');
    Route::post('product-types', [ProductTypeController::class, 'store'])->name('product-types.store');
    Route::put('product-types/{productType}', [ProductTypeController::class, 'update'])->name('product-types.update');
    Route::delete('product-types/{productType}', [ProductTypeController::class, 'destroy'])->name('product-types.destroy');

    // Orders Management Routes
    Route::get('orders/data', [OrderController::class, 'data'])->name('orders.data');
    Route::get('orders/search-products', [OrderController::class, 'searchProducts'])->name('orders.searchProducts');
    Route::get('orders/product-variants/{ref_code}', [OrderController::class, 'getProductVariants'])->name('orders.productVariants');
    Route::get('orders/shipping-methods', [OrderController::class, 'shippingMethods'])->name('orders.shippingMethods');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
    Route::resource('orders', OrderController::class)->except('edit', 'update', 'destroy')->names('orders');

    // Discount Codes Management Routes
    Route::get('discount-codes/data', [DiscountCodeController::class, 'data'])->name('discount-codes.data');
    Route::put('discount-codes/{discountCode}/toggle-status', [DiscountCodeController::class, 'toggleStatus'])->name('discount-codes.toggleStatus');
    Route::resource('discount-codes', DiscountCodeController::class)->names('discount-codes');

    // Home Sliders Management Routes
    Route::get('slider-products/data', [SliderProductController::class, 'data'])->name('slider-products.data');
    Route::get('slider-products/search', [SliderProductController::class, 'searchProducts'])->name('slider-products.search');
    Route::get('slider-products', [SliderProductController::class, 'index'])->name('slider-products.index');
    Route::post('slider-products', [SliderProductController::class, 'store'])->name('slider-products.store');
    Route::delete('slider-products/{homeSlider}', [SliderProductController::class, 'destroy'])->name('slider-products.destroy');

    // Home Featured Management Routes
    Route::get('featured-products/data', [FeaturedProductController::class, 'data'])->name('featured-products.data');
    Route::get('featured-products/search', [FeaturedProductController::class, 'searchProducts'])->name('featured-products.search');
    Route::get('featured-products', [FeaturedProductController::class, 'index'])->name('featured-products.index');
    Route::post('featured-products', [FeaturedProductController::class, 'store'])->name('featured-products.store');
    Route::delete('featured-products/{featuredProduct}', [FeaturedProductController::class, 'destroy'])->name('featured-products.destroy');

    // Posts/Blog Management Routes
    Route::get('posts/data', [PostController::class, 'data'])->name('posts.data');
    Route::get('posts/search', [PostController::class, 'searchProducts'])->name('posts.search');
    Route::resource('posts', PostController::class)->names('posts');

    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('latest', [NotificationController::class, 'latest'])->name('latest');
        Route::post('{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    });
});