<?php

namespace Tests\Feature\Controllers;

use App\Pin;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PinControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_list_of_saved_replies()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');
        factory(Pin::class, 2)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get('/api/pins');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }
    
    /** @test */
    public function user_can_pin_a_message()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/api/pins', [
                'text' => 'example pin'
            ]);

        $response->assertStatus(200);
        tap(Pin::latest('id')->first(), function ($pin) {
            $this->assertEquals('example pin', $pin->text);
        });
    }

    /** @test */
    public function user_can_remove_saved_pin()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $pin = factory(Pin::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('pins', [
            'id' => $pin->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->delete('/api/pins/' . $pin->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('pins', [
            'id' => $pin->id,
            'user_id' => $user->id
        ]);
    }
}
