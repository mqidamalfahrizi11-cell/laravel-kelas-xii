<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kritik;
use Illuminate\Http\Request;

class KritikController extends Controller
{
    public function index(Request $request)
    {
        $query = Kritik::with(['user', 'film'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('film', function ($f) use ($search) {
                      $f->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $kritiks = $query->paginate(15)->withQueryString();

        return view('admin.kritiks.index', compact('kritiks'));
    }

    public function destroy(Kritik $kritik)
    {
        $kritik->delete();

        return redirect()->route('admin.kritiks.index')->with('success', 'Komentar berhasil dihapus!');
    }
}
