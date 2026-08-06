<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'ComingSoon::index');
$routes->get('home', 'Home::index');
$routes->get('shop', 'Shop::index');
$routes->get('shop/load-more', 'Shop::loadMore');
$routes->get('search', 'Search::index');
$routes->get('search/suggest', 'Search::suggest');
$routes->get('product/(:segment)/quick', 'ProductController::quick/$1');
$routes->get('product/(:segment)', 'ProductController::show/$1');
$routes->get('cart', 'CartController::index');
$routes->get('checkout', 'CartController::checkout');
$routes->get('order/success/(:segment)', 'CartController::success/$1');
$routes->post('cart/add', 'CartController::add', ['filter' => 'csrf']);
$routes->post('cart/update', 'CartController::update', ['filter' => 'csrf']);
$routes->post('cart/set-plan', 'CartController::setPlan', ['filter' => 'csrf']);
$routes->post('cart/set-payment', 'CartController::setPayment', ['filter' => 'csrf']);
$routes->post('cart/remove', 'CartController::remove', ['filter' => 'csrf']);
$routes->post('checkout/place-order', 'CartController::placeOrder', ['filter' => 'csrf']);

$routes->group('account', static function ($routes) {
    $routes->group('', ['filter' => 'gueststore'], static function ($routes) {
        $routes->get('login', 'AccountController::login');
        $routes->post('login', 'AccountController::attemptLogin', ['filter' => 'csrf']);
        $routes->get('register', 'AccountController::register');
        $routes->post('register/send-otp', 'AccountController::sendRegisterOtp', ['filter' => 'csrf']);
        $routes->post('register/verify-otp', 'AccountController::verifyRegisterOtp', ['filter' => 'csrf']);
        $routes->get('forgot-password', 'AccountController::forgotPassword');
        $routes->post('forgot-password/send-otp', 'AccountController::sendForgotOtp', ['filter' => 'csrf']);
        $routes->post('forgot-password/reset', 'AccountController::resetPasswordWithOtp', ['filter' => 'csrf']);
    });

    $routes->post('logout', 'AccountController::logout', ['filter' => 'csrf']);

    $routes->group('', ['filter' => 'storeauth'], static function ($routes) {
        $routes->get('profile', 'AccountController::profile');
        $routes->post('profile', 'AccountController::updateProfile', ['filter' => 'csrf']);
        $routes->get('orders', 'AccountController::orders');
    });
});

$routes->get('about', 'Pages::about');
$routes->get('contact', 'Pages::contact');
$routes->get('track-order', 'TrackOrderController::index');
$routes->post('track-order/lookup', 'TrackOrderController::lookup', ['filter' => 'csrf']);
$routes->get('faq', 'Pages::faq');
$routes->get('privacy', 'Pages::privacy');
$routes->get('terms', 'Pages::terms');
$routes->get('installment-terms', 'Pages::installmentTerms');

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->group('', ['filter' => 'guestadmin'], static function ($routes) {
        $routes->get('login', 'AuthController::login');
        $routes->post('login', 'AuthController::attemptLogin', ['filter' => 'csrf']);
    });

    $routes->post('logout', 'AuthController::logout', ['filter' => 'csrf']);

    $routes->group('', ['filter' => 'adminauth'], static function ($routes) {
        $routes->get('/', 'DashboardController::index');
        $routes->get('dashboard', 'DashboardController::index');
        $routes->get('api/dashboard/stats', 'DashboardController::stats', ['filter' => 'permission:dashboard.view']);

        // Categories
        $routes->get('categories', 'CategoriesController::index', ['filter' => 'permission:categories.view']);
        $routes->get('api/categories', 'CategoriesController::list', ['filter' => 'permission:categories.view']);
        $routes->get('api/categories/(:num)', 'CategoriesController::show/$1', ['filter' => 'permission:categories.view']);
        $routes->post('api/categories', 'CategoriesController::store', ['filter' => ['csrf', 'permission:categories.create']]);
        $routes->post('api/categories/(:num)', 'CategoriesController::update/$1', ['filter' => ['csrf', 'permission:categories.update']]);
        $routes->post('api/categories/(:num)/delete', 'CategoriesController::delete/$1', ['filter' => ['csrf', 'permission:categories.delete']]);

        // Products
        $routes->get('products', 'ProductsController::index', ['filter' => 'permission:products.view']);
        $routes->get('api/products', 'ProductsController::list', ['filter' => 'permission:products.view']);
        $routes->get('api/products/(:num)', 'ProductsController::show/$1', ['filter' => 'permission:products.view']);
        $routes->post('api/products', 'ProductsController::store', ['filter' => ['csrf', 'permission:products.create']]);
        $routes->post('api/products/(:num)', 'ProductsController::update/$1', ['filter' => ['csrf', 'permission:products.update']]);
        $routes->post('api/products/(:num)/delete', 'ProductsController::delete/$1', ['filter' => ['csrf', 'permission:products.delete']]);

        // Customers
        $routes->get('customers', 'CustomersController::index', ['filter' => 'permission:customers.view']);
        $routes->get('api/customers', 'CustomersController::list', ['filter' => 'permission:customers.view']);
        $routes->get('api/customers/(:num)', 'CustomersController::show/$1', ['filter' => 'permission:customers.view']);
        $routes->post('api/customers', 'CustomersController::store', ['filter' => ['csrf', 'permission:customers.create']]);
        $routes->post('api/customers/(:num)', 'CustomersController::update/$1', ['filter' => ['csrf', 'permission:customers.update']]);
        $routes->post('api/customers/(:num)/delete', 'CustomersController::delete/$1', ['filter' => ['csrf', 'permission:customers.delete']]);

        // Orders
        $routes->get('orders', 'OrdersController::index', ['filter' => 'permission:orders.view']);
        $routes->get('api/orders', 'OrdersController::list', ['filter' => 'permission:orders.view']);
        $routes->get('api/orders/(:num)', 'OrdersController::show/$1', ['filter' => 'permission:orders.view']);
        $routes->post('api/orders', 'OrdersController::store', ['filter' => ['csrf', 'permission:orders.create']]);
        $routes->post('api/orders/(:num)', 'OrdersController::update/$1', ['filter' => ['csrf', 'permission:orders.update']]);
        $routes->post('api/orders/(:num)/status', 'OrdersController::updateStatus/$1', ['filter' => ['csrf', 'permission:orders.update']]);
        $routes->post('api/orders/(:num)/delete', 'OrdersController::delete/$1', ['filter' => ['csrf', 'permission:orders.delete']]);

        // Contents
        $routes->get('contents', 'ContentsController::index', ['filter' => 'permission:contents.view']);
        $routes->get('api/contents', 'ContentsController::list', ['filter' => 'permission:contents.view']);
        $routes->get('api/contents/(:num)', 'ContentsController::show/$1', ['filter' => 'permission:contents.view']);
        $routes->post('api/contents', 'ContentsController::store', ['filter' => ['csrf', 'permission:contents.create']]);
        $routes->post('api/contents/(:num)', 'ContentsController::update/$1', ['filter' => ['csrf', 'permission:contents.update']]);
        $routes->post('api/contents/(:num)/delete', 'ContentsController::delete/$1', ['filter' => ['csrf', 'permission:contents.delete']]);

        // Banners
        $routes->get('banners', 'BannersController::index', ['filter' => 'permission:banners.view']);
        $routes->get('api/banners', 'BannersController::list', ['filter' => 'permission:banners.view']);
        $routes->get('api/banners/(:num)', 'BannersController::show/$1', ['filter' => 'permission:banners.view']);
        $routes->post('api/banners', 'BannersController::store', ['filter' => ['csrf', 'permission:banners.create']]);
        $routes->post('api/banners/(:num)', 'BannersController::update/$1', ['filter' => ['csrf', 'permission:banners.update']]);
        $routes->post('api/banners/(:num)/delete', 'BannersController::delete/$1', ['filter' => ['csrf', 'permission:banners.delete']]);

        // Settings
        $routes->get('settings', 'SettingsController::index', ['filter' => 'permission:settings.view']);
        $routes->get('api/settings', 'SettingsController::list', ['filter' => 'permission:settings.view']);
        $routes->post('api/settings', 'SettingsController::update', ['filter' => ['csrf', 'permission:settings.update']]);

        // Users
        $routes->get('users', 'UsersController::index', ['filter' => 'permission:users.view']);
        $routes->get('api/users', 'UsersController::list', ['filter' => 'permission:users.view']);
        $routes->get('api/users/(:num)', 'UsersController::show/$1', ['filter' => 'permission:users.view']);
        $routes->post('api/users', 'UsersController::store', ['filter' => ['csrf', 'permission:users.create']]);
        $routes->post('api/users/(:num)', 'UsersController::update/$1', ['filter' => ['csrf', 'permission:users.update']]);
        $routes->post('api/users/(:num)/delete', 'UsersController::delete/$1', ['filter' => ['csrf', 'permission:users.delete']]);

        // Roles
        $routes->get('roles', 'RolesController::index', ['filter' => 'permission:roles.view']);
        $routes->get('api/roles', 'RolesController::list', ['filter' => 'permission:roles.view']);
        $routes->get('api/roles/(:num)', 'RolesController::show/$1', ['filter' => 'permission:roles.view']);
        $routes->post('api/roles', 'RolesController::store', ['filter' => ['csrf', 'permission:roles.create']]);
        $routes->post('api/roles/(:num)', 'RolesController::update/$1', ['filter' => ['csrf', 'permission:roles.update']]);
        $routes->post('api/roles/(:num)/delete', 'RolesController::delete/$1', ['filter' => ['csrf', 'permission:roles.delete']]);
        $routes->get('api/permissions', 'RolesController::permissions', ['filter' => 'permission:roles.view']);
    });
});
