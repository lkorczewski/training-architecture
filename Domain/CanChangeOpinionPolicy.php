<?php

namespace Domain;

class CanChangeOpinionPolicy implements ChangeOpinionPolicy
{
    public function canChangeOpinion(): bool
    {
        return true;
    }
}
