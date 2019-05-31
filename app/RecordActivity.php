<?php
/**
 * Created by PhpStorm.
 * User: rain
 * Date: 2019/5/7
 * Time: 16:34
 */

namespace App;


trait RecordActivity
{

    protected static function bootRecordActivity()
    {
        if(auth()->guest()) return ;

        foreach (static::getRecordEvents() as $event){
            static::$event(function($thread)use ($event){
                $thread->recordActivity($event);

            });
        }

        static::deleting(function($model){
            $model->activity()->delete();
        });

    }

    protected static function getRecordEvents()
    {
        return ['created'];
    }


    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);

//        Activity::create([
//            'user_id' => auth()->id(),
//            'type' => $this->getActivityType($event),
//            'subject_id' => $this->id,
//            'subject_type' => get_class($this)
//        ]);
    }

    public function activity()
    {
        return $this->morphMany('App\Activity','subject');
    }

    protected function getActivityType($event)
    {
        return $event . '_' . (new \ReflectionClass($this))->getShortName();
    }
}