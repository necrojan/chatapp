<?php

namespace Tests\Feature\Controllers;

use App\Client;
use App\Events\AddToQueue;
use App\Events\RemovePool;
use App\Events\RestorePool;
use App\Pool;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PoolControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_see_list_of_users_on_pool()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $users = factory(User::class, 5)->create();
        $users->each(function ($user, $key) {
            $user->assignRoleTitle('client');
            $client = $user->client()->firstOrCreate(['machine_id' => str_replace(' ', '_', $user->name)]);
            Pool::firstOrCreate(['client_id' => $client->id]);
        });

        $response = $this->actingAs($user)
            ->get('/api/pools');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    /** @test */
    public function it_can_delete_users_on_pool_list()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');
        $client = factory(Client::class)->create();
        factory(Pool::class)->create(['client_id' => $client->id]);

        $response = $this->actingAs($user)
            ->post('/api/pool/' . $client->id, ['userId' => $user->id]);

        $response->assertStatus(200);
        tap(Pool::latest('id')->first(), function ($pool) {
            $this->assertSoftDeleted($pool);
        });
    }

    /** @test */
    public function it_fires_event_on_delete()
    {
        Event::fake();
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');
        $client = factory(Client::class)->create();
        factory(Pool::class)->create(['client_id' => $client->id]);

        $response = $this->actingAs($user)
            ->post('/api/pool/' . $client->id, ['userId' => $user->id]);

        Event::assertDispatched(RemovePool::class);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_cannot_delete_if_does_not_have_admin_role()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create();
        factory(Pool::class)->create(['client_id' => $client->id]);

        $response = $this->actingAs($user)
            ->post('/api/pool/' . $client->id);

        $response->assertStatus(401);
        $this->assertDatabaseHas('pools', ['client_id' => $client->id]);
    }

    /** @test */
    public function it_returns_error_if_not_found()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $client = factory(Client::class)->create();
        factory(Pool::class)->create(['client_id' => $client->id]);

        $response = $this->actingAs($user)
            ->post('/api/pool/' . 1234567);

        $response->assertSee('error');
    }

    /** @test */
    public function it_can_add_user_back_to_pool()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $client = factory(Client::class)->create();
        factory(Pool::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->post('/api/pool/' . $client->id);

        $this->assertSoftDeleted('pools');

        $response = $this->actingAs($user)
            ->post('/api/pool/' . $client->id . '/add');

        $response->assertStatus(200);
        $response->assertSee('restored');
        tap(Pool::latest('id')->first(), function ($pool) {
            $this->assertEquals($pool->deleted_at, null);
        });
    }

    /** @test */
    public function it_fires_event_when_adding_back_to_pool_list()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $client = factory(Client::class)->create();
        $poolA = factory(Pool::class)->create(['client_id' => $client->id]);

        $this->actingAs($user)
            ->post('/api/pool/' . $client->id);

        $this->assertSoftDeleted('pools');

        $this->actingAs($user)
            ->post('/api/pool/' . $client->id . '/add');

        Event::assertDispatched(RestorePool::class, function ($e) use ($poolA) {
            return $e->client->id === $poolA->id;
        });
    }
}
