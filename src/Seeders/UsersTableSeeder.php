<?php namespace Arcanedev\LaravelAuth\Seeders;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\Support\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class     UsersTableSeeder
 *
 * @package  Arcanedev\LaravelAuth\Seeders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UsersTableSeeder extends Seeder
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = $this->prepareUsers(config('laravel-auth.seeds.users', []));

        User::query()->insert($users);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
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
                'username'     => $user['username'],
                'first_name'   => Arr::get($user, 'first_name', null),
                'last_name'    => Arr::get($user, 'last_name', null),
                'email'        => $user['email'],
                'password'     => bcrypt($user['password']),
                'is_admin'     => true,
                'created_at'   => $now,
                'updated_at'   => $now,
                'activated_at' => $user['activated_at'] ?? $now,
            ];
        }

        return $users;
    }
}
