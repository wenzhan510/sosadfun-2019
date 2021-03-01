<?php

use Illuminate\Database\Seeder;
use App\Models\Helpfaq;

class HelpfaqsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = factory(Helpfaq::class)->times(10)->create();
        
    }
}
