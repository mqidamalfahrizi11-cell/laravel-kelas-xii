<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Film::with('genre')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $films = $query->paginate(10)->withQueryString();

        return view('admin.films.index', compact('films'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::all();

        return view('admin.films.create', compact('genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilmRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('images', 'public');
            $data['poster'] = 'storage/' . $path;
        }

        Film::create($data);

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film)
    {
        $genres = Genre::all();

        return view('admin.films.edit', compact('film', 'genres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        $data = $request->validated();

        if ($request->hasFile('poster')) {
            // Delete old poster from storage if it exists
            if ($film->poster && Str::startsWith($film->poster, 'storage/')) {
                $oldPath = Str::after($film->poster, 'storage/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('poster')->store('images', 'public');
            $data['poster'] = 'storage/' . $path;
        } else {
            unset($data['poster']);
        }

        $film->update($data);

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        DB::transaction(function () use ($film) {
            // Delete related perans and kritiks
            Peran::where('film_id', $film->id)->delete();
            Kritik::where('film_id', $film->id)->delete();

            // Delete poster file if present in storage
            if ($film->poster && Str::startsWith($film->poster, 'storage/')) {
                $oldPath = Str::after($film->poster, 'storage/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $film->delete();
        });

        return redirect()->route('admin.films.index')->with('success', 'Film berhasil dihapus!');
    }
}
