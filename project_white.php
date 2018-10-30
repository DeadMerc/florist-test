<?php
include './bad_loader.php';

use \DeadMerc\Test\Classes\ShopWhite;

try {
    $shop = new ShopWhite();

    if (isset($argv[1])) {
        $shop->handleAction($argv[1],$argv);
    }
    throw new Exception('Empty action');

} catch (\Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}


