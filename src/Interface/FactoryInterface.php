<?php

namespace App\Interface;

interface FactoryInterface
{
    public function persistEntity(): void;

    public function grabEntity(): object;
}
