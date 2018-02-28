<?php

namespace App\Models;

trait RecordActivity {

    protected static function bootRecordActivity()
    {
        if (auth()->guest()) return;
        
        foreach (static::getActivityEvent() as $event) {
            static::$event(function($model) use ($event){
                $model->recordActivity($event);
            });
        }

        static::deleting(function($model){
            $model->activity()->delete();
        });
    }

    protected static function getActivityEvent()
    {
        return ['created'];
    }
    
    protected function recordActivity($event)
    {
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => auth()->id(),
        ]);
    }

    public function activity()
    {
        return $this->morphMany('App\Models\Activity', 'subject');
    }

    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }

}