<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create corresponding user profile
        UserProfile::create([
            'user_id'      => $user->id,
            'first_name'   => 'Test',
            'last_name'    => 'User',
            'phone_number' => '+1 (555) 123-4567',
            'address1'     => '1000 5th Avenue',
            'address2'     => null,
            'city'         => 'New York',
            'state'        => 'NY',
            'country'      => 'United States',
            'postal_code'  => '10028',
        ]);

        // Uncomment to seed multiple users with profiles
        // User::factory(10)->create()->each(function (User $user) {
        //     UserProfile::create([
        //         'user_id' => $user->id,
        //         'first_name' => fake()->firstName(),
        //         'last_name' => fake()->lastName(),
        //         'phone_number' => fake()->phoneNumber(),
        //         'address1' => fake()->streetAddress(),
        //         'address2' => fake()->secondaryAddress(),
        //         'city' => fake()->city(),
        //         'state' => fake()->state(),
        //         'country' => 'United States',
        //         'postal_code' => fake()->postcode(),
        //     ]);
        // });
    }
}
