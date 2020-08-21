<?php

namespace Tests\Feature\Controllers;

use App\Client;
use App\Pool;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_see_clients_not_included_in_pools()
    {
        $clientA = factory(Client::class)->create();
        $clientB = factory(Client::class)->create([
            'machine' => 'client_b_machine_id'
        ]);

        factory(Pool::class)->create([
            'client_id' => $clientA
        ]);

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->get('/api/clients');
        $data = json_decode((string) $response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals(2, $data[0]['id']);
        $this->assertEquals('client_b_machine_id', $data[0]['machine']);
        $this->assertDatabaseMissing('pools', [$clientB->id]);
    }
}
