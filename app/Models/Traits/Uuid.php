<?php

namespace App\Models\Traits;
use Ramsey\Uuid\Uuid as RUuid;

trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($obj) {
            $obj->id = RUuid::uuid4();
        });
    }
}
