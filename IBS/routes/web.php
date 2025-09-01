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

/*
|--------------------------------------------------------------------------
| Public (guest-only) auth routes
|--------------------------------------------------------------------------
| Login & Register pages are accessible only when NOT authenticated.
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // Register (new users default to role=client)
    Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Admin login
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
| Sends users to the right landing page by role. Guests -> /login.
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(
            Auth::user()->role === 'admin' ? 'dashboard' : 'client.home'
        );
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin area (protected)
|--------------------------------------------------------------------------
| Admin keeps access to: dashboard, leads, clients, tasks, projects, staff,
| and client requests (open & history).
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/leads-chart', [LeadController::class, 'chart'])->name('leads.chart');
    Route::resource('leads', LeadController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('staff', StaffController::class);

    Route::post('/tasks/{task}/handle', [TaskHandlerController::class, 'store'])
        ->name('task-handlers.store');

    // Client Requests for Admin
    Route::get('/admin/requests', [ClientRequestController::class, 'index'])
        ->name('admin.requests.index');                  // Open
    Route::get('/admin/requests/history', [ClientRequestController::class, 'history'])
        ->name('admin.requests.history');                // History (Resolved)
    Route::get('/admin/requests/{requestItem}', [ClientRequestController::class, 'show'])
        ->whereNumber('requestItem')                     // prevents 'history' catching
        ->name('admin.requests.show');
    Route::patch('/admin/requests/{requestItem}', [ClientRequestController::class, 'update'])
        ->whereNumber('requestItem')
        ->name('admin.requests.update');
});

/*
|--------------------------------------------------------------------------
| Client area (protected)
|--------------------------------------------------------------------------
| Client sees their own index only + actions to rate tasks, send requests,
| and download their data as PDF.
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client', [ClientPortalController::class, 'index'])
        ->name('client.home');

    // NEW: download the client's data (tasks + requests) as PDF
    Route::get('/client/export/pdf', [ClientPortalController::class, 'exportPdf'])
        ->name('client.export.pdf');

    // Client actions
    Route::post('/client/tasks/{task}/rate', [ClientPortalController::class, 'rateTask'])
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
