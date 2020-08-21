<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->create('admin@admin.com ', 'Admin', 'admin', 'admin');
        $this->create('another@admin.com', 'Another', 'another', 'admin');

//        $users = factory(User::class, 100)->create();
//        $users->each(function($user) {
//            $user->assignRoleTitle('agent');
//        });
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $userName
     * @param string $role
     */
    private function create(string $email, string $name, string $userName, string $role)
    {
        $user = User::firstOrNew(['email' => $email]);
        if (!$user->exists) {
            $user->fill([
                'name' => $name,
                'username' => $userName,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRoleTitle($role);
        }

        $user->save();
    }
}
