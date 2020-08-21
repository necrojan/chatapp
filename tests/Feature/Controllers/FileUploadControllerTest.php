<?php

namespace Tests\Feature\Controllers;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_upload_an_image()
    {
        Storage::fake('local');

        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/api/file-upload', [
                'attachments' => [
                    UploadedFile::fake()->image('avatar.jpg')
                ]
            ]);

        $response->assertStatus(200);
        Storage::disk('local')->assertExists('public/messages/images/avatar.jpg');
    }

    /** @test */
    public function it_only_accepts_the_required_mime_types()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/api/file-upload', [
                'attachments' => [
                    'example'
                ]
            ]);

        $response->assertSessionHasErrors('attachments.0');
    }

    /** @test */
    public function it_should_have_a_valid_max_size()
    {
        $user = factory(User::class)->create();
        $user->assignRoleTitle('admin');

        $response = $this->actingAs($user)
            ->post('/api/file-upload', [
                'attachments' => [
                    UploadedFile::fake()->image('image.jpg')->size(26000)
                ]
            ]);

        $response->assertSessionHasErrors('attachments.0');
        $this->assertEquals(
            'The attachments.0 may not be greater than 25000 kilobytes.',
            session('errors')->getBag('default')->getMessages()['attachments.0'][0] // ugly
        );
    }
}
