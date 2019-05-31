<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/22
 * Time: 13:15
 */

namespace App;


use Illuminate\Support\Facades\Redis;

class Visits
{

    public $thread;

    public function __construct($thread)
    {

        $this->thread = $thread;
    }


    public function record()
    {
        Redis::incr($this->CacheKey());

        return $this;
    }

    public function reset()
    {
        Redis::del($this->CacheKey());

        return $this;
    }


    public function count()
    {
        return Redis::get($this->CacheKey())?? 0;
    }

    public function CacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }
}