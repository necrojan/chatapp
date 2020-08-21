<?php

namespace Tests\Feature\Controllers;

use App\CannedResponse;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CannedResponseTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_see_list_of_canned_responses()
    {
        factory(CannedResponse::class, 5)->create();
        $user = factory(User::class)->create(['name' => 'example']);
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->get('/responses');

        $response->assertStatus(200);
    }

    /** @test */
    public function a_user_admin_can_create_canned_response()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams());

        $response->assertStatus(302);
        tap(CannedResponse::latest('id')->first(), function ($cannedResponse) {
            $this->assertEquals('examplekey', $cannedResponse->key);
            $this->assertEquals('example message', $cannedResponse->message);
        });
    }

    /** @test */
    public function a_user_cannot_create_canned_response_if_not_admin()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('client');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams());

        $response->assertStatus(401);
        $this->assertDatabaseMissing('canned_responses', [
            'key' => 'examplekey',
            'message' => 'example message'
        ]);
    }

    /** @test */
    public function it_should_have_a_canned_response_key()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams([
                'key' => null
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('key');
    }

    /** @test */
    public function it_should_have_a_unique_canned_response_key()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        factory(CannedResponse::class)->create([
            'key' => 'examplekey'
        ]);

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams());

        $errors = session('errors');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('key');
        $this->assertEquals($errors->get('key')[0], 'The key has already been taken.');
    }

    /** @test */
    public function it_should_max_of_255_characters_for_canned_response_key()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams([
                'key' => str_repeat('a', 256)
            ]));

        $errors = session('errors');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('key');
        $this->assertEquals($errors->get('key')[0], 'The key may not be greater than 255 characters.');
    }

    /** @test */
    public function it_should_have_a_canned_response_message()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams([
                'message' => null
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('message');
    }

    /** @test */
    public function it_should_max_of_255_characters_for_canned_response_message()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/responses', $this->validParams([
                'message' => str_repeat('a', 256)
            ]));

        $errors = session('errors');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('message');
        $this->assertEquals($errors->get('message')[0], 'The message may not be greater than 255 characters.');
    }

    /** @test */
    public function a_user_can_update_a_canned_response()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $cannedResponse = factory(CannedResponse::class)->create([
            'user_id' => $user->id,
            'key' => 'keyA',
            'message' => 'messageA'
        ]);

        $response = $this->actingAs($user)
            ->put("/responses/$cannedResponse->id", [
                'key' => 'keyB',
                'message' => 'messageB'
            ]);

        $response->assertStatus(302);
        tap(CannedResponse::latest('id')->first(), function ($cannedResponse) {
            $this->assertEquals('keyB', $cannedResponse->key);
            $this->assertEquals('messageB', $cannedResponse->message);
        });
    }

    /** @test */
    public function a_user_canned_response_update_key_is_required()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $cannedResponse = factory(CannedResponse::class)->create([
            'user_id' => $user->id,
            'key' => 'keyA',
            'message' => 'messageA'
        ]);

        $response = $this->actingAs($user)
            ->put("/responses/$cannedResponse->id", $this->validParams([
                'key' => null
            ]));

        $errors = session('errors');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('key');
        $this->assertEquals($errors->get('key')[0], 'The key field is required.');

        tap(CannedResponse::latest('id')->first(), function($cannedResponse) {
            $this->assertEquals('keyA', $cannedResponse->key);
            $this->assertEquals('messageA', $cannedResponse->message);
        });
    }

    /** @test */
    public function a_user_canned_response_update_message_is_required()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $cannedResponse = factory(CannedResponse::class)->create([
            'user_id' => $user->id,
            'key' => 'keyA',
            'message' => 'messageA'
        ]);

        $response = $this->actingAs($user)
            ->put("/responses/$cannedResponse->id", $this->validParams([
                'message' => null
            ]));

        $errors = session('errors');

        $response->assertStatus(302);
        $response->assertSessionHasErrors('message');
        $this->assertEquals($errors->get('message')[0], 'The message field is required.');

        tap(CannedResponse::latest('id')->first(), function($cannedResponse) {
            $this->assertEquals('keyA', $cannedResponse->key);
            $this->assertEquals('messageA', $cannedResponse->message);
        });
    }

    /** @test */
    public function an_admin_user_can_delete_canned_response()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $cannedResponse = factory(CannedResponse::class)->create([
            'user_id' => $user->id,
            'key' => 'keyA',
            'message' => 'messageA'
        ]);

        $response = $this->actingAs($user)
            ->delete("/responses/$cannedResponse->id");


        $response->assertStatus(302);
        $this->assertDatabaseMissing('canned_responses', [
            'key' => 'keyA',
            'messageA' => 'messageA'
        ]);
    }

    /** @test */
    public function a_user_with_no_admin_role_cannot_delete_canned_response()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('client');

        $cannedResponse = factory(CannedResponse::class)->create([
            'user_id' => $user->id,
            'key' => 'keyA',
            'message' => 'messageA'
        ]);

        $response = $this->actingAs($user)
            ->delete("/responses/$cannedResponse->id");

        $response->assertStatus(401);
        $this->assertDatabaseHas('canned_responses', [
            'key' => 'keyA',
            'messageA' => 'messageA'
        ]);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'key' => 'examplekey',
            'message' => 'example message',
            'is_personal' => false
        ], $overrides);
    }
}
