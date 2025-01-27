<?php

use App\Actions\Fortify\CreateNewUser;
use App\DataObjects\UserData;
use App\Http\Controllers\Users\UserQueryBuilderController;
use App\Http\Controllers\Users\UserUpdateController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(static function () {
    Route::name('api')->as('web.api.')->group(static function () {
        Route::as('users.')->prefix('users')->group(static function () {
            Route::get('me', static fn (Request $request) => $request->user())->name('current');
            Route::get('', [UserQueryBuilderController::class, 'index'])->name('index');
            Route::get('{id}', [UserQueryBuilderController::class, 'show'])->name('show');
            Route::get('all', [UserQueryBuilderController::class, 'all'])->name('all');
            Route::post(
                'create',
                static fn () => UserData::fromModel((new CreateNewUser)->create(request()?->input()))
            )->name('create');
            Route::put('{user}', UserUpdateController::class)->name('update');
        });
    });

    Route::inertia('/', 'Page1/DataTableExample')->name('page1');
    Route::inertia('page2', 'Page2/ReverbExample')->name('page2');
    Route::inertia('profile', 'Users/ProfileEdit')->name('me.profile');
    Route::inertia('settings', 'Users/UserSettings')->name('me.settings');
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy']);
});
