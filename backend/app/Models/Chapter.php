<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Sosadfun\Traits\ColumnTrait;

class Chapter extends Model
{
    //use ColumnTrait;

    protected $primaryKey = 'post_id';
    protected $guarded = [];
    public $timestamps = false;

    public function mainpost()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function post_brief()
    {
        return $this->belongsTo(Post::class, 'post_id')->select($this->postbrief_columns);
    }

    public function volumn()
    {
        return $this->belongsTo(Volumn::class, 'volumn_id');
    }
}
