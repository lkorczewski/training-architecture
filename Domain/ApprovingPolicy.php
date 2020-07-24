<?php

namespace Domain;

use Domain\Values\Value;

interface ApprovingPolicy
{
    public function getApprovers(Value $value);
}