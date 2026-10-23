<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeranRequest;
use App\Http\Requests\UpdatePeranRequest;
use App\Models\Cast;
use App\Models\Film;
use App\Models\Peran;
use Illuminate\Http\Request;

class PeranController extends Controller
{
    public function index(Request $request)
    {
        $query = Peran::with(['film', 'cast'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('actor', 'like', "%{$search}%")
                  ->orWhereHas('film', function ($f) use ($search) {
                      $f->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('cast', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $perans = $query->paginate(15)->withQueryString();

        return view('admin.perans.index', compact('perans'));
    }

    public function create()
    {
        $films = Film::orderBy('title')->get();
        $casts = Cast::orderBy('name')->get();

        return view('admin.perans.create', compact('films', 'casts'));
    }

    public function store(StorePeranRequest $request)
    {
        Peran::create($request->validated());

        return redirect()->route('admin.perans.index')->with('success', 'Peran berhasil ditambahkan!');
    }

    public function edit(Peran $peran)
    {
        $films = Film::orderBy('title')->get();
        $casts = Cast::orderBy('name')->get();

        return view('admin.perans.edit', compact('peran', 'films', 'casts'));
    }

    public function update(UpdatePeranRequest $request, Peran $peran)
    {
        $peran->update($request->validated());

        return redirect()->route('admin.perans.index')->with('success', 'Peran berhasil diperbarui!');
    }

    public function destroy(Peran $peran)
    {
        $peran->delete();

        return redirect()->route('admin.perans.index')->with('success', 'Peran berhasil dihapus!');
    }
}
