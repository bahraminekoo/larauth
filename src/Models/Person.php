<?php

namespace Bahraminekoo\Larauth\Models;

use Illuminate\Database\Eloquent\Model;
use Hash;
use Bahraminekoo\Larauth\Traits\Normalizable;

class Person extends Model
{
    use Normalizable;

    protected $kind = "user";

    protected $fillable = [
        'email', 'password', 'verified'
    ];

    protected $hidden = [
        'password', 'verified',
        ];
    protected $appends = [
        'kind',
        ];

    public function getKindAttribute()
    {
        return $this->kind;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
