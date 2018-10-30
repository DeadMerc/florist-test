<?php

namespace DeadMerc\Test\Classes;

use DeadMerc\Test\DB;
use DeadMerc\Test\Interfaces\ProductInterface;
use DeadMerc\Test\Traits\Validation;

class Product implements ProductInterface
{

    use Validation;

    public $price,$name,$amount,$project,$discount_card,$discount_val;

    public $validation = [
        'price' => 'is_float',
        'name' => 'not_empty',
        'amount' => 'is_int',
        'project' => 'not_empty'
    ];

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getProject(): string
    {
        return $this->project;
    }

    public function calculateDiscount($discount_card)
    {
        $discount = Card::getDiscountByNumber($discount_card);
        if ($discount) {
            $this->discount_val = ($this->price * ($discount / 100));
        } else {
            $this->discount_val = 0;
            $this->discount_card = '';
        }
    }

    public function save(): void
    {
        $db = DB::getInstance();
        if (!empty($this->discount_card)) {
            $this->calculateDiscount($this->discount_card);
        }
        //without prepare, waste time now
        $res = $db->query("INSERT INTO `orders` ('name','price','amount','discount_card','discount_val') " . " VALUES ('{$this->getName()}','{$this->getPrice()}','{$this->getAmount()}','{$this->discount_card}','{$this->discount_val}')");
    }
}