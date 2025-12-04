<?php

use App\Events\QuestionUpdated;
use App\Http\Controllers\ProfileController;
use App\Livewire\QuizController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
Route::get('/test', function () {
    QuestionUpdated::dispatch();

    return 'Event dispatched';
});
*/

Route::get('/', QuizController::class)->name('quiz.play')->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
