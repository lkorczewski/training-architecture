<?php

namespace Domain;

use Domain\Values\Value;

class Contract
{
    /** @var Value[] */
    private array $values;

    public function __construct(
        Value ...$values
    ) {
        $this->values = $values;
    }
}
