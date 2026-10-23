<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $filmCount = Film::count();
        $genreCount = Genre::count();
        $castCount = Cast::count();
        $userCount = User::count();
        $komentarCount = Kritik::count();

        $recentFilms = Film::with('genre')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'filmCount',
            'genreCount',
            'castCount',
            'userCount',
            'komentarCount',
            'recentFilms'
        ));
    }
}
