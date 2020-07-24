<?php

namespace Domain;

class CannotChangeOpinionPolicy implements ChangeOpinionPolicy
{
    public function canChangeOpinion(): bool
    {
        return false;
    }
}
