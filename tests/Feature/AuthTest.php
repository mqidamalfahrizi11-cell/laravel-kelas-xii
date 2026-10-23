<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;
    public function test_register_new_user_with_valid_data(): void
    {
        $initialUserCount = User::count();
        $initialProfileCount = Profile::count();

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budisantoso_test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'form_type' => 'register',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $this->assertEquals($initialUserCount + 1, User::count());
        $this->assertEquals($initialProfileCount + 1, Profile::count());

        $user = User::where('email', 'budisantoso_test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->role_id);
        $profile = Profile::find($user->profile_id);
        $this->assertNotNull($profile);
        $this->assertEquals(0, $profile->umur);
        $this->assertEquals('-', $profile->bio);
        $this->assertEquals('-', $profile->alamat);

        // Check header has user name and logout button
        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Budi Santoso');
        $homeResponse->assertSee(route('logout'));
    }

    public function test_register_with_existing_email_fails(): void
    {
        $existingUser = User::first();
        $initialUserCount = User::count();
        $initialProfileCount = Profile::count();

        $response = $this->post('/register', [
            'name' => 'Duplikat User',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'form_type' => 'register',
        ]);

        $response->assertSessionHasErrors('email', null, 'register');
        $this->assertEquals($initialUserCount, User::count());
        $this->assertEquals($initialProfileCount, Profile::count());
        $this->assertGuest();

        $page = $this->followRedirects($response);
        $page->assertSee('The email has already been taken');
    }

    public function test_register_with_short_password_or_mismatched_confirmation_fails(): void
    {
        $initialUserCount = User::count();

        // Password < 8 characters
        $response1 = $this->post('/register', [
            'name' => 'User Pendek',
            'email' => 'shortpass@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
            'form_type' => 'register',
        ]);
        $response1->assertSessionHasErrors('password', null, 'register');

        // Mismatched confirmation
        $response2 = $this->post('/register', [
            'name' => 'User Mismatch',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
            'form_type' => 'register',
        ]);
        $response2->assertSessionHasErrors('password', null, 'register');

        $this->assertEquals($initialUserCount, User::count());
        $this->assertGuest();
    }

    public function test_login_with_correct_seed_credentials(): void
    {
        // Admin user redirects to admin.dashboard
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => '12345678',
            'form_type' => 'login',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin@example.com', Auth::user()->email);

        // Regular user redirects to home
        $regularUser = User::where('role_id', 1)->first();
        if ($regularUser) {
            $regResponse = $this->post('/login', [
                'email' => $regularUser->email,
                'password' => '12345678',
                'form_type' => 'login',
            ]);
            $regResponse->assertRedirect(route('home'));
        }
    }

    public function test_login_with_wrong_password_fails_and_preserves_old_email(): void
    {
        $response = $this->from('/')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'passwordsalah',
            'form_type' => 'login',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email', null, 'login');
        $response->assertSessionHasInput('email', 'admin@example.com');
        $this->assertGuest();

        $page = $this->followRedirects($response);
        $page->assertSee('Email atau password salah');
        $page->assertSee('value="admin@example.com"', false);
    }

    public function test_logout_clears_session_and_shows_guest_header(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect(route('home'));
        $this->assertGuest();

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Login');
        $homeResponse->assertDontSee('Logout');
    }

    public function test_get_logout_returns_405_method_not_allowed(): void
    {
        $response = $this->get('/logout');
        $response->assertStatus(405);
    }

    public function test_home_movies_and_movie_detail_pages_still_work(): void
    {
        $home = $this->get('/');
        $home->assertStatus(200);

        $movies = $this->get('/movies');
        $movies->assertStatus(200);

        $film = Film::first();
        $this->assertNotNull($film);

        $detail = $this->get('/movies/' . $film->id);
        $detail->assertStatus(200);
        $detail->assertSee($film->title);
    }
}
