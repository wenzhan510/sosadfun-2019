<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserInfo;
use App\Models\User;
use APP\Models\Checkin;
use Carbon;
use Cache;

class CheckinTest extends TestCase
{
    // helper
    // why the method is needed:
    // checkin API has very strict throttle rate limit (1 request per min)
    // the throttle feature is based on Illuminate/Routing/Middleware/ThrottleRequests
    // which uses Illuminate\Cache\RateLimiter
    // the RateLimiter stores user's attemp count (the number of API requests) in cache and set it to expire in 1 min.
    // as the cache record will expire in 1 min, the request quote will reset after 1 min
    // however, when we use Carbon to modify system time, we did not clean up the cache
    // as a workaround, we manually clean up the cache
    private function clearThrottleCache($user, $prefix){
        $key = $prefix.sha1($user->id);
        // Reset the number of attempts for the given key.
        Cache::forget($key);
        Cache::forget($key.':timer');
        Cache::forget('checkin-user-'.$user->id);
        // error_log(cache($key).' |cache key| '.cache($key.':timer'));

    }

    /** @test */
    public function a_guest_can_not_check_in()
    {
        $this->get('api/qiandao')
            ->assertStatus(401);
        $this->get('api/qiandao/complement')
            ->assertStatus(401);
    }

    // rate limit
    /** @test */
    public function user_can_only_checkin_once_per_min()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $this->get('api/qiandao')
            ->assertStatus(200);
        $this->get('api/qiandao')
            ->assertStatus(429);    // too many request
    }

    /** @test */
    // user can check in
    public function user_can_checkin()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $response = $this->get('api/qiandao')
            ->assertStatus(200)
            ->assertJsonStructure(["code", "data" => ["type", "attributes" => ["levelup", "checkin_reward" => ["special_reward", "salt", "fish", "ham"]], "info"]]);

        $attributes = $response->decodeResponseJson() ["data"]["attributes"];
        $expected = ["special_reward" => false, "salt" => 5, "fish" => 1, "ham" => 0];
        $this->assertEquals($expected, $attributes["checkin_reward"]);
        $this->assertEquals(false, $attributes["levelup"]);

        // check if UserInfo is updated in db
        $info = UserInfo::find($user->id);
        $this->assertEquals(1, $info->qiandao_max);
        $this->assertEquals(1, $info->qiandao_continued);
        $this->assertEquals(1, $info->qiandao_all);
        $this->assertEquals(5, $info->salt);
        $this->assertEquals(1, $info->fish);
        $this->assertEquals(0, $info->ham);
    }

    /** @test */
    // user cannot check in twice in the same day
    public function user_can_not_checkin_twice()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        Carbon::setTestNow(Carbon::create(2020, 01, 10, 10));
        $response = $this->get('api/qiandao')
            ->assertStatus(200);

        $this->clearThrottleCache($user, 'checkin');
        Carbon::setTestNow(Carbon::create(2020, 01, 10, 11));
        $response = $this->get('api/qiandao')
            ->assertStatus(409);

        $this->clearThrottleCache($user, 'checkin');
        Carbon::setTestNow(Carbon::create(2020, 01, 10, 12));
        $response = $this->get('api/qiandao')
            ->assertStatus(409);

        $this->clearThrottleCache($user, 'checkin');
        Carbon::setTestNow(Carbon::create(2020, 01, 11, 10)); // a new day
        $response = $this->get('api/qiandao')
            ->assertStatus(200);

        $this->clearThrottleCache($user, 'checkin');
        Carbon::setTestNow();
    }

    /** @test */
    public function user_checkin_continousely_for_10_days()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // check in at 10am for 10 days
        for ($x = 1;$x <= 10;$x++)
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 0 + $x, 10));
            $response = $this->get('api/qiandao')
                ->assertStatus(200);

            $checkin_result = $response->decodeResponseJson() ["data"]["attributes"];
            $checkin_reward = $checkin_result["checkin_reward"];

            // check reward value
            if ($x != 10)
            {
                $expected = ["special_reward" => false, "salt" => 5, "fish" => 1, "ham" => 0];
                $this->assertEquals($expected, $checkin_reward);
            }
            else
            {
                // day 10
                $expected = ["special_reward" => true, "salt" => 15, "fish" => 3, "ham" => 0];
                $this->assertEquals($expected, $checkin_reward);
            }

            // check level up
            // on day 4 user should have 4*5 = 20 salt -> level 1
            if ($x == 4)
            {
                $this->assertEquals(true, $checkin_result["levelup"]);
            }

            $this->clearThrottleCache($user, 'checkin');
        }

        // check db
        // after 10 days, user should have
        // 5*9 + 15 = 60 salt,
        // 1*9 + 3 = 12 fish
        $info = UserInfo::find($user->id);
        $this->assertEquals(10, $info->qiandao_max);
        $this->assertEquals(10, $info->qiandao_continued);
        $this->assertEquals(10, $info->qiandao_all);
        $this->assertEquals(60, $info->salt);
        $this->assertEquals(12, $info->fish);
        $this->assertEquals(0, $info->ham);

        $u = User::find($user->id);
        $this->assertEquals(1, $u["level"]);

        $numberOfCheckinRecords = Checkin::where('user_id', $user->id)
            ->count();
        $this->assertEquals(10, $numberOfCheckinRecords);
        Carbon::setTestNow();
    }

    /** @test */
    public function user_can_complement_checkin()
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        // no checkin_reward
        $this->get('api/qiandao/complement')
            ->assertStatus(412);

        $user->info->qiandao_reward_limit = 3;
        $user->info->save();

        Carbon::setTestNow(Carbon::now()->addMinute());
        $this->clearThrottleCache($user, 'comp_checkin');
        // no break before
        $this->get('api/qiandao/complement')
            ->assertStatus(412);
        $this->clearThrottleCache($user, 'comp_checkin');

        // check in continously for 5 days
        for ($x = 1;$x <= 5;$x++)
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 0 + $x, 10));
            $this->get('api/qiandao')
                ->assertStatus(200);
            $this->clearThrottleCache($user, 'checkin');
        }
        // break for 3 days
        // check in again for 3 days
        for ($x = 1;$x <= 3;$x++)
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 8 + $x, 10));
            $response = $this->get('api/qiandao')
                ->assertStatus(200);
            $this->clearThrottleCache($user, 'checkin');

            $info = $response->decodeResponseJson() ["data"]["info"]["attributes"];
            $this->assertEquals($x, $info["qiandao_continued"]);
            $this->assertEquals($x + 5, $info["qiandao_all"]);
            $this->assertEquals(5 , $info["qiandao_max"]);
            $this->assertEquals(5, $info["qiandao_last"]);
        }

        // complement
        // Carbon::setTestNow(Carbon::create(2020, 01, 8 + 7, 10));
        $this->get('api/qiandao/complement')
            ->assertStatus(200);
        $this->clearThrottleCache($user, 'comp_checkin');
        Carbon::setTestNow(Carbon::create(2020, 01, 8 + 8, 10));
        $this->get('api/qiandao/complement')
            ->assertStatus(412);
        $this->clearThrottleCache($user, 'comp_checkin');

        // check db
        $info = UserInfo::find($user->id);
        $this->assertEquals(6, $info->qiandao_max);
        $this->assertEquals(6, $info->qiandao_continued);
        $this->assertEquals(8, $info->qiandao_all);
        $this->assertEquals(0, $info->qiandao_last);

        Carbon::setTestNow();
    }

    // user cannot complement checkin if qiandao_continued > qiandao_last
    /** @test */
    public function user_cannot_complement_checin_if_curr_dur_longer(){
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $user->info->qiandao_reward_limit = 3;
        $user->info->save();

        // check in continously for 3 days
        for ($x = 1;$x <= 3;$x++)
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 0 + $x, 10));
            $this->get('api/qiandao')
                ->assertStatus(200);
            $this->clearThrottleCache($user, 'checkin');
        }
        // break
        // check in again for 3 days
        for ($x = 1;$x <= 3;$x++)
        {
            Carbon::setTestNow(Carbon::create(2020, 01, 8 + $x, 10));
            $response = $this->get('api/qiandao')
                ->assertStatus(200);
            $this->clearThrottleCache($user, 'checkin');
        }

        // complement
        $this->get('api/qiandao/complement')
            ->assertStatus(412);
        $this->clearThrottleCache($user, 'comp_checkin');

        // check db
        $info = UserInfo::find($user->id);
        $this->assertEquals(3, $info->qiandao_max);
        $this->assertEquals(3, $info->qiandao_continued);
        $this->assertEquals(6, $info->qiandao_all);
        $this->assertEquals(3, $info->qiandao_last);

        Carbon::setTestNow();
    }
}
