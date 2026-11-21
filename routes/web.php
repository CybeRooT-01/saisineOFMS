<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaisineController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttachmentController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API routes for dynamic data
    Route::get('/api/request-types/{requestType}/mandatory-fields', function(\App\Models\RequestType $requestType) {
        return response()->json($requestType->getMandatoryFieldsArray());
    });
    
    // Saisine routes
    Route::get('/saisines/all', [SaisineController::class, 'all'])->name('saisines.all');
    Route::get('/saisines/{saisine}/export', [SaisineController::class, 'export'])->name('saisines.export');
    Route::resource('saisines', SaisineController::class);
    
    // Validation routes
    Route::post('/saisines/{saisine}/validate', [ValidationController::class, 'validate'])->name('saisines.validate');
    Route::post('/saisines/{saisine}/request-completion', [ValidationController::class, 'requestCompletion'])->name('saisines.request-completion');
    Route::post('/saisines/{saisine}/reject', [ValidationController::class, 'reject'])->name('saisines.reject');
    
    // Attachment routes
    Route::post('/saisines/{saisine}/attachments', [AttachmentController::class, 'store'])->name('saisines.attachments.store');
    Route::get('/saisines/{saisine}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('saisines.attachments.download');
    Route::delete('/saisines/{saisine}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('saisines.attachments.destroy');
    
    // Contributor routes
    Route::post('/saisines/{saisine}/contributors', [ContributorController::class, 'store'])->name('saisines.contributors.store');
    Route::delete('/saisines/{saisine}/contributors/{contributorUser}', [ContributorController::class, 'destroy'])->name('saisines.contributors.destroy');
    
    // Version routes
    Route::get('/saisines/{saisine}/versions', [VersionController::class, 'index'])->name('saisines.versions.index');
    Route::get('/saisines/{saisine}/versions/{version}', [VersionController::class, 'show'])->name('saisines.versions.show');
    Route::get('/saisines/{saisine}/versions-compare', [VersionController::class, 'compare'])->name('saisines.versions.compare');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    
    // Admin routes
    Route::get('/admin/mandatory-fields', [AdminController::class, 'mandatoryFields'])->name('admin.mandatory-fields');
    Route::post('/admin/mandatory-fields', [AdminController::class, 'storeMandatoryField'])->name('admin.mandatory-fields.store');
    Route::delete('/admin/mandatory-fields/{mandatoryField}', [AdminController::class, 'destroyMandatoryField'])->name('admin.mandatory-fields.destroy');
    
    Route::get('/admin/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::post('/admin/roles', [AdminController::class, 'storeRole'])->name('admin.roles.store');
    Route::put('/admin/roles/{role}', [AdminController::class, 'updateRole'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [AdminController::class, 'destroyRole'])->name('admin.roles.destroy');
    
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    Route::get('/admin/structures', [AdminController::class, 'structures'])->name('admin.structures');
    Route::post('/admin/structures', [AdminController::class, 'storeStructure'])->name('admin.structures.store');
    Route::put('/admin/structures/{structure}', [AdminController::class, 'updateStructure'])->name('admin.structures.update');
    Route::delete('/admin/structures/{structure}', [AdminController::class, 'destroyStructure'])->name('admin.structures.destroy');
});

// Redirect root to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
