<?php

namespace Bahraminekoo\Larauth\Models;

use Illuminate\Database\Eloquent\Model;
use Hash;
use Bahraminekoo\Larauth\Scopes\IsVerified;
use Bahraminekoo\Larauth\Traits\Normalizable;

class Person extends Model
{

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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsVerified());
    }

    public function getKindAttribute()
    {
        return $this->kind;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
