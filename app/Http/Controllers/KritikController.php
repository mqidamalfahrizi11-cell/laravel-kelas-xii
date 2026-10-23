<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Kritik;
use App\Http\Requests\StoreKritikRequest;
use Illuminate\Support\Facades\Auth;

class KritikController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKritikRequest $request, Film $film)
    {
        Kritik::create([
            'comment' => $request->comment,
            'rating'  => $request->rating,
            'user_id' => Auth::id(),
            'film_id' => $film->id,
        ]);

        return redirect()->route('movies.show', $film->id)
                         ->with('success', 'Komentar berhasil ditambahkan!');
    }
}
