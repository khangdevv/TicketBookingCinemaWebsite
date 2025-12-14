<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\AdminController;

// Redirect root to login if not authenticated, otherwise redirect based on role
Route::get('/', function () {
    if (auth()->guard('web')->check()) {
        $user = auth()->guard('web')->user();

        // Nếu là ADMIN hoặc STAFF thì vào admin panel
        if (in_array($user->role, ['ADMIN', 'STAFF'])) {
            return redirect()->route('admin.dashboard');
        }

        // Nếu là CUSTOMER thì vào trang đặt vé
        return redirect()->route('booking.index');
    }
    return redirect()->route('auth.login.form');
})->name('home');

// Booking Routes (Requires Authentication + CUSTOMER role only)
Route::middleware(['auth:web', 'role:CUSTOMER'])->group(function () {
    Route::get('/movies', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/movies/{id}', [BookingController::class, 'showMovieDetail'])->name('booking.movie.detail');
    Route::get('/showtimes/{id}/seats', [BookingController::class, 'seatMap'])->name('booking.seat-map');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-bookings', [DashboardController::class, 'myBookings'])->name('my.bookings');
});

// Authentication Routes (Guest only)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

// Logout (Authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:web');

// Admin Routes (Requires Authentication + ADMIN/STAFF role only)
Route::middleware(['auth:web', 'role:ADMIN,STAFF'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');

    // Movie Management
    Route::get('/movies', [AdminController::class, 'moviesList'])->name('admin.movies.list');
    Route::get('/movies/create', [AdminController::class, 'movieCreate'])->name('admin.movies.create');
    Route::post('/movies', [AdminController::class, 'movieStore'])->name('admin.movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'movieEdit'])->name('admin.movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'movieUpdate'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'movieDelete'])->name('admin.movies.delete');
    Route::patch('/movies/{id}/toggle-status', [AdminController::class, 'movieToggleStatus'])->name('admin.movies.toggle-status');
});
