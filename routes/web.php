<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [ChatController::class, 'index'])->name('home');
Route::get('/chat/{client}', [ChatController::class, 'chat'])->name('chat.user');
Route::post('/chat/message', [ChatController::class, 'store']);
Route::put('/chat/message/edit/{message}', [ChatController::class, 'update']);
Route::post('/new-censor', [ChatController::class, 'addWordToCensor']);
Route::get('/messages-moderate', [ChatController::class, 'getMessagesForModerate']);
Route::put('/moderate/{message}', [ChatController::class, 'messageModerate']);
Route::get('/monitor', [ChatController::class, 'monitorSupport']);
// Auth::routes();

