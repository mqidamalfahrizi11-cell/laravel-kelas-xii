<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    protected function getAdminUser(): User
    {
        return User::where('role_id', 2)->first() ?? User::factory()->create(['role_id' => 2]);
    }

    protected function getRegularUser(): User
    {
        return User::where('role_id', 1)->first() ?? User::factory()->create(['role_id' => 1]);
    }

    public function test_guest_accessing_admin_is_redirected_out(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('home'));
    }

    public function test_regular_user_accessing_admin_gets_403(): void
    {
        $user = $this->getRegularUser();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_accessing_admin_sees_dashboard_with_correct_counts(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Ringkasan');
        $response->assertSee(number_format(Film::count()));
        $response->assertSee(number_format(Genre::count()));
        $response->assertSee(number_format(Cast::count()));
        $response->assertSee(number_format(User::count()));
        $response->assertSee(number_format(Kritik::count()));
    }

    public function test_admin_can_create_film_with_poster(): void
    {
        Storage::fake('public');
        $admin = $this->getAdminUser();
        $genre = Genre::first();

        $poster = UploadedFile::fake()->image('test_poster.jpg', 600, 800);

        $response = $this->actingAs($admin)->post(route('admin.films.store'), [
            'title' => 'Film Keren 2026',
            'sinopsis' => 'Sinopsis film keren luar biasa untuk testing.',
            'year' => 2026,
            'genre_id' => $genre->id,
            'poster' => $poster,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $film = Film::where('title', 'Film Keren 2026')->first();
        $this->assertNotNull($film);
        $this->assertStringStartsWith('storage/images/', $film->poster);

        // Check storage has the file
        $storedPath = str_replace('storage/', '', $film->poster);
        Storage::disk('public')->assertExists($storedPath);

        // Check public page has the film
        $moviesResponse = $this->get('/movies');
        $moviesResponse->assertSee('Film Keren 2026');
        $moviesResponse->assertSee($film->poster);
    }

    public function test_admin_can_edit_film_without_changing_poster(): void
    {
        $admin = $this->getAdminUser();
        $film = Film::first();
        $originalPoster = $film->poster;

        $response = $this->actingAs($admin)->put(route('admin.films.update', $film->id), [
            'title' => 'Judul Film Diperbarui',
            'sinopsis' => 'Sinopsis baru yang diperbarui.',
            'year' => $film->year,
            'genre_id' => $film->genre_id,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $film->refresh();
        $this->assertEquals('Judul Film Diperbarui', $film->title);
        $this->assertEquals($originalPoster, $film->poster);
    }

    public function test_admin_can_edit_film_with_new_poster(): void
    {
        Storage::fake('public');
        $admin = $this->getAdminUser();
        $film = Film::first();

        $newPoster = UploadedFile::fake()->image('new_poster.jpg', 600, 800);

        $response = $this->actingAs($admin)->put(route('admin.films.update', $film->id), [
            'title' => 'Judul Baru Dengan Poster Baru',
            'sinopsis' => $film->sinopsis,
            'year' => $film->year,
            'genre_id' => $film->genre_id,
            'poster' => $newPoster,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $film->refresh();
        $this->assertEquals('Judul Baru Dengan Poster Baru', $film->title);
        $this->assertStringStartsWith('storage/images/', $film->poster);

        $storedPath = str_replace('storage/', '', $film->poster);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_film_validation_rules(): void
    {
        $admin = $this->getAdminUser();

        // Empty title
        $response1 = $this->actingAs($admin)->post(route('admin.films.store'), [
            'title' => '',
            'sinopsis' => 'Test',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => UploadedFile::fake()->image('test.jpg'),
        ]);
        $response1->assertSessionHasErrors('title');

        // File is not an image
        $response2 = $this->actingAs($admin)->post(route('admin.films.store'), [
            'title' => 'Film Non Image',
            'sinopsis' => 'Test',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => UploadedFile::fake()->create('document.pdf', 100),
        ]);
        $response2->assertSessionHasErrors('poster');

        // File size > 2MB (e.g. 3000KB)
        $response3 = $this->actingAs($admin)->post(route('admin.films.store'), [
            'title' => 'Film Oversized',
            'sinopsis' => 'Test',
            'year' => 2024,
            'genre_id' => 1,
            'poster' => UploadedFile::fake()->create('big_image.jpg', 3000),
        ]);
        $response3->assertSessionHasErrors('poster');
    }

    public function test_delete_film_with_perans_and_kritiks_deletes_successfully_without_fk_error(): void
    {
        $admin = $this->getAdminUser();

        // Create a film with cast and comment
        $genre = Genre::first();
        $film = Film::create([
            'title' => 'Film Hapus Test',
            'sinopsis' => 'Sinopsis yang akan dihapus.',
            'year' => 2024,
            'genre_id' => $genre->id,
            'poster' => 'storage/images/sample.jpg',
        ]);

        $cast = Cast::first() ?? Cast::create(['name' => 'Aktor Test', 'age' => 30, 'bio' => '-']);
        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Utama',
        ]);

        $user = $this->getRegularUser();
        Kritik::create([
            'film_id' => $film->id,
            'user_id' => $user->id,
            'comment' => 'Komentar film uji',
            'rating' => 5,
        ]);

        $this->assertEquals(1, Peran::where('film_id', $film->id)->count());
        $this->assertEquals(1, Kritik::where('film_id', $film->id)->count());

        $response = $this->actingAs($admin)->delete(route('admin.films.destroy', $film->id));
        $response->assertRedirect(route('admin.films.index'));

        $this->assertNull(Film::find($film->id));
        $this->assertEquals(0, Peran::where('film_id', $film->id)->count());
        $this->assertEquals(0, Kritik::where('film_id', $film->id)->count());

        // Check it is gone from public
        $publicResponse = $this->get('/movies/' . $film->id);
        $publicResponse->assertStatus(404);
    }

    public function test_title_search_and_pagination_works(): void
    {
        $admin = $this->getAdminUser();

        // Index default
        $response = $this->actingAs($admin)->get(route('admin.films.index'));
        $response->assertStatus(200);

        // Search for a specific film title
        $firstFilm = Film::first();
        $searchResponse = $this->actingAs($admin)->get(route('admin.films.index', ['search' => $firstFilm->title]));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($firstFilm->title);

        // Pagination page 2
        $page2Response = $this->actingAs($admin)->get(route('admin.films.index', ['page' => 2]));
        $page2Response->assertStatus(200);
    }

    public function test_login_as_admin_redirects_to_admin_dashboard_and_header_shows_dashboard_link(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => '12345678',
            'form_type' => 'login',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $homeResponse = $this->get('/');
        $homeResponse->assertSee(route('admin.dashboard'));
        $homeResponse->assertSee('Dashboard');
    }
}
