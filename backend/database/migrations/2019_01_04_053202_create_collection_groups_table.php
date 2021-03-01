<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();//是谁收藏的
            $table->string('name',10)->nullable();//收藏夹名称
            $table->unsignedInteger('update_count')->default(0);
            $table->tinyInteger('order_by')->default(0);//收藏夹内排序方式
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_groups');
    }
}
