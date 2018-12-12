<?php

namespace Bahraminekoo\Larauth\Traits;

trait Normalizable
{

    public function normalize()
    {
        return [
            'kind' => $this->kind,
            'id' => $this->getKey(),
            'email' => $this->email,
            'isVerified' => $this->verified,
        ];
    }
}