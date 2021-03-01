<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;

    /**
    * 如果想要让test不影响数据库、不存储在你现在的数据库里面，使用下面这一行代码。
    */
    // use DatabaseTransactions;

    /**
    * 如果想让test整个运行前后重置database的话，可以使用下面的代码
    */
    // use RefreshDatabase;
    // public function setUp()
    // {
    //     parent::setUp();
    //
    //     $this->artisan('db:seed');
    //     $this->artisan('passport:install');
    // }


}
