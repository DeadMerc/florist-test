<?php

namespace DeadMerc\Test\Classes;


use DeadMerc\Test\DB;
use DeadMerc\Test\Interfaces\ProductInterface;

class ShopBlue extends Shop
{
    public function createCard($arguments): void
    {
        $arguments[0] = 'php project.php';
        $command = implode(' ',$arguments);
        $res = exec($command);
        echo $res . PHP_EOL;
    }

    public function getDiscount($arguments,$internal_use = false)
    {
        $arguments[0] = 'php project.php';
        $arguments[] = Shop::getProjectFile();
        $command = implode(' ',$arguments);
        $res = exec($command);
        return $res;
    }


}