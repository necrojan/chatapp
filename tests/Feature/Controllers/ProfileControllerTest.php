<?php

namespace Tests\Feature\Controllers;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_should_see_the_user_profile_page()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile.show');
    }

    /** @test */
    public function it_should_not_see_profile_page_if_not_admin_or_agent()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('client');

        $response = $this->actingAs($user)
            ->get('/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_update_user_profile()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'password' => 'password'
            ]);

        $response->assertStatus(302);
        tap(User::latest('id')->first(), function ($user) {
            $this->assertEquals('example name', $user->name);
            $this->assertEquals('example@email.com', $user->email);
        });
    }

    /** @test */
    public function it_can_update_upload_a_profile_image()
    {
        Storage::fake('local');

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'password' => 'password',
                'photo' => UploadedFile::fake()->image('image1.png')
            ]);

        $response->assertStatus(302);
        tap(User::latest('id')->first(), function ($user) {
            $this->assertEquals('example name', $user->name);
            $this->assertEquals('example@email.com', $user->email);
            $this->assertEquals('image1.png', $user->photo);
        });

        Storage::disk('local')->assertExists('public/images/image1.png');
    }

    /** @test */
    public function it_should_have_a_name_on_update()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => null,
                'email' => 'example@email.com',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_should_have_a_name_max_255()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => str_repeat('a', 256),
                'email' => 'example@email.com',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_should_have_an_email()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => null,
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_should_have_a_valid_email()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'notanemail',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_can_should_have_a_valid_mime_type()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'photo' => UploadedFile::fake()->create('document.pdf', 300)
            ]);

        $response->assertSessionHasErrors('photo');
    }

    /** @test */
    public function it_can_should_have_a_valid_size_for_mime()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'photo' => UploadedFile::fake()->create('image.png', 2049)
            ]);

        $response->assertSessionHasErrors('photo');
    }

    /** @test */
    public function it_can_should_update_profile_even_without_adding_an_image()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'photo' => null
            ]);

        $response->assertStatus(302);
        tap(User::latest('id')->first(), function ($user) {
            $this->assertEquals('example name', $user->name);
            $this->assertEquals(null, $user->photo);
        });
    }

    /** @test */
    public function it_should_have_a_minimum_value_of_6_for_password()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'password' => 'abc'
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_should_have_a_string_of_password()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->put("/profile/{$user->id}", [
                'name' => 'example name',
                'email' => 'example@email.com',
                'password' => false
            ]);

        $response->assertSessionHasErrors('password');
    }
}
