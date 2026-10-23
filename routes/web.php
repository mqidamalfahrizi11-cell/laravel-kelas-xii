<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    FilmController,
};
use App\Http\Controllers\Admin\{
    AdminDashboardController,
    CastController,
    FilmController as AdminFilmController,
    GenreController,
    KritikController,
    PeranController,
};

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/movies/{film}/kritik', [\App\Http\Controllers\KritikController::class, 'store'])
    ->middleware('auth')
    ->name('kritik.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('films', AdminFilmController::class)->except(['show']);
    Route::resource('genres', GenreController::class)->except(['show']);
    Route::resource('casts', CastController::class)->except(['show']);
    Route::resource('perans', PeranController::class)->except(['show']);
    Route::resource('kritiks', KritikController::class)->only(['index', 'destroy']);
});
