<?php

namespace Tests\Feature;

use App\Http\Resources\QuizOptionResource;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\RegistrationApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Sosadfun\Traits\QuizObjectTraits;

class RegistrationByInvitationEmailTest extends TestCase
{
    use QuizObjectTraits;
    /** @test */
    public function registration_by_invitation_email_submit_email()
    {
        $data = [
            'email' => 'hahahahaha'
        ];
        // 邮箱格式不符合的时候，不允许注册
        Artisan::call('cache:clear');
        $this->post('api/register/by_invitation_email/submit_email', $data)
            ->assertStatus(422);

        // qq邮箱不允许注册
        $data['email'] = 'tester@qq.com';
        Artisan::call('cache:clear');
        $this->post('api/register/by_invitation_email/submit_email', $data)
            ->assertStatus(422);

        // .con 报错
        $data['email'] = 'tester@tester.con';
        Artisan::call('cache:clear');
        $this->post('api/register/by_invitation_email/submit_email', $data)
            ->assertStatus(422);

        // 验证返回题目和格式
        $data['email'] = $this->faker->email;
        Artisan::call('cache:clear');
        $response = $this->post('api/register/by_invitation_email/submit_email', $data);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'data' => [
                    'registration_application' => [
                        'id',
                        'type',
                        'attributes' => [
                            'email',
                            'has_quizzed',
                            'email_verified_at',
                            'submitted_at',
                            'is_passed',
                            'last_invited_at',
                            'is_in_cooldown'
                        ]
                    ],
                    'quizzes' => [
                        '*' => [
                            'id',
                            'type',
                            'attributes' => [
                                'body',
                                'hint',
                                'options' => [
                                    '*' => [
                                        'type',
                                        'id',
                                        'attributes'
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ]);

        // 验证是否取到了正确的题目
        $response=$response->decodeResponseJson();
        $id = $response["data"]["registration_application"]["id"];
        $quizzes_questions = RegistrationApplication::find($id)->quiz_questions;
        $returned_quizzes_questions = [];
        $this->assertCount(config('constants.registration_quiz_total'),$response["data"]["quizzes"]);
        foreach ($response["data"]["quizzes"] as $quiz) {
            $returned_quizzes_questions[] = $quiz["id"];
            $this->assertDatabaseHas('quizzes', ["id" => $quiz["id"]]);
            // 这里是验证是否所有选项都被选出来了
            $options_from_database = QuizOption::where('quiz_id',$quiz['id'])->orderBy('id')->pluck('id')->toArray();
            $options_from_returned = [];
            foreach ($quiz["attributes"]["options"] as $option) {
                $options_from_returned[] = $option['id'];
            }
            $this->assertEquals($options_from_database,$options_from_returned);
        }
        $this->assertEquals($quizzes_questions,implode(",",$returned_quizzes_questions));

        // 验证禁止频繁访问
        /*FIXME: uncomment
        $response = $this->post('api/register/by_invitation_email/submit_email', $data)
            ->assertStatus(498);*/
    }

    /** @test */
    public function registration_by_invitation_email_resend_verification_email()
    {
        // 拒绝申请记录不存在的邮箱
        Artisan::call('cache:clear');
        $email_address = 'null@null.com';
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(404);

        // 拒绝未完成前序步骤的邮箱
        Artisan::call('cache:clear');
        $regapp = factory('App\Models\RegistrationApplication')->create();
        $email_address = $regapp->email;
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(411);

        // 拒绝短时间内重复要求重发验证码的
        Artisan::call('cache:clear');
        $regapp->update([
            'has_quizzed' => true,
            'send_verification_at' => Carbon::now()
        ]);
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(410);

        // 拒绝已经验证过邮箱了的
        Artisan::call('cache:clear');
        $regapp->update(['email_verified_at' => Carbon::now()]);
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(409);

        // 成功发送
        Artisan::call('cache:clear');
        $regapp->update([
            'email_verified_at' => null,
            'send_verification_at' => null
        ]);
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(200)->assertExactJson([
                "code" => 200,
                "data" => [
                    "email" => $email_address
                ]
            ]);

        // 验证禁止频繁访问
        /* FIXME: uncomment
        $this->get('api/register/by_invitation_email/resend_email_verification?email='.$email_address)
            ->assertStatus(498);*/
    }

    /** @test */
    public function registration_by_invitation_email_resend_invitation_email()
    {
        // 拒绝申请记录不存在的邮箱
        Artisan::call('cache:clear');
        $email_address = 'null@null.com';
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(404);

        // 拒绝未完成前序步骤的邮箱
        Artisan::call('cache:clear');
        $regapp = factory('App\Models\RegistrationApplication')->create();
        $email_address = $regapp->email;
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(411);

        // 成功发送
        Artisan::call('cache:clear');
        $regapp->update([
            'has_quizzed' => true,
            'is_passed' => true
        ]);
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(200)->assertExactJson([
                "code" => 200,
                "data" => [
                    "email" => $email_address
                ]
            ]);

        // 拒绝短时间内重复要求重发验证码的
        Artisan::call('cache:clear');
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(409);

        // 拒绝已经成功通过点击邀请链接注册了的
        Artisan::call('cache:clear');
        $regapp->update(['user_id' => $this->faker->numberBetween($min = 10000, $max = 99999)]); // 产生一个随机用户id
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(409);

        // 验证禁止频繁访问
        /*FIXME: uncomment
        $this->get('api/register/by_invitation_email/resend_invitation_email?email='.$email_address)
            ->assertStatus(498);*/
    }

    /** @test */
    public function registration_by_invitation_submit_email_confirmation_token()
    {
        // 拒绝缺少token的请求
        Artisan::call('cache:clear');
        $data['email'] = 'null@null.com';
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(422);

        // 拒绝申请记录不存在的邮箱
        Artisan::call('cache:clear');
        $data['email'] = 'null@null.com';
        $data['token'] = 'NotAValidToken';
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(404);

        // 拒绝未完成前序步骤的邮箱
        Artisan::call('cache:clear');
        $regapp = factory('App\Models\RegistrationApplication')->create();
        $data['email'] = $regapp->email;
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(411);

        // 拒绝被拉黑的邮箱/申请
        Artisan::call('cache:clear');
        $regapp->update([
            'is_forbidden' => true
        ]);
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(499);

        // 拒绝错误的验证码
        Artisan::call('cache:clear');
        $regapp->update([
            'is_forbidden' => false,
            'has_quizzed' => true
        ]);
        $data['token'] = 'NotAValidToken';
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(422);

        // 成功验证
        Artisan::call('cache:clear');
        $data['token'] = $regapp->email_token;
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(200)->assertExactJson([
                "code" => 200,
                "data" => [
                    "email" => $data['email']
                ]
            ]);

        // 验证数据库是否已被更新
        $regapp = RegistrationApplication::where('email',$data['email'])->first();
        $this->assertNotNull($regapp->email_verified_at);
        $this->assertNotNull($regapp->ip_address_verify_email);

        // 拒绝已经验证过的
        Artisan::call('cache:clear');
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(409);

        // 验证禁止频繁访问
        /*FIXME: uncomment
        $this->post('api/register/by_invitation_email/submit_email_confirmation_token', $data)
            ->assertStatus(498);*/
    }

    /** @test */
    public function registration_by_invitation_submit_essay()
    {
        // 拒绝缺少body或essay_id的请求
        Artisan::call('cache:clear');
        $data['email'] = 'null@null.com';
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(422);

        // 拒绝申请记录不存在的邮箱
        Artisan::call('cache:clear');
        $data['email'] = 'null@null.com';
        $body='';
        for($i=500;$i>0;$i--){
            $character = $this->faker->randomLetter;
            $body.=$character;
        }
        $data['body'] = $body;
        $data['essay_id'] = -1;
        $response = $this->post('api/register/by_invitation_email/submit_essay', $data)
        ->assertStatus(404);

        // 拒绝未完成前序步骤的邮箱
        Artisan::call('cache:clear');
        $regapp = factory('App\Models\RegistrationApplication')->create();
        $data['email'] = $regapp->email;
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(411);

        // 拒绝被拉黑的邮箱/申请
        Artisan::call('cache:clear');
        $regapp->update([
            'is_forbidden' => true
        ]);
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(499);

        // 拒绝已经通过的申请
        Artisan::call('cache:clear');
        $regapp->update([
            'is_forbidden' => false,
            'is_passed' => true
        ]);
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(409);

        // 拒绝错误的小论文题目
        Artisan::call('cache:clear');
        $regapp->update([
            'is_passed' => false,
            'has_quizzed' => true,
            'email_verified_at' => "2020-01-30 21:23:11"
        ]);
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(444);

        // 拒绝上一次提交日期距今还在缓冲期的申请
        Artisan::call('cache:clear');
        $regapp->update([
            'submitted_at' => Carbon::now(),
        ]);
        $data['token'] = 'NotAValidToken';
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(409);

        // 成功提交
        Artisan::call('cache:clear');
        $regapp->update([
            'essay_question_id' => 15,
            'submitted_at' => null,
        ]);
        $data['essay_id'] = 15;
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(200)->assertJsonStructure([
                "code",
                "data" => [
                    "registration_application" => [
                        "id",
                        "type",
                        "attributes" => [
                            "email",
                            "has_quizzed",
                            "email_verified_at",
                            "submitted_at",
                            "is_passed",
                            "last_invited_at",
                            "is_in_cooldown"
                        ]
                    ]
                ]
            ]);

        // 验证数据库是否已被更新
        $regapp = RegistrationApplication::where('email',$data['email'])->first();
        $this->assertEquals($body,$regapp->body);
        $this->assertNotNull($regapp->submitted_at);
        $this->assertEquals(0,$regapp->reviewer_id);
        $this->assertNull($regapp->reviewed_at);
        $this->assertEquals(1,$regapp->submission_count);
        $this->assertNotNull($regapp->ip_address_submit_essay);

        // 验证禁止频繁访问
        /*FIXME: uncomment
        $this->post('api/register/by_invitation_email/submit_essay', $data)
            ->assertStatus(498);*/
    }

    /** @test */
    public function registration_by_invitation_submit_quiz()
    {
        // 拒绝申请记录不存在的邮箱
        Artisan::call('cache:clear');
        $data['email'] = 'null@null.com';
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(404);

        // 拒绝被拉黑的邮箱/申请
        Artisan::call('cache:clear');
        $regapp = factory('App\Models\RegistrationApplication')->create();
        $data['email'] = $regapp->email;
        $regapp->update([
            'is_forbidden' => true
        ]);
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(499);

        // 拒绝已经通过的申请
        Artisan::call('cache:clear');
        $regapp->update([
            'is_forbidden' => false,
            'is_passed' => true
        ]);
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(409);

        // 拒绝缺少quizzes的请求
        Artisan::call('cache:clear');
        $regapp->update([
            'is_passed' => false
        ]);
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(422);

        // 拒绝提交题目不匹配的请求
        Artisan::call('cache:clear');
        $quizzes = self::random_quizzes(-1, 'register', config('constants.registration_quiz_total'));
        $quiz_questions = $quizzes->pluck('id')->toArray();
        $regapp->update([
            'quiz_questions' => implode(",", $quiz_questions)
        ]);
        $data['quizzes'] = [
            ['id' => 1, 'answer' => '1']
        ];
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(444);

        // 成功提交，答错数量太多
        Artisan::call('cache:clear');
        unset($data['quizzes']);
        // 直接填充错误答案。填充方式为：如果正确选项数量大于1个，则只填充第一个选项；如果正确选项数量只有1个，则填充所有选项
        foreach ($quiz_questions as $quiz_question) {
            $possible_answers = QuizObjectTraits::find_quiz_set($quiz_question)->quiz_options;
            $correct_answer = $possible_answers->where('is_correct',true)->pluck('id')->toArray();
            if (count($correct_answer) > 1) {
                $data['quizzes'][] = ['id' => $quiz_question, 'answer' => $correct_answer[0]];
            } else {
                $data['quizzes'][] = ['id' => $quiz_question, 'answer' => implode(',',$possible_answers->pluck('id')->toArray())];
            }

        }
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(200)->assertJsonFragment(['has_quizzed' => false]);

        // 验证数据库是否已被更新
        $regapp_tmp = RegistrationApplication::where('email',$data['email'])->first();
        $this->assertEquals(false,$regapp_tmp->has_quizzed);
        $this->assertEquals(1,$regapp_tmp->quiz_count);

        // 成功提交，答题全对
        Artisan::call('cache:clear');

        // 先重新刷新一次model
        $regapp->update(['quiz_questions' =>'']);

        $regapp->update([
            'quiz_questions' => implode(",", $quiz_questions)
        ]);
        unset($data['quizzes']);
        // 直接填充正确答案
        foreach ($quiz_questions as $quiz_question) {
            $data['quizzes'][] = ['id' => $quiz_question, 'answer' => implode(',',QuizObjectTraits::find_quiz_set($quiz_question)->quiz_options->where('is_correct',true)->pluck('id')->toArray())];
        }
        $response = $this->post('api/register/by_invitation_email/submit_quiz', $data);
        $response->assertStatus(200)->assertJsonFragment(['has_quizzed' => true]);

        // 验证数据库中的数据
        $db_regapp = RegistrationApplication::find($regapp->id);
        $db_essay = Quiz::find($db_regapp->essay_question_id);

        $response->assertExactJson([
            "code" => 200,
            "data" => [
                "essay" => [
                    "type" => "essay",
                    "id" => $db_regapp->essay_question_id,
                    "attributes" => [
                        "body" => $db_essay->body,
                        "hint" => $db_essay->hint
                    ]
                ],
                "registration_application" => [
                    "id" => $regapp->id,
                    "type" => "registration_application",
                    "attributes" => [
                        "email" => $data['email'],
                        "has_quizzed" => true,
                        "email_verified_at" => "",
                        "submitted_at" => "",
                        "is_passed" => false,
                        "last_invited_at" => "",
                        "is_in_cooldown" => false
                    ]
                ]
            ]
        ]);

        // 验证数据库是否已被更新
        $regapp = RegistrationApplication::where('email',$data['email'])->first();
        $this->assertEquals(true,$regapp->has_quizzed);
        $this->assertNotNull($regapp->ip_address_last_quiz);
        $this->assertEquals(2,$regapp->quiz_count);
        $this->assertNotNull($regapp->send_verification_at);

        // 拒绝答题通过后再次答题
        Artisan::call('cache:clear');
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(409);

        // 验证禁止频繁访问
        /*FIXME: uncomment
        $this->post('api/register/by_invitation_email/submit_quiz', $data)
            ->assertStatus(498);*/
    }
}
