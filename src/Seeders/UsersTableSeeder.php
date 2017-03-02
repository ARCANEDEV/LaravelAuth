<?php namespace Arcanedev\LaravelAuth\Seeders;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\Support\Bases\Seeder;
use Carbon\Carbon;

/**
 * Class     UsersTableSeeder
 *
 * @package  Arcanedev\LaravelAuth\Seeders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = $this->prepareUsers(config('laravel-auth.seeds.users', []));

        User::insert($users);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Prepare users.
     *
     * @param  array  $data
     *
     * @return array
     */
    private function prepareUsers(array $data)
    {
        $users = [];
        $now   = Carbon::now();

        foreach ($data as $user) {
            $users[] = [
                'username'   => $user['username'],
                'first_name' => array_get($user, 'first_name', null),
                'last_name'  => array_get($user, 'last_name', null),
                'email'      => $user['email'],
                'password'   => bcrypt($user['password']),
                'is_admin'   => true,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $users;
    }
}
