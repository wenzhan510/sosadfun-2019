<?php

use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\Vote;
use App\Models\Reward;


class QuotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quotes1 = factory(Quote::class)->times(20)->create();
        $quotes1->each(function ($quote) {
            // seed votes
            $votes = factory(Vote::class)->create([
                'receiver_id' => $quote->user_id,
                'votable_id' => $quote->id,
            ]);
            // seed rewards
            $votes = factory(Reward::class)->create([
                'receiver_id' => $quote->user_id,
                'rewardable_id' => $quote->id,
                'rewardable_type' => 'quote'
            ]);
        });
        $quotes2 = factory(Quote::class)->times(20)->create([
            'approved' => false,
        ]);
    }
}
