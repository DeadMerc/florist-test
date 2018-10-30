<?php
include './bad_loader.php';

use \DeadMerc\Test\Classes\ShopBlue;

try {
    $shop = new ShopBlue();

    if (isset($argv[1])) {
        $shop->handleAction($argv[1],$argv);
    }
    throw new Exception('Empty action');

} catch (\Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}


