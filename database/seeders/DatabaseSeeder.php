<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Create 10 users
        User::factory()->count(5)->create();

        // $this->call([
        //     UtilitySeeder::class,
        //     EmailSMSTemplateSeeder::class,
        //     CurrencySeeder::class,
        // ]);

    }
}
