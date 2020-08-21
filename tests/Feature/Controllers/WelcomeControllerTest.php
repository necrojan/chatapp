<?php

namespace Tests\Feature\Controllers;

use App\Client;
use App\Events\NewPool;
use App\Pool;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_create_a_user_using_email()
    {
        $this->markTestIncomplete();
        $response = $this->post('/', [
            'email' => 'example@email.com',
            'recaptcha' => 'token'
        ]);

        $response->assertStatus(302);
        tap(User::latest('id')->first(), function ($user) {
            $this->assertEquals('example@email.com', $user->email);
        });
    }

    /** @test */
    public function it_should_have_a_client_role()
    {
        $this->markTestIncomplete();
        $this->post('/', [
            'email' => 'example@email.com'
        ]);

        tap(User::latest('id')->first(), function ($user) {
            $this->assertTrue($user->hasRole('client'));
        });
    }

    /** @test */
    public function it_redirect_to_home_on_successfull_login()
    {
        $this->markTestIncomplete();
        $response = $this->post('/', [
            'email' => 'example@email.com'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/chat');
    }

    /** @test */
    public function it_should_be_a_valid_email()
    {
        $response = $this->post('/', [
            'email' => 'invalid'
        ]);

        $response->assertSessionHasErrorsIn('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid'
        ]);
    }

    /** @test */
    public function it_should_have_a_full_name()
    {
        $response = $this->post('/', [
            'email' => 'example@email.com',
            'full_name' => null
        ]);

        $response->assertSessionHasErrorsIn('full_name');
    }

    /** @test */
    public function it_should_pass_an_email()
    {
        $response = $this->post('/', [
            'email' => null
        ]);

        $response->assertSessionHasErrorsIn('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid'
        ]);
    }

    /** @test */
    public function it_should_create_a_pool_entry_if_not_yet_found()
    {
        $this->markTestIncomplete();
        $response = $this->post('/', [
            'email' => 'hello@email.com'
        ]);

        $client = Client::first();

        $response->assertStatus(302);
        tap(Pool::latest('latest')->first(), function ($pool) use ($client) {
            $this->assertEquals($pool->client_id, $client->id);
        });
    }

    /** @test */
    public function it_sends_the_event_new_pool_if_client_not_in_pool_yet()
    {
        $this->markTestIncomplete();
        Event::fake();

        $response = $this->post('/', [
            'email' => 'hello@email.com'
        ]);

        $client = Client::first();

        $response->assertStatus(302);
        tap(Pool::latest('latest')->first(), function ($pool) use ($client) {
            $this->assertEquals($pool->client_id, $client->id);
        });
        Event::assertDispatched(
            NewPool::class
        );
    }
}
