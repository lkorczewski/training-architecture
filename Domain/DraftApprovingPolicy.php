<?php

namespace Domain;

use Domain\Values\Penalty;
use Domain\Values\PickUpHours;
use Domain\Values\Value;

class DraftApprovingPolicy implements ApprovingPolicy
{
    public function getApprovers(Value $value)
    {
        switch (get_class($value)) {
            case PickUpHours::class:
                return [
                    'pm'
                ];
            case Penalty::class:
                return [
                    'head.pm',
                    'manager'
                ];
            default: throw new \LogicException('invalid value');
        }
    }
}
