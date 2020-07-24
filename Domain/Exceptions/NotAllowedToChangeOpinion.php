<?php

namespace Domain\Exceptions;

use RuntimeException;

class NotAllowedToChangeOpinion extends RuntimeException
{
    public function __construct(string $originalStatus, string $targetStatus)
    {
        parent::__construct("Not allowed to change status from $originalStatus to $targetStatus");
    }
}
