<?php
include './bad_loader.php';

use \DeadMerc\Test\Classes\Shop;

try {
    $shop = new Shop();

    if (isset($argv[1])) {
        $shop->handleAction($argv[1],$argv);
    }
    throw new Exception('Empty action');

} catch (\Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}


