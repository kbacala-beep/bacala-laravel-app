<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Faker\Factory as Faker;

class PopulateUserPhoneAndAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:populate-profile-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate phone and address fields for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faker = Faker::create();
        $count = 0;

        // Update users without phone
        User::whereNull('phone')->each(function ($user) use ($faker, &$count) {
            $user->update(['phone' => $faker->phoneNumber()]);
            $count++;
        });

        // Update users without address
        User::whereNull('address')->each(function ($user) use ($faker, &$count) {
            $user->update(['address' => $faker->address()]);
            $count++;
        });

        if ($count === 0) {
            $this->info('All users already have phone and address populated.');
            return 0;
        }

        $this->info('✓ Successfully populated phone and address fields for users.');

        return 0;
    }
}
