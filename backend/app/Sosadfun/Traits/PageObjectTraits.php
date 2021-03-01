<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use Carbon;
use ConstantObjects;

trait PageObjectTraits{

    public function quotes()//在首页上显示的最新quotes
    {
        return Cache::remember('quotes', 5, function () {
            $new_quotes = \App\Models\Quote::with('author')
            ->where('approved', true)
            ->where('created_at','>',Carbon::now()->subDays(100))
            ->inRandomOrder()
            ->limit(config('preference.regular_quotes_on_homepage'))
            ->get();
            $old_quotes = \App\Models\Quote::with('author')
            ->where('approved', true)
            ->where('created_at','<',Carbon::now()->subDays(100))
            ->inRandomOrder()
            ->limit(config('preference.regular_quotes_on_homepage'))
            ->get();
            $helper_quotes = \App\Models\Quote::with('author')
            ->where('approved', true)
            ->notSad()
            ->inRandomOrder()
            ->limit(config('preference.helper_quotes_on_homepage'))
            ->get();
            return $new_quotes->merge($old_quotes)->merge($helper_quotes)
            ->shuffle();
        });
    }


    public function short_recommendations()
    {
        return Cache::remember('short_recommendations', 5, function () {
            $short_reviews = \App\Models\Post::join('post_infos','posts.id','=','post_infos.post_id')
            ->withType('review')
            ->withSummary('editorRec')
            ->inRandomOrder()
            ->take(config('preference.short_recommendations_on_homepage'))
            ->select('posts.*')
            ->get();
            $short_reviews->load('simpleInfo.reviewee.author');
            return $short_reviews;
        });
    }

    public function thread_recommendation()
    {
        return Cache::remember('thread_recommendation', 10, function () {
            $id = ConstantObjects::system_variable()->homepage_thread_id;
            if($id>0){
                return \App\Models\Thread::find($id);
            }
        });
    }

    public function channel_threads($channel_id)
    {
        return Cache::remember('channel_threads_ch'.$channel_id, 1, function () use($channel_id) {
            return \App\Models\Thread::isPublic()
            ->inChannel($channel_id)
            ->withBianyuan('none_bianyuan_only')
            ->with('author', 'tags')
            ->ordered('responded_at')
            ->take(config('preference.threads_per_channel'))
            ->get();
        });
    }

    public function pages_homeworks()
    {
        return Cache::remember('HomePageHomeworks', 15, function (){

            $active_homeworks = \App\Models\Homework::where('is_active',1)->with('registration_thread',  'summary_thread','registrations.owner')->orderBy('id','desc')->get();

            $past_homework_samples = \App\Models\Homework::where('is_active',0)
            ->with('registration_thread',  'summary_thread','registrations.owner')
            ->inRandomOrder()
            ->limit(config('preference.past_homeworks_on_homepage'))
            ->get();

            return $active_homeworks->merge($past_homework_samples);
        });
    }

}
