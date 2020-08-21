<?php

namespace Tests\Feature\Controllers;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_displays_user_list()
    {
        factory(User::class, 5)->create();
        $admin = factory(User::class)->create();
        $admin->assignRoleTitle('admin');

        $response = $this->actingAs($admin)
            ->get('/api/users');
        
        $response->assertStatus(200);
        $response->assertJsonCount(6);
    }
}
