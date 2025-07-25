<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\RoleMiddleware;

use App\Http\Controllers\{
    DashboardController,
    HomeController,
    SubHeadOfAccController,
    COAController,
    SaleController,
    ProductionController,
    PurchaseInvoiceController,
    PurchaseReturnController,
    ProductController,
    UserController,
    RoleController,
    AttributeController,
    ProductCategoryController,
    ProductionReceivingController,
    PaymentVoucherController
};

Auth::routes();

Route::middleware(['auth', RoleMiddleware::class . ':admin|superadmin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});

Route::middleware(['auth'])->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products/details', [ProductController::class, 'details'])->name('products.receiving');
    Route::get('/product/{id}/variations', [ProductController::class, 'getVariations']);
    Route::get('/product/{id}/variations', [ProductionReceivingController::class, 'getVariations'])->name('production.receiving.getVariations');

    Route::prefix('production_receiving')->name('production.receiving.')->group(function () {
        Route::get('/', [ProductionReceivingController::class, 'index'])->name('index');
        Route::get('/create', [ProductionReceivingController::class, 'create'])->name('create');
        Route::post('/store', [ProductionReceivingController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductionReceivingController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ProductionReceivingController::class, 'update'])->name('update');
        Route::get('/{id}/print', [ProductionReceivingController::class, 'print'])->name('print');
    });
    
    Route::get('/api/item/{item}/invoices', [PurchaseInvoiceController::class, 'getInvoicesByItem']);
    Route::get('/invoice-item/{invoiceId}/item/{itemId}', [PurchaseInvoiceController::class, 'getItemDetails']);

    $modules = [
        'coa' => ['controller' => COAController::class, 'permission' => 'coa'],
        'shoa' => ['controller' => SubHeadOfAccController::class, 'permission' => 'shoa'],
        'products' => ['controller' => ProductController::class, 'permission' => 'products'],
        'purchase_invoices' => ['controller' => PurchaseInvoiceController::class, 'permission' => 'purchase_invoices'],
        'purchase_return' => ['controller' => PurchaseReturnController::class, 'permission' => 'purchase_return'],
        'production' => ['controller' => ProductionController::class, 'permission' => 'production'],
        'sale_vouchers' => ['controller' => SaleController::class, 'permission' => 'sale_vouchers'],
        'sale_return' => ['controller' => SaleReturnController::class, 'permission' => 'sale_return'],
        'payment_vouchers' => ['controller' => PaymentVoucherController::class, 'permission' => 'payment_vouchers'],
        'pos_system' => ['controller' => POSController::class, 'permission' => 'pos_system'],
    ];

    foreach ($modules as $uri => $config) {
        $controller = $config['controller'];
        $permission = $config['permission'];

        Route::get("$uri", [$controller, 'index'])->middleware("check.permission:$permission.index")->name("$uri.index");
        Route::get("$uri/create", [$controller, 'create'])->middleware("check.permission:$permission.create")->name("$uri.create");
        Route::get("$uri/{id}/print", [$controller, 'print'])->middleware("check.permission:$permission.print")->name("$uri.print");
        Route::post("$uri", [$controller, 'store'])->middleware("check.permission:$permission.store")->name("$uri.store");
        Route::post("$uri/{id}/approve", [$controller, 'approve'])->middleware("check.permission:$permission.approve")->name("$uri.approve");
        Route::get("$uri/{id}", [$controller, 'show'])->middleware("check.permission:$permission.show")->name("$uri.show");
        Route::get("$uri/{id}/edit", [$controller, 'edit'])->middleware("check.permission:$permission.edit")->name("$uri.edit");
        Route::put("$uri/{id}", [$controller, 'update'])->middleware("check.permission:$permission.update")->name("$uri.update");
        Route::delete("$uri/{id}", [$controller, 'destroy'])->middleware("check.permission:$permission.delete")->name("$uri.destroy");
    }

    Route::prefix('product-categories')->name('product-categories.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
        Route::put('/{productCategory}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::post('/', [AttributeController::class, 'store'])->name('store');
        Route::put('/{attribute}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AttributeController::class, 'destroy'])->name('destroy');
    });

});

// Route::resource('/products', ProductController::class);
// Route::resource('/purchases', PurchaseController::class);
// Route::get('/production/receiving', [ProductionController::class, 'receiving'])->name('production.receiving');
// Route::resource('/production', ProductionController::class);
// Route::resource('/sales', SaleController::class);
// Route::resource('/subhead-of-accounts', SubHeadOfAccController::class);
// Route::resource('/chart-of-accounts', COAController::class);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
