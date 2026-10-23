<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('films')->orderBy('name')->paginate(10);

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        Genre::create($request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil ditambahkan!');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil diperbarui!');
    }

    public function destroy(Genre $genre)
    {
        $filmsCount = $genre->films()->count();

        if ($filmsCount > 0) {
            return redirect()->route('admin.genres.index')->with('error', "Genre masih dipakai oleh {$filmsCount} film");
        }

        $genre->delete();

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil dihapus!');
    }
}
