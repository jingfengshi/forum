<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/22
 * Time: 11:07
 */

namespace App;


use Illuminate\Support\Facades\Redis;

class Trending
{

    public function cacheKey()
    {
        return app()->environment('testing')? 'testing_trending_threads':'trending_threads';
    }


    public function get()
    {
        return array_map('json_decode',Redis::zrevrange($this->cacheKey(), 0, 4));
    }


    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(),1,json_encode([
            'title'=>$thread->title,
            'path'=>$thread->path()
        ]));
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}