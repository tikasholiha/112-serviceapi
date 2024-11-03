<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Calls\CallController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Emergency\EmergencyController;
use App\Http\Controllers\EmployeeKPIController;
use App\Http\Controllers\Master\DistrictController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\ReligionController;
use App\Http\Controllers\Master\StatusController;
use App\Http\Controllers\RBAC\MenuController;
use App\Http\Controllers\RBAC\PermissionController;
use App\Http\Controllers\RBAC\RoleController;
use App\Http\Controllers\RBAC\UserController;
use App\Http\Controllers\TestCallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::get('/login',  function (Request $request) {
        return response()->json([
            'error' => 'Token is blacklisted. Please log in again.',
        ], 401);
    })->name('login');

    Route::post('/auth/login',  [AuthController::class, 'login'])->name('post-login');
    Route::post('/auth/refresh-token',  [AuthController::class, 'refresh'])->name('refresh-token');
    Route::post('/auth/logout',  [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:api')->group(function () {
        // Master
        Route::post('/employees/{employee}', [EmployeeController::class, 'update']);
        Route::apiResource('/employees', EmployeeController::class)->except('update');
        Route::apiResource('/employees-kpi', EmployeeKPIController::class);
        Route::apiResource('/marital-status', StatusController::class)->except(['show']);
        Route::apiResource('/religions', ReligionController::class)->except(['show']);
        Route::apiResource('/districts', DistrictController::class)->except(['show']);

        // RBAC
        Route::get("/users/me", [UserController::class, 'me']);
        Route::apiResource('/users', UserController::class);
        Route::post('/users/assign-roles', [UserController::class, 'assign_roles']);
        Route::apiResource('/roles', RoleController::class);
        Route::post('/roles/assign-menus', [RoleController::class, 'assign_menus'])->name('assign-menus');
        Route::post('/roles/assign-permissions', [RoleController::class, 'assign_permissions'])->name('assign-permissions');
        Route::apiResource('/menus', MenuController::class);
        Route::apiResource('/permissions', PermissionController::class);

        // Call Reports
        Route::get('/call-reports/export', [CallController::class, 'export_data']);
        Route::apiResource("/call-reports", CallController::class);

        // Test Call Reports
        Route::get('/test-calls/export', [TestCallController::class, 'export_data']);
        Route::apiResource("/test-calls", TestCallController::class);

        // Emergency Reports
        Route::get('/emergency-reports/export', [EmergencyController::class, 'export_data']);
        Route::get('/emergency-reports/{month_period}/{year}', [EmergencyController::class, 'show_by_period']);
        Route::apiResource("/emergency-reports", EmergencyController::class);

        // Dashboard
        Route::post('/dashboard/call-reports', [DashboardController::class, 'call_reports'])->name('call-reports');
        Route::post('/dashboard/emergency-reports', [DashboardController::class, 'emergency_reports'])->name('emergency_reports');
    });
});
