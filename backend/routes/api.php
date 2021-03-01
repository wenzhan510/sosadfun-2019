<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 测试用api
Route::middleware('auth:api')->group( function(){
    // Route::get('test/{id}', 'API\ThreadController@test');
});

// for each userid/ ip: 60 requests max per min
Route::group(['middleware' => ['throttle:60,1,all']], function () {
    // 注册相关
    Route::post('register', 'API\PassportController@register');
    Route::post('register_by_invitation', 'API\PassportController@register_by_invitation');
    Route::post('login', 'API\PassportController@login')->name('login');
    Route::post('logout', 'API\PassportController@logout')->name('logout');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset_via_email', 'API\PassportController@reset_password_via_email');

    Route::patch('password/reset_via_email', 'API\PassportController@reset_password_via_email')->name('password.reset');
    Route::patch('password/reset_via_password', 'API\PassportController@reset_password_via_password');
    Route::patch('email/reset_via_password', 'API\PassportController@reset_email_via_password');//修改个人邮箱
    Route::get('email/reset_via_password/{token}', 'API\PassportController@reset_email_via_token');//确认个人邮箱为本人

    // register by invitation token
    // check if token is valid
    Route::post('register/by_invitation_token/submit_token', 'API\PassportController@register_by_invitation_token_submit_token');
    // ->middleware('throttle:1,5,checkin'); // FIXME:uncomment throttle (1 times per 5 min), it's temporarily disabled for testing

    // 输入邮箱申请测试答题
    Route::post('register/by_invitation_email/submit_email', 'API\RegAppController@submit_email'); // 输入邮箱尝试注册
    Route::post('register/by_invitation_email/submit_quiz', 'API\RegAppController@submit_quiz'); // 尝试答题
    Route::post('register/by_invitation_email/submit_email_confirmation_token', 'API\RegAppController@submit_email_confirmation_token'); // 填写确认邮箱的token
    Route::get('register/by_invitation_email/resend_email_verification', 'API\RegAppController@resend_email_verification'); // 重新发送邮箱确认邮件
    Route::post('register/by_invitation_email/submit_essay', 'API\RegAppController@submit_essay'); // 提交小论文
    Route::get('register/by_invitation_email/resend_invitation_email', 'API\RegAppController@resend_invitation_email'); // 重发邀请邮件

    // 关联账户相关
    Route::get('/linkaccount','API\LinkAccountController@index');
    Route::post('/linkaccount/store','API\LinkAccountController@store');
    Route::get('/linkaccount/switch/{id}','API\LinkAccountController@switch');
    Route::delete('/linkaccount/destroy','API\LinkAccountController@destroy');

    // 默认页面
    Route::get('/', 'API\PageController@home')->name('home');// 网站首页

    Route::get('/administration_records', 'API\PageController@administration_records')->name('administration_record');// 管理目录

    // 固定信息
    Route::get('config/allTags', 'API\PageController@allTags');
    Route::get('config/allChannels', 'API\PageController@allChannels');
    Route::get('config/allTitles', 'API\PageController@allTitles');
    Route::get('config/system', 'API\PageController@system');

    // 讨论串/讨论楼/讨论帖
    Route::get('/thread_index', 'API\ThreadController@thread_index');//某个版面的讨论贴，或者书评/问答列表
    Route::get('/channel/{channel}', 'API\ThreadController@channel_index')->middleware('filter_channel');//某个版面的讨论贴，或者书评/问答列表

    Route::apiResource('thread', 'API\ThreadController');
    Route::get('/thread/{thread}/profile', 'API\ThreadController@show_profile');//展示书籍或讨论首页(非论坛模式，默认从此进入)
    Route::patch('/thread/{thread}/update_tag', 'API\ThreadController@update_tag');

    // 书籍
    Route::get('/book','API\BookController@index');// 文库目录和筛选
    Route::get('/books/{thread}', 'API\BookController@redirect')->name('book.redirect');// 往期书籍遗留导航
    Route::patch('/thread/{thread}/update_tongren', 'API\BookController@update_tongren');

    Route::patch('/thread/{thread}/update_component_index', 'API\ComponentController@update_component_index');//书籍重排序

    Route::apiResource('/thread/{thread}/post', 'API\PostController')->only(['show', 'store'])->middleware('filter_thread');
    Route::apiResource('/post', 'API\PostController')->only(['update', 'destroy']);

    Route::get('/post/{post}', 'API\PostController@redirect')->name('post.redirect');

    Route::patch('/post/{post}/convert', 'API\ComponentController@convert');// 将特别的post转化为普通post, 或将post转化成特殊格式 // TODO
    Route::patch('/post/{post}/fold', 'API\PostController@fold');

    // 用户
    Route::apiResource('/user', 'API\UserController')->only(['index', 'show', 'destroy']);

    // 用户个人管理
    Route::patch('user/{user}/intro', 'API\UserController@updateIntro');//修改个人简介
    Route::get('user/{user}/preference', 'API\UserController@getPreference');// 获取用户的个人偏好信息
    Route::get('user/{user}/reminder', 'API\UserController@getReminder');// 获取用户的当前未读提醒信息。这个数据前端定时获取。
    Route::patch('user/{user}/reminder', 'API\UserController@updateReminder');// 更新用户的当前未读提醒信息（比如标记哪些已读）

    Route::patch('user/{user}/preference', 'API\UserController@updatePreference');//修改个人偏好

    //用户的个人内容
    Route::get('user/{user}/thread', 'API\UserController@showThread');// 展示某用户的全部thread，当本人或管理查询时，允许出现匿名和私密thread
    Route::get('user/{user}/book', 'API\UserController@showBook');// 展示某用户的全部book，当本人或管理查询时，允许出现匿名和私密book
    Route::get('user/{user}/post', 'API\UserController@showPost');// 展示某用户的全部post，当本人或管理查询时，允许出现匿名和私密post
    Route::get('user/{user}/status', 'API\UserController@showStatus');// 展示某用户的全部status


    // 签到
    Route::get('qiandao', 'API\QiandaoController@qiandao')
        ->middleware('throttle:1,1,checkin');// 签到
    Route::get('qiandao/complement', 'API\QiandaoController@complement_qiandao')
        ->middleware('throttle:1,1,comp_checkin');// 补签



    // 关注
    Route::get('user/{user}/follower', 'API\FollowerController@follower');//展示该用户的所有粉丝
    Route::get('user/{user}/following', 'API\FollowerController@following');//展示该用户的所有关注
    Route::get('user/{user}/followingStatuses', 'API\FollowerController@followingStatuses');//展示该用户的所有关注，附带关注信息更新状态
    Route::post('user/{user}/follow','API\FollowerController@store');//关注某人
    Route::delete('user/{user}/follow','API\FollowerController@destroy');//取关某人
    Route::patch('user/{user}/follow','API\FollowerController@update');//切换是否跟踪动态
    Route::get('user/{user}/follow','API\FollowerController@show');//返回与该关注相关的信息（是否跟踪动态，是否已阅更新）

    //收藏部分
    Route::post('/thread/{thread}/collect', 'API\CollectionController@store');//收藏某个thread
    Route::patch('/collection/{collection}', 'API\CollectionController@update');//修改某个收藏
    Route::delete('/collection/{collection}', 'API\CollectionController@destroy');//删除某个收藏
    Route::get('user/{user}/collection', 'API\CollectionController@index');//查看收藏更新
    Route::patch('user/{user}/clear_update', 'API\CollectionController@clear_update');//收藏内容全部已读

    Route::get('user/{user}/collection_group', 'API\CollectionGroupController@index');//查看收藏分页列表
    Route::post('collection_group', 'API\CollectionGroupController@store');//新建收藏分页
    Route::patch('collection_group/{collection_group}', 'API\CollectionGroupController@update');//修改收藏分页
    Route::delete('collection_group/{collection_group}', 'API\CollectionGroupController@destroy');//删除收藏分页

    // 动态部分
    Route::apiResource('status', 'API\StatusController');
    Route::patch('status/{status}/no_reply', 'API\StatusController@no_reply');//作者设置某动态不可回复
    Route::get('follow_status', 'API\StatusController@follow_status');//关注的人的动态

    // 题头部分
    Route::apiResource('quote', 'API\QuoteController')->only(['index','show','store','destroy']);

    Route::get('user/{user}/quote', 'API\QuoteController@userQuote');// 展示某用户的全部quote，当本人或管理查询时，允许出现匿名quote

    Route::get('/admin/manage/quote_review_index', 'API\QuoteController@review_index')->name('quote.review_index');//批量审核题头

    Route::patch('/quote/{quote}/review','API\QuoteController@review')->name('quote.review');//审核单独题头

    // 私信部分
    Route::get('/user/{user}/message', 'API\MessageController@index');// 展示某用户的信箱，仅允许本人和管理员查询
    Route::post('message', 'API\MessageController@store');
    Route::post('groupmessage', 'API\MessageController@groupmessage');//管理员群发私信
    Route::post('publicnotice', 'API\MessageController@publicnotice');//管理员发系统消息
    Route::get('publicnotice', 'API\MessageController@publicnotice_index');//用户查看当前全部系统公共消息
    // 消息部分
    Route::get('/user/{user}/activity', 'API\ActivityController@index');// 展示某用户的站内提醒，仅允许本人和管理员查询
    Route::post('/clearupdates', 'API\ActivityController@clearupdates');// 清除未读提醒
    Route::get('/user/{user}/administration_record', 'API\MessageController@administration_record')->name('user.administration_record');// 展示某用户的被管理记录

    // 阅读历史保存?

    // 投票
    Route::apiResource('vote', 'API\VoteController')->only(['index', 'store', 'destroy']);

    Route::get('/user/{user}/vote_sent','API\VoteController@sent');//我给出的评票
    Route::get('/user/{user}/vote_received','API\VoteController@received');//我收到的评票

    // 打赏
    Route::apiResource('reward', 'API\RewardController')->only(['index', 'store', 'destroy']);

    Route::get('/user/{user}/reward_sent','API\RewardController@sent');//我给出的评票
    Route::get('/user/{user}/reward_received','API\RewardController@received');//我收到的打赏

    // 头衔
    Route::get('user/{user}/title', 'API\TitleController@title');
    Route::post('wearTitle/{title}', 'API\TitleController@wear');
    Route::post('redeemTitle', 'API\TitleController@redeem_title');

    // 作业列表
    Route::apiResource('homework', 'API\HomeworkController')->only(['index', 'store', 'update', 'destroy']);
    Route::get('/user/{user}/homework','API\HomeworkController@userHomework');
    // 用户参加作业
    Route::get('/user/{user}/homework_invitation','API\HomeworkController@userHomeworkInvitation');
    // 用户作业邀请
    Route::post('/homework/{homework}/register', 'API\HomeworkController@register');//用户注册参加作业
    Route::post('/homework/{homework}/submit', 'API\HomeworkController@submit');//用户提交作业
    Route::post('/homework/{homework}/submit_work', 'API\HomeworkController@submitWork');//用户提交作业
    Route::patch('/homework_registration/{homework_registration}/mark_as_finished', 'API\HomeworkController@markAsFinished');//用户标记作业结束
    Route::patch('/homework/{homework}/deactivate', 'API\HomeworkController@deactivate');//管理员终止作业
    Route::patch('/homework/{homework}/send_reward', 'API\HomeworkController@send_reward');//管理员终发放奖励
    Route::patch('/homework_registration/{homework_registration}/manage_registration', 'API\HomeworkController@manage_registration');//管理员修改注册详情

    // 帮助FAQ管理
    Route::apiResource('helpfaq', 'API\FAQController')->only(['index', 'store', 'update', 'destroy']);

    // 注意顺序
    Route::get('quiz/get_quiz','API\QuizController@getQuiz');
    Route::post('quiz/submit_quiz','API\QuizController@submitQuiz');
    Route::apiResource('quiz', 'API\QuizController');


    // 标签系统管理
    Route::apiResource('tag', 'API\TagController', ['only' => [
        'index', 'store', 'update', 'show', 'destroy'
    ]]);

    // patreon & donation record controllers
    Route::get('donation','API\DonationController@index')->name('donation.index');
    Route::patch('donation/{donation}', 'API\DonationController@donation_update')->name('donation.update');
    Route::get('user/{user}/donation', 'API\DonationController@user_donation')->name('donation.user_donation');

    Route::get('user/{user}/reward_token', 'API\DonationController@user_reward_token')->name('donation.user_reward_token');
    Route::post('reward_token_redeem', 'API\DonationController@reward_token_redeem')->name('donation.reward_token_redeem');
    Route::post('reward_token', 'API\DonationController@reward_token_store')->name('donation.reward_token_store');

    Route::post('patreon', 'API\DonationController@patreon_store')->name('patreon.store');
    Route::delete('patreon/{patreon}', 'API\DonationController@patreon_destroy')->name('patreon.store');
    Route::get('patreon', 'API\DonationController@patreon_index')->name('patreon.index');
    Route::patch('patreon/{patreon}/approve', 'API\DonationController@patreon_approve')->name('patreon.approve');
    Route::patch('patreon/{patreon}/disapprove', 'API\DonationController@patreon_disapprove')->name('patreon.disapprove');
    Route::post('patreon_upload', 'API\DonationController@patreon_upload')->name('patreon.upload');

    Route::post('admin/management','API\AdminController@management')->name('admin.management');

});
