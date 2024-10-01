<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuccessfulEmail;
use Faker\Factory as Faker;

class SuccessfulEmailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            SuccessfulEmail::create([
                'affiliate_id' => $faker->numberBetween(1, 100),
                'envelope' => json_encode(['to' => $faker->email, 'from' => $faker->email]),
                'from' => $faker->email,
                'subject' => $faker->sentence,
                'dkim' => $faker->randomElement(['pass', 'fail', null]),
                'SPF' => $faker->randomElement(['pass', 'fail', null]),
                'spam_score' => $faker->randomFloat(2, 0, 10),
                'email' => $faker->randomHtml(),
                'raw_text' => '',
                'sender_ip' => $faker->ipv4,
                'to' => $faker->email,
                'timestamp' => $faker->unixTime,
            ]);
        }
    }
}
