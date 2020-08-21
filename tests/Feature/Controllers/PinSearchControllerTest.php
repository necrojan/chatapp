<?php

namespace Tests\Feature\Controllers;

use App\CannedResponse;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PinSearchControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_should_display_a_suggestion_list_to_admin_users()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $cannedResponse = factory(CannedResponse::class)->create([
            'key' => 'examplekey',
            'message' => 'example response',
            'is_personal' => false,
        ]);

        $response = $this->actingAs($user)
            ->json('GET', '/search', [
                'search' => 'ex'
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'key' => $cannedResponse->key,
            'message' => $cannedResponse->message,
        ]);
    }

    /** @test */
    public function it_should_see_no_respose_list_if_words_doesnt_match()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        factory(CannedResponse::class)->create([
            'key' => 'examplekey',
            'message' => 'example response'
        ]);

        $response = $this->actingAs($user)
            ->json('GET', '/search', [
                'search' => 'abc'
            ]);

        $response->assertJsonCount(0);
        $response->assertDontSee('example response');
    }

    /** @test */
    public function it_should_only_see_its_own_personal_response_on_suggestion_list()
    {
        $userA = factory(User::class)->create();
        $userA->assignRoleTitle('admin');

        $userB = factory(User::class)->create();
        $userB->assignRoleTitle('admin');

        $cannedResponseA = factory(CannedResponse::class)->create([
            'user_id' => $userA->id,
            'key' => 'example_should_see',
            'message' => 'example response should see',
            'is_personal' => 1
        ]);

        $cannedResponseB = factory(CannedResponse::class)->create([
            'user_id' => $userB->id,
            'key' => 'example_should_not_see',
            'message' => 'example response should not see',
            'is_personal' => 1
        ]);

        $response = $this->actingAs($userA)
            ->json('GET', '/search', [
                'search' => 'example response should see'
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'key' => $cannedResponseA->key,
            'message' => $cannedResponseA->message,
        ]);
        $response->assertDontSee($cannedResponseB->message);
    }
}
