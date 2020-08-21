<?php

namespace Tests\Feature\Controllers;

use App\Events\NewMessage;
use App\Message;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_store_a_message()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $response = $this->actingAs($userA)
            ->post('/api/private', [
                'user_id' => $userA,
                'receiver_id' => $userB->id,
                'message' => 'example message',
            ]);
        $data = json_decode((string)$response->getContent(), true)['message'];

        $response->assertStatus(200);
        tap(Message::latest('id')->first(), function ($message) use ($data) {
            $this->assertEquals('example message', $message->message);
            $this->assertEquals($data['receiver_id'], $message->receiver_id);
        });
    }

    /** @test */
    public function it_can_dispatch_an_event_on_when_sending_message()
    {
        Event::fake();

        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $response = $this->actingAs($userA)
            ->post('/api/private', [
                'user_id' => $userA,
                'receiver_id' => $userB->id,
                'message' => 'example message',
            ]);

        Event::assertDispatched(NewMessage::class);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_return_a_list_of_message()
    {
        $userA = factory(User::class)->create([
            'name' => 'userA',
        ]);
        $userB = factory(User::class)->create();;
        factory(Message::class)->create([
            'user_id' => $userA->id,
            'receiver_id' => $userB->id,
            'message' => 'example message',
            'is_archive' => false
        ]);

        $response = $this->actingAs($userA)
            ->get('/api/private/'.$userB->id);

        $data = json_decode((string)$response->getContent(), true);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'receiver_id' => $data[0]['receiver_id'],
            'message' => 'example message',
            'name' => $data[0]['user']['name'],
        ]);
    }

    /** @test */
    public function it_returns_error_if_message_input_is_empty()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $response = $this->actingAs($userA)
            ->post('/api/private', [
                'user_id' => $userA,
                'receiver_id' => $userB->id,
                'message' => '',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('message');
    }
}
