<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/6
 * Time: 14:30
 */

namespace App;


trait Favoritable
{

    protected static function bootFavoritable()
    {
        static::deleting(function($model){
            $model->favorites->each->delete();
        });
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        if (!$this->favorites()->where(['user_id' => auth()->id()])->exists()) {
            Reputation::award(auth()->user(),Reputation::REPLY_FAVORITED);
            $this->favorites()->create(['user_id' => auth()->id()]);
        }

    }


    public function unfavorite()
    {
        $attributes = ['user_id'=>auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();

        Reputation::demote(auth()->user(),Reputation::REPLY_FAVORITED);
    }

    public function isFavorited()
    {
        return !!$this->favorites->where('user_id', auth()->id())->count();
    }


    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}