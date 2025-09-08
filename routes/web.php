<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TaskHandlerController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\StaffPortalController;

/*
|--------------------------------------------------------------------------
| Prevent route collisions:
| Make the resource param {staff} numeric, so /staff/dashboard won't match it.
|--------------------------------------------------------------------------
*/
Route::pattern('staff', '[0-9]+');

/*
|--------------------------------------------------------------------------
| Public (guest-only) auth routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
});

/*
|--------------------------------------------------------------------------
| Logout (auth only)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Smart home
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $role = strtolower(trim(Auth::user()->role ?? ''));
        return redirect()->route(
            $role === 'admin' ? 'dashboard'
            : ($role === 'staff' ? 'staff.portal.dashboard'
            : 'client.home')
        );
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin area (protected)
|--------------------------------------------------------------------------
| Admin-only CRUD + client requests + notifications
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/leads-chart', [LeadController::class, 'chart'])->name('leads.chart');
    Route::resource('leads', LeadController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('staff', StaffController::class); // Admin-only Staff CRUD

    Route::post('/tasks/{task}/handle', [TaskHandlerController::class, 'store'])
        ->name('task-handlers.store');

    // Client Requests for Admin
    Route::get('/admin/requests', [ClientRequestController::class, 'index'])
        ->name('admin.requests.index');
    Route::get('/admin/requests/history', [ClientRequestController::class, 'history'])
        ->name('admin.requests.history');
    Route::get('/admin/requests/{requestItem}', [ClientRequestController::class, 'show'])
        ->whereNumber('requestItem')
        ->name('admin.requests.show');
    Route::patch('/admin/requests/{requestItem}', [ClientRequestController::class, 'update'])
        ->whereNumber('requestItem')
        ->name('admin.requests.update');

    // NEW: Admin notifications
    Route::get('/admin/notifications', [DashboardController::class, 'notifications'])
        ->name('admin.notifications');
    Route::post('/admin/notifications/read-all', [DashboardController::class, 'readAllNotifications'])
        ->name('admin.notifications.readAll');
});

/*
|--------------------------------------------------------------------------
| Staff area (protected)
|--------------------------------------------------------------------------
| Staff dashboard + task progress + file uploads
*/
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.portal.')
    ->group(function () {
        Route::get('/dashboard', [StaffPortalController::class, 'dashboard'])
            ->name('dashboard');

        // NEW: staff can update progress / mark complete (assigned staff only)
        Route::post('/tasks/{task}/progress', [StaffPortalController::class, 'updateProgress'])
            ->whereNumber('task')
            ->name('tasks.progress');

        // NEW: staff can upload proof attachments
        Route::post('/tasks/{task}/attachments', [StaffPortalController::class, 'uploadAttachments'])
            ->whereNumber('task')
            ->name('tasks.attachments.upload');
    });

/*
|--------------------------------------------------------------------------
| Client area (protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client', [ClientPortalController::class, 'index'])
        ->name('client.home');

    Route::get('/client/export/pdf', [ClientPortalController::class, 'exportPdf'])
        ->name('client.export.pdf');

    Route::post('/client/tasks/{task}/rate', [ClientPortalController::class, 'rateTask'])
        ->whereNumber('task')
        ->name('client.tasks.rate');

    Route::post('/client/requests', [ClientPortalController::class, 'storeRequest'])
        ->name('client.requests.store');
});

/*
|--------------------------------------------------------------------------
| Settings (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
});
