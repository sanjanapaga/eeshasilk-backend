<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Auth
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');

    // Products
    $routes->get('products', 'ProductController::index');
    $routes->get('products/(:num)', 'ProductController::show/$1');
    $routes->post('products', 'ProductController::create');
    $routes->put('products/(:num)', 'ProductController::update/$1');
    $routes->delete('products/(:num)', 'ProductController::delete/$1');

    // Orders
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:num)', 'OrderController::show/$1');
    $routes->post('orders', 'OrderController::create');
    $routes->put('orders/(:num)', 'OrderController::update/$1');
    $routes->delete('orders/(:num)', 'OrderController::delete/$1');

    // Wishlist
    $routes->get('wishlist', 'WishlistController::index');
    $routes->post('wishlist', 'WishlistController::create');
    $routes->delete('wishlist/(:num)', 'WishlistController::delete/$1');

    // Cart
    $routes->get('cart', 'CartController::index');
    $routes->post('cart', 'CartController::create');
    $routes->put('cart/(:num)', 'CartController::update/$1');
    $routes->delete('cart/(:num)', 'CartController::delete/$1');
    $routes->delete('cart/clear', 'CartController::clear');

    // Offers
    $routes->get('offers', 'OfferController::index');
    $routes->post('offers', 'OfferController::create');
    $routes->delete('offers/(:num)', 'OfferController::delete/$1');

    // Reviews
    $routes->get('reviews', 'ReviewController::index');
    $routes->post('reviews', 'ReviewController::create', ['filter' => 'jwt']);
    $routes->delete('reviews/(:num)', 'ReviewController::delete/$1', ['filter' => 'jwt']);

    // Categories
    $routes->get('categories', 'CategoryController::index');
    $routes->post('categories', 'CategoryController::create');
    $routes->delete('categories/(:num)', 'CategoryController::delete/$1');

    // Messages
    $routes->get('messages', 'MessageController::index');
    $routes->post('messages', 'MessageController::create');
    // Razorpay
    $routes->post('razorpay/order', 'RazorpayController::createOrder');
    $routes->post('razorpay/verify', 'RazorpayController::verifyPayment');
    // Test Email
    $routes->get('test-email', 'TestEmailController::sendTest');
    $routes->get('preview-email', 'TestEmailController::preview');

});
