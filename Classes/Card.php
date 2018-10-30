<?php

namespace DeadMerc\Test\Classes;

use DeadMerc\Test\DB;
use DeadMerc\Test\Interfaces\CardInterface;
use DeadMerc\Test\Traits\Validation;

class Card implements CardInterface
{

    use Validation;

    public $discount,$number;

    public $validation = [
        'number' => 'is_int',
        'discount' => 'is_double',
    ];

    public static function findByNumber($number)
    {
        $db = DB::getInstance();
        $card = $db->query("SELECT * FROM discount_cards WHERE `number` = '{$number}'");
        $card = $card->fetch();
        return $card;
    }

    public static function getDiscountByNumber($number)
    {
        $command = 'php project.php get_discount --num=' . $number . ' ' . Shop::getProjectFile();
        //echo $command;
        return exec($command);
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function exists(): bool
    {
        $card = self::findByNumber($this->getNumber());
        return is_array($card) ? true : false;
    }

    public function save(): void
    {
        $db = DB::getInstance();
        //without prepare, waste time now
        $res = $db->query("INSERT INTO `discount_cards` ('number','discount') " . " VALUES ('{$this->getNumber()}','{$this->getDiscount()}')");
    }

}