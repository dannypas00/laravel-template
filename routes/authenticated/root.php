<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::middleware('verified')->group(function () {
    require __DIR__ . '/views.php';
});

Route::middleware(['verified', 'api'])->group(function () {
    require __DIR__ . '/webapi.php';
});
