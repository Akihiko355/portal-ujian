<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentNotificationController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/auth/login', fn () => redirect()->route('user.login'))->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('user.login');
    Route::post('/login', [AuthController::class, 'login'])->name('user.login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('user.register');
    Route::post('/register', [AuthController::class, 'register'])->name('user.register.submit');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.profile.password');
    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('user.notifications');
    Route::get('/notifications/recent', [StudentNotificationController::class, 'recent'])->name('user.notifications.recent');
    Route::post('/notifications/{broadcast}/read', [StudentNotificationController::class, 'markBroadcastRead'])->name('user.notifications.read');
    Route::post('/notifications/dismiss', [StudentNotificationController::class, 'dismissBroadcast'])->name('user.notifications.dismiss');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/import', [UserController::class, 'importForm'])->name('users.import');
        Route::post('users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('users/import/preview', [UserController::class, 'importPreview'])->name('users.import.preview');
        Route::get('users/import/confirm', [UserController::class, 'importConfirm'])->name('users.import.confirm');
        Route::post('users/import/finalize', [UserController::class, 'importFinalize'])->name('users.import.finalize');
        Route::resource('exam-schedules', ExamScheduleController::class)->except(['show']);
        Route::resource('scores', ScoreController::class)->except(['show']);
        Route::post('/scores/{score}/publish', [ScoreController::class, 'publish'])->name('scores.publish');
        Route::post('/scores/{score}/unpublish', [ScoreController::class, 'unpublish'])->name('scores.unpublish');
        Route::post('/scores/publish-all', [ScoreController::class, 'publishAll'])->name('scores.publish-all');
        Route::post('/scores/unpublish-all', [ScoreController::class, 'unpublishAll'])->name('scores.unpublish-all');
        Route::get('/logs', [LogController::class, 'index'])->name('logs');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('broadcasts');
        Route::get('/broadcasts/create', [BroadcastController::class, 'create'])->name('broadcasts.create');
        Route::post('/broadcasts', [BroadcastController::class, 'store'])->name('broadcasts.store');
        Route::get('/broadcasts/{broadcast}', [BroadcastController::class, 'show'])->name('broadcasts.show');
        Route::delete('/broadcasts/{broadcast}', [BroadcastController::class, 'destroy'])->name('broadcasts.destroy');
        Route::get('/broadcasts/recipient-count', [BroadcastController::class, 'recipientCount'])->name('broadcasts.recipient-count');
    });
});
