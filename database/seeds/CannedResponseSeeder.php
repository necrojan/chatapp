<?php

use App\User;
use Illuminate\Database\Seeder;

class CannedResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);

        for ($i = 1; $i <= 10; $i++) {
            factory(\App\CannedResponse::class)->create(['user_id' => $user->id]);
        }
    }
}
