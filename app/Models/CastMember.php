<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, Traits\Uuid;
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'bool'
    ];
    public $incrementing = false;

    public const TYPE_DIRECTOR = 1;
    public const TYPE_ACTOR = 2;
}
