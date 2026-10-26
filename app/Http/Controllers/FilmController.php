<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Film,
    Genre,
    Kritik,
    Peran,
};
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use Carbon\Carbon;

class FilmController extends Controller
{
    /**
     * Return suggestions for movie search.
     */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->input('q'));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        if (mb_strlen($q) > 100) {
            $q = mb_substr($q, 0, 100);
        }

        $ids = Film::selectRaw('MAX(id) as id')
            ->where('title', 'like', '%' . $q . '%')
            ->groupBy('title');

        $films = Film::select('id', 'title', 'year', 'poster')
            ->whereIn('id', $ids)
            ->orderByRaw('title LIKE ? DESC', [$q . '%'])
            ->orderBy('title')
            ->limit(6)
            ->get();

        $data = $films->map(function ($film) {
            return [
                'id' => $film->id,
                'title' => $film->title,
                'year' => $film->year,
                'poster' => asset($film->poster),
                'url' => route('movies.show', $film->id),
            ];
        });

        return response()->json($data);
    }

    /**
     * Search films by keyword.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:100',
        ]);

        $q = $request->input('q');

        $ids = Film::selectRaw('MAX(id) as id')
            ->where('title', 'like', '%' . $q . '%')
            ->groupBy('title');

        $films = Film::select('id', 'title', 'poster', 'year', 'genre_id')
            ->whereIn('id', $ids)
            ->orderByDesc('year')
            ->paginate(18)
            ->withQueryString();

        $genreFilm = null;
        $keyword = $q;
        $notFound = false;

        if ($films->count() > 0) {
            $genreId = $films->first()->genre_id;
            $excludedTitles = $films->pluck('title')->toArray();

            $recIds = Film::selectRaw('MAX(id) as id')
                ->where('genre_id', $genreId)
                ->whereNotIn('title', $excludedTitles)
                ->groupBy('title');

            $recommendations = Film::select('id', 'title', 'poster', 'year')
                ->whereIn('id', $recIds)
                ->orderByDesc('year')
                ->limit(6)
                ->get();

            if ($recommendations->count() < 6) {
                $needed = 6 - $recommendations->count();
                $moreExcludedTitles = array_merge($excludedTitles, $recommendations->pluck('title')->toArray());

                $fallbackIds = Film::selectRaw('MAX(id) as id')
                    ->whereNotIn('title', $moreExcludedTitles)
                    ->groupBy('title');

                $fallbackFilms = Film::select('id', 'title', 'poster', 'year')
                    ->whereIn('id', $fallbackIds)
                    ->orderByDesc('year')
                    ->limit($needed)
                    ->get();

                $recommendations = $recommendations->concat($fallbackFilms);
            }
        } else {
            $notFound = true;

            $recIds = Film::selectRaw('MAX(id) as id')
                ->groupBy('title');

            $recommendations = Film::select('id', 'title', 'poster', 'year')
                ->whereIn('id', $recIds)
                ->orderByDesc('year')
                ->limit(6)
                ->get();
        }

        return view('components.movies', compact('films', 'genreFilm', 'keyword', 'recommendations', 'notFound'));
    }

    /**
     * Display a listing of the resource.
     */
    public function movies()
    {
        //
        $genreFilm = null;
        $films = Film::select('id','title', 'poster','year')
                ->orderByDesc('year')
                ->OrderBy('created_at', 'asc')
                ->paginate(18);
        return view('components/movies', compact('films', 'genreFilm'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilmRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film)
    {
        //
        $filmByGenre    = Film::select('id','title', 'poster','year', 'sinopsis')
                        ->where('genre_id', '=', $film->genre_id)
                        ->OrderBy('created_at', 'asc')
                        ->limit(7)
                        ->get();
        $filmByRelease  = Film::select('id','title', 'poster','year')
                        ->where('year', '=', Carbon::now()->format('Y'))
                        ->OrderBy('created_at', 'asc')
                        ->limit(9)
                        ->get();
        $comments        = Kritik::select('id', 'comment', 'rating', 'user_id', 'created_at')
                        ->with('user:id,name')
                        ->where('film_id', '=', $film->id)
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get();
        $perans         = $film->perans()->with('cast:id,name')->orderBy('id', 'asc')->get();
        return view('components.movie-show', compact('film','filmByGenre','filmByRelease','comments', 'perans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        //
    }

    public function movieHome()
    {
        $films = Film::select('id','title', 'poster','year')
                ->OrderBy('created_at', 'asc')
                ->limit(9)->get();
        $filmFutured = Film::select('id','title', 'poster','year')
                        ->orderByDesc('year')
                        ->OrderBy('created_at', 'asc')
                        ->limit(6)->get();
        $filmRecentAdded = Film::select('id','title', 'poster','year')
                            ->orderByDesc('created_at', 'asc')
                            ->limit(6)->get();
        $filmSlidey = Film::select('title', 'poster','sinopsis')
                        ->orderByDesc('year')
                        ->OrderBy('created_at', 'asc')
                        ->limit(6)->get();
        return view('components/home', compact('filmSlidey','films','filmFutured','filmRecentAdded'));
    }

    public function moviesByGenre($genre)
    {
        //
        $genreFilm = Genre::where('id', '=', $genre)->first();
        $films = Film::select('id','title', 'poster','year')
                ->where('genre_id', '=', $genre)
                ->orderByDesc('year')
                ->OrderBy('created_at', 'asc')
                ->paginate(18);
        return view('components/movies', compact('films','genreFilm'));
    }
}
