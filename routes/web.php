<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\QuoteRequestController as AdminQuoteRequestController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\QuoteRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::middleware('set.locale')->group(function () {
    Route::get('/', HomeController::class)->name('store.home');
    Route::get('/products', [ProductController::class, 'index'])->name('store.products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('store.products.show');

    Route::get('/cart', [CartController::class, 'index'])->name('store.cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('store.cart.store');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('store.cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('store.cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('store.checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('store.checkout.store');
    Route::get('/checkout/thanks/{order}', [CheckoutController::class, 'thanks'])->name('store.checkout.thanks');
    Route::post('/checkout/thanks/{order}/receipt', [CheckoutController::class, 'uploadReceipt'])->name('store.checkout.receipt');

    Route::get('/quote', [QuoteRequestController::class, 'create'])->name('store.quote.create');
    Route::post('/quote', [QuoteRequestController::class, 'store'])->name('store.quote.store');
    Route::get('/quote/thanks/{quoteRequest}', [QuoteRequestController::class, 'thanks'])->name('store.quote.thanks');

    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('store.page');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/confirm-payment', [AdminOrderController::class, 'confirmPayment'])->name('orders.confirm-payment');

        Route::get('/quotes', [AdminQuoteRequestController::class, 'index'])->name('quotes.index');
        Route::get('/quotes/{quoteRequest}', [AdminQuoteRequestController::class, 'show'])->name('quotes.show');
        Route::put('/quotes/{quoteRequest}/status', [AdminQuoteRequestController::class, 'updateStatus'])->name('quotes.status');
        Route::post('/quotes/{quoteRequest}/quotes', [AdminQuoteRequestController::class, 'storeQuote'])->name('quotes.store-quote');
    });
});
