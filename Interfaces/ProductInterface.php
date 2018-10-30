<?php

namespace DeadMerc\Test\Interfaces;

interface ProductInterface
{
    public function getPrice(): float;

    public function getName(): string;

    public function getAmount(): int;

    //deprecated
    public function getProject(): string;
}