<?php

namespace Tests\Feature\Controllers;

use App\Message;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_should_mark_is_archive_to_true_when_user_log_out()
    {
        $userA= factory(User::class)->create();
        $userA->assignRoleTitle('admin');

        $userB = factory(User::class)->create();
        $userB->assignRoleTitle('client');

        factory(Message::class)->create([
            'user_id' => $userA,
            'receiver_id' => $userB,
            'is_archive' => false
        ]);

        $this->actingAs($userB)
            ->post('/logout');

        tap(Message::latest('id')->first(), function ($message) {
            $this->assertEquals(true, $message->is_archive);
        });
    }

    /** @test */
    public function it_should_not_affect_other_client_when_log_out()
    {
        $admin= factory(User::class)->create();
        $admin->assignRoleTitle('admin');

        $clientA = factory(User::class)->create();
        $clientA->assignRoleTitle('client');

        $clientB = factory(User::class)->create();
        $clientB->assignRoleTitle('client');

        $messageB = factory(Message::class)->create([
            'user_id' => $admin,
            'receiver_id' => $clientB,
            'is_archive' => false
        ]);

        $messageA = factory(Message::class)->create([
            'user_id' => $admin,
            'receiver_id' => $clientA,
            'is_archive' => false
        ]);


        $this->actingAs($clientA)
            ->post('/logout');

        tap(Message::latest('id')->first(), function ($messageA) {
            $this->assertEquals(true, $messageA->is_archive);
        });
        $this->assertFalse($messageB->is_archive);
    }
}
