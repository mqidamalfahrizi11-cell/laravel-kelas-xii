<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminModuleTest extends TestCase
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

    // --- GENRE MODULE ---
    public function test_genre_crud_and_validation(): void
    {
        $admin = $this->getAdminUser();

        // 1. Create
        $response = $this->actingAs($admin)->post(route('admin.genres.store'), [
            'name' => 'Genre Unik Baru',
        ]);
        $response->assertRedirect(route('admin.genres.index'));
        $response->assertSessionHas('success');
        $genre = Genre::where('name', 'Genre Unik Baru')->first();
        $this->assertNotNull($genre);

        // 2. Validation unique
        $dupResponse = $this->actingAs($admin)->post(route('admin.genres.store'), [
            'name' => 'Genre Unik Baru',
        ]);
        $dupResponse->assertSessionHasErrors('name');

        // 3. Edit & Update
        $editResponse = $this->actingAs($admin)->get(route('admin.genres.edit', $genre->id));
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($admin)->put(route('admin.genres.update', $genre->id), [
            'name' => 'Genre Unik Diubah',
        ]);
        $updateResponse->assertRedirect(route('admin.genres.index'));
        $genre->refresh();
        $this->assertEquals('Genre Unik Diubah', $genre->name);

        // 4. Delete unused genre
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.genres.destroy', $genre->id));
        $deleteResponse->assertRedirect(route('admin.genres.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Genre::find($genre->id));
    }

    public function test_delete_genre_used_by_films_is_rejected(): void
    {
        $admin = $this->getAdminUser();
        // Find a genre used by a film
        $film = Film::first();
        $this->assertNotNull($film);
        $genre = Genre::find($film->genre_id);
        $this->assertNotNull($genre);

        $response = $this->actingAs($admin)->delete(route('admin.genres.destroy', $genre->id));
        $response->assertRedirect(route('admin.genres.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Genre masih dipakai oleh', session('error'));
        $this->assertNotNull(Genre::find($genre->id));
    }

    // --- CAST MODULE ---
    public function test_cast_crud_and_validation(): void
    {
        $admin = $this->getAdminUser();

        // 1. Create
        $response = $this->actingAs($admin)->post(route('admin.casts.store'), [
            'name' => 'Aktor Uji Baru',
            'age' => 35,
            'bio' => 'Biografi aktor uji yang lengkap.',
        ]);
        $response->assertRedirect(route('admin.casts.index'));
        $response->assertSessionHas('success');
        $cast = Cast::where('name', 'Aktor Uji Baru')->first();
        $this->assertNotNull($cast);

        // 2. Validation invalid age
        $invResponse = $this->actingAs($admin)->post(route('admin.casts.store'), [
            'name' => 'Aktor Invalid',
            'age' => 200, // max 120
            'bio' => 'Bio',
        ]);
        $invResponse->assertSessionHasErrors('age');

        // 3. Edit & Update
        $updateResponse = $this->actingAs($admin)->put(route('admin.casts.update', $cast->id), [
            'name' => 'Aktor Uji Diperbarui',
            'age' => 36,
            'bio' => 'Biografi diperbarui.',
        ]);
        $updateResponse->assertRedirect(route('admin.casts.index'));
        $cast->refresh();
        $this->assertEquals('Aktor Uji Diperbarui', $cast->name);
        $this->assertEquals(36, $cast->age);

        // 4. Delete unused cast
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.casts.destroy', $cast->id));
        $deleteResponse->assertRedirect(route('admin.casts.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Cast::find($cast->id));
    }

    public function test_delete_cast_used_in_peran_is_rejected(): void
    {
        $admin = $this->getAdminUser();
        $peran = Peran::first();
        $this->assertNotNull($peran);
        $cast = Cast::find($peran->cast_id);
        $this->assertNotNull($cast);

        $response = $this->actingAs($admin)->delete(route('admin.casts.destroy', $cast->id));
        $response->assertRedirect(route('admin.casts.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Cast masih dipakai oleh', session('error'));
        $this->assertNotNull(Cast::find($cast->id));
    }

    // --- PERAN MODULE ---
    public function test_peran_crud_and_display(): void
    {
        $admin = $this->getAdminUser();
        $film = Film::first();
        $cast = Cast::first();

        // 1. Create
        $response = $this->actingAs($admin)->post(route('admin.perans.store'), [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Superhero Uji',
        ]);
        $response->assertRedirect(route('admin.perans.index'));
        $response->assertSessionHas('success');

        $peran = Peran::where('actor', 'Karakter Superhero Uji')->first();
        $this->assertNotNull($peran);

        // 2. Check appears in index
        $indexResponse = $this->actingAs($admin)->get(route('admin.perans.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Karakter Superhero Uji');
        $indexResponse->assertSee($film->title);
        $indexResponse->assertSee($cast->name);

        // 3. Edit & Update
        $updateResponse = $this->actingAs($admin)->put(route('admin.perans.update', $peran->id), [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Karakter Diperbarui',
        ]);
        $updateResponse->assertRedirect(route('admin.perans.index'));
        $peran->refresh();
        $this->assertEquals('Karakter Diperbarui', $peran->actor);

        // 4. Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.perans.destroy', $peran->id));
        $deleteResponse->assertRedirect(route('admin.perans.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Peran::find($peran->id));
    }

    // --- KRITIK MODULE ---
    public function test_kritik_list_displays_user_name_and_film_title_and_delete_works(): void
    {
        $admin = $this->getAdminUser();
        $user = $this->getRegularUser();
        $film = Film::first();

        $kritik = Kritik::create([
            'film_id' => $film->id,
            'user_id' => $user->id,
            'comment' => 'Komentar spesifik untuk testing display di admin index.',
            'rating' => 4,
        ]);

        $indexResponse = $this->actingAs($admin)->get(route('admin.kritiks.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($user->name);
        $indexResponse->assertSee($film->title);
        $indexResponse->assertSee('Komentar spesifik untuk testing');

        // Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.kritiks.destroy', $kritik->id));
        $deleteResponse->assertRedirect(route('admin.kritiks.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Kritik::find($kritik->id));
    }

    // --- ACCESS PROTECTION ---
    public function test_all_new_admin_routes_redirect_guest(): void
    {
        $routes = [
            route('admin.genres.index'),
            route('admin.genres.create'),
            route('admin.casts.index'),
            route('admin.casts.create'),
            route('admin.perans.index'),
            route('admin.perans.create'),
            route('admin.kritiks.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('home'));
        }
    }

    public function test_all_new_admin_routes_return_403_for_regular_user(): void
    {
        $routes = [
            route('admin.genres.index'),
            route('admin.genres.create'),
            route('admin.casts.index'),
            route('admin.casts.create'),
            route('admin.perans.index'),
            route('admin.perans.create'),
            route('admin.kritiks.index'),
        ];

        $regularUser = $this->getRegularUser();

        foreach ($routes as $url) {
            $response = $this->actingAs($regularUser)->get($url);
            $response->assertStatus(403);
        }
    }
}
