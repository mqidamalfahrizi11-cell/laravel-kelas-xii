<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MovieShowCastTest extends TestCase
{
    use DatabaseTransactions;

    public function test_detail_film_displays_cast_name_and_role_name(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Detail Test ' . uniqid(),
            'sinopsis' => 'Sinopsis test detail.',
            'year' => 2025,
            'genre_id' => $genre->id,
            'poster' => 'storage/images/sample.jpg',
        ]);

        $cast = Cast::create([
            'name' => 'Aktor Bintang Utama',
            'age' => 40,
            'bio' => 'Bio',
        ]);

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Kapten Perkasa',
        ]);

        $response = $this->get('/movies/' . $film->id);
        $response->assertStatus(200);

        // Header table
        $response->assertSee('Nama Cast');
        $response->assertSee('Peran');

        // Cast name and role
        $response->assertSee('Aktor Bintang Utama');
        $response->assertSee('Karakter Kapten Perkasa');
    }

    public function test_film_without_perans_displays_empty_state_message(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Tanpa Peran ' . uniqid(),
            'sinopsis' => 'Sinopsis tanpa peran.',
            'year' => 2025,
            'genre_id' => $genre->id,
            'poster' => 'storage/images/sample.jpg',
        ]);

        $response = $this->get('/movies/' . $film->id);
        $response->assertStatus(200);
        $response->assertSee('Belum ada data cast');
    }

    public function test_query_count_for_perans_and_cast_is_not_n_plus_one(): void
    {
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Multi Peran ' . uniqid(),
            'sinopsis' => 'Sinopsis multi peran.',
            'year' => 2025,
            'genre_id' => $genre->id,
            'poster' => 'storage/images/sample.jpg',
        ]);

        // Add 5 perans with different casts
        for ($i = 1; $i <= 5; $i++) {
            $cast = Cast::create(['name' => "Cast Ke-$i", 'age' => 20 + $i, 'bio' => '-']);
            Peran::create([
                'film_id' => $film->id,
                'cast_id' => $cast->id,
                'actor' => "Peran Ke-$i",
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get('/movies/' . $film->id);
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Find queries related to perans and casts
        $peranQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'perans');
        });
        $castQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'casts');
        });

        // Exactly 1 query for perans and at most 1 query for eager-loaded casts
        $this->assertCount(1, $peranQueries, 'Query perans harus tepat 1 kali.');
        $this->assertLessThanOrEqual(1, count($castQueries), 'Query casts tidak boleh berulang N+1 per baris.');
    }

    public function test_peran_added_via_admin_appears_on_film_public_page(): void
    {
        $admin = User::where('role_id', 2)->first() ?? User::factory()->create(['role_id' => 2]);
        $film = Film::first();
        $cast = Cast::first();

        $uniqueRole = 'Karakter Admin Test ' . uniqid();

        // Admin adds peran
        $response = $this->actingAs($admin)->post(route('admin.perans.store'), [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => $uniqueRole,
        ]);
        $response->assertRedirect(route('admin.perans.index'));

        // Check public movie show
        $publicResponse = $this->get('/movies/' . $film->id);
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee($uniqueRole);
        $publicResponse->assertSee($cast->name);
    }
}
