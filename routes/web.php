<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OpenApiController;
use App\Http\Controllers\PlaygroundController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

// Public docs
Route::get('/', [ProjectController::class, 'index']);
Route::get('/p/{project}', [ProjectController::class, 'show']);
Route::get('/p/{project}/openapi', [OpenApiController::class, 'spec']);

// Manage
Route::get('/manage', [ProjectController::class, 'manage'])->name('manage');
Route::get('/manage/projects/create', [ProjectController::class, 'create']);
Route::post('/manage/projects', [ProjectController::class, 'store']);
Route::get('/manage/projects/{project}/edit', [ProjectController::class, 'edit']);
Route::put('/manage/projects/{project}', [ProjectController::class, 'update']);
Route::delete('/manage/projects/{project}', [ProjectController::class, 'destroy']);

Route::post('/manage/projects/{project}/groups', [GroupController::class, 'store']);
Route::put('/manage/groups/{group}', [GroupController::class, 'update']);
Route::delete('/manage/groups/{group}', [GroupController::class, 'destroy']);

Route::get('/manage/projects/{project}/apis/create', [ApiController::class, 'create']);
Route::post('/manage/projects/{project}/apis', [ApiController::class, 'store']);
Route::get('/manage/projects/{project}/apis/{api}/edit', [ApiController::class, 'edit']);
Route::put('/manage/projects/{project}/apis/{api}', [ApiController::class, 'update']);
Route::delete('/manage/projects/{project}/apis/{api}', [ApiController::class, 'destroy']);

// Playground
Route::post('/playground/{api}', [PlaygroundController::class, 'send']);
