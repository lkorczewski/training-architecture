<?php

namespace Domain;

interface ChangeOpinionPolicy
{
    public function canChangeOpinion(): bool;
}