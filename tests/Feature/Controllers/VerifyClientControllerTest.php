<?php

namespace Tests\Feature\Controllers;

use App\Client;
use App\Events\ClientVerification;
use App\Events\ClientVerified;
use App\Notifications\VerifyClient;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerifyClientControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_agent_can_send_verification_email()
    {
        Notification::fake();
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $userClient = factory(User::class)->create();
        factory(Client::class)->create(['user_id' => $userClient->id]);

        $response = $this->actingAs($user)
            ->get('/verify/'.$userClient->id);

        $response->assertStatus(200);
        $response->assertSee('success');

        Notification::assertSentTo(
            $userClient,
            VerifyClient::class
        );

        Event::assertDispatched(
            ClientVerification::class
        );
    }

    /** @test */
    public function it_generates_a_random_code_when_sending()
    {
        Notification::fake();
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $userClient = factory(User::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $userClient->id
        ]);

        $response = $this->actingAs($user)
            ->get('/verify/'.$userClient->id);

        $response->assertStatus(200);
        tap(Client::latest('id')->first(), function ($c) use ($client) {
            $this->assertNotEquals($client->code, $c->code);
        });
    }

    /** @test */
    public function it_can_update_verification_status_if_code_is_correct()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $userClient = factory(User::class)->create();
        factory(Client::class)->create([
            'user_id' => $userClient->id,
            'code' => 1234,
            'is_verified' => false
        ]);

        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(0, $client->is_verified);
        });

        $response = $this->actingAs($user)
            ->post('/verify/'.$userClient->id, [
                'code' => 1234
            ]);

        $response->assertStatus(200);
        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(1, $client->is_verified);
        });
    }

    /** @test */
    public function it_sends_an_event_upon_updating_the_verified_status()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $userClient = factory(User::class)->create();
        factory(Client::class)->create([
            'user_id' => $userClient->id,
            'code' => 1234,
            'is_verified' => false
        ]);

        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(0, $client->is_verified);
        });

        $response = $this->actingAs($user)
            ->post('/verify/'.$userClient->id, [
                'code' => 1234
            ]);

        Event::assertDispatched(ClientVerified::class);

        $response->assertStatus(200);
        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(1, $client->is_verified);
        });
    }

    /** @test */
    public function it_wont_update_verification_status_if_code_is_incorrect()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $userClient = factory(User::class)->create();
        factory(Client::class)->create([
            'user_id' => $userClient->id,
            'code' => 1234,
            'is_verified' => false
        ]);

        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(0, $client->is_verified);
        });

        $response = $this->actingAs($user)
            ->post('/verify/'.$userClient->id, [
                'code' => 'this-is-wrong'
            ]);

        $response->assertStatus(200);
        tap(Client::latest('id')->first(), function ($client) {
            $this->assertEquals(0, $client->is_verified);
        });
    }

    /** @test */
    public function client_cannot_send_verification_email()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $userClient = factory(User::class)->create();
        factory(Client::class)->create(['user_id' => $userClient->id]);

        $response = $this->actingAs($user)
            ->get('/verify/'.$userClient->id);

        $response->assertStatus(401);
    }
}
