<?php

use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;

// User
Route::post('/signup', [SignupController::class, 'register']);
Route::put('/setup-account', [SignupController::class, 'update']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/session-return/{id}', [LoginController::class, 'resession']);
Route::delete('/delete-account/{id}', [UserController::class, 'delete']);
Route::post('/reset-password', [UserController::class, 'reset_password']);

// Notes
Route::post('/note/save', [NoteController::class, 'store']);
Route::get('/note/get/{id}', [NoteController::class, 'fetch']);
Route::delete('/note/delete/{id}', [NoteController::class, 'delete']);

// Subject
Route::post('/subject/save', [SubjectController::class, 'store']);
Route::get('/subject/get/{id}', [SubjectController::class, 'fetch']);
Route::delete('/subject/delete/{id}', [SubjectController::class, 'delete']);

// Assignment
Route::post('/assignment/save', [AssignmentController::class, 'store']);
Route::get('/assignment/get/{id}', [AssignmentController::class, 'fetch']);
Route::delete('/assignment/delete/{id}', [AssignmentController::class, 'delete']);
Route::put('/assignment/update/{id}', [AssignmentController::class, 'update']);

// Schedules
Route::post('/schedule/generate/{id}', [ScheduleController::class, 'generate']);
Route::get('/schedule/get/{id}', [ScheduleController::class, 'fetch']);
Route::delete('/schedule/delete/{id}', [ScheduleController::class, 'delete']);

// Chat
Route::post('/chat/send', [ChatController::class, 'send']);


