<?php

namespace DeadMerc\Test\Interfaces;

interface CardInterface
{
    public function getDiscount(): float;

    public function getNumber(): int;
}