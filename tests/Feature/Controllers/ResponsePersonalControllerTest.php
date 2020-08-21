<?php

namespace Tests\Feature\Controllers;

use App\CannedResponse;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ResponsePersonalControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_should_return_a_list_of_personal_responses()
    {
        $user = factory(User::class)->create([
            'name' => 'exampleA'
        ]);
        $user->assignRoleTitle('admin');

        factory(CannedResponse::class, 5)->create([
            'user_id' => $user->id,
            'is_personal' => true
        ]);

        $response = $this->actingAs($user)
            ->get('/api/personal-responses');

        $body = json_decode((string) $response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals(5, count($body));
    }

    /** @test */
    public function a_user_can_only_see_their_own_personal_response()
    {
        $userA = factory(User::class)->create([
            'name' => 'exampleA'
        ]);
        $userA->assignRoleTitle('admin');
        $userB = factory(User::class)->create([
            'name' => 'exampleB'
        ]);
        $userB->assignRoleTitle('admin');

        factory(CannedResponse::class, 5)->create([
            'user_id' => $userA->id,
            'is_personal' => true
        ]);

        $response = $this->actingAs($userB)
            ->get('/api/personal-responses');

        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
