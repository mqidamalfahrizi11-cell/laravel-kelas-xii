<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastRequest;
use App\Http\Requests\UpdateCastRequest;
use App\Models\Cast;
use Illuminate\Http\Request;

class CastController extends Controller
{
    public function index(Request $request)
    {
        $query = Cast::withCount('perans')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $casts = $query->paginate(10)->withQueryString();

        return view('admin.casts.index', compact('casts'));
    }

    public function create()
    {
        return view('admin.casts.create');
    }

    public function store(StoreCastRequest $request)
    {
        Cast::create($request->validated());

        return redirect()->route('admin.casts.index')->with('success', 'Cast berhasil ditambahkan!');
    }

    public function edit(Cast $cast)
    {
        return view('admin.casts.edit', compact('cast'));
    }

    public function update(UpdateCastRequest $request, Cast $cast)
    {
        $cast->update($request->validated());

        return redirect()->route('admin.casts.index')->with('success', 'Cast berhasil diperbarui!');
    }

    public function destroy(Cast $cast)
    {
        $peransCount = $cast->perans()->count();

        if ($peransCount > 0) {
            return redirect()->route('admin.casts.index')->with('error', "Cast masih dipakai oleh {$peransCount} peran");
        }

        $cast->delete();

        return redirect()->route('admin.casts.index')->with('success', 'Cast berhasil dihapus!');
    }
}
