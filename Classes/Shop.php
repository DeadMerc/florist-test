<?php

namespace DeadMerc\Test\Classes;


use DeadMerc\Test\DB;
use DeadMerc\Test\Interfaces\ProductInterface;

class Shop
{

    public $db;

    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            throw new \Exception('NEED_START_VIA_CLI');
        }

        DB::setup();
    }

    public static function getProjectFile()
    {
        return str_replace('.php','',$_SERVER['argv'][0]);
    }

    public function handleAction($action,$arguments)
    {
        //maybe better with switch, whatever
        if ($action == 'create_order') {
            $this->createOrder($arguments);
            return;
        }
        if ($action == 'list_order') {
            $this->listOrders();
            return;
        }
        if ($action == 'create_card') {
            $this->createCard($arguments);
            return;
        }
        if ($action == 'get_discount') {
            $this->getDiscount($arguments);
            return;
        }
        echo 'Action not found' . PHP_EOL;
    }

    public function getDiscount($arguments,$internal_use = false)
    {
        if (isset($arguments[3]) && in_array($arguments[3],[
                'project',
                'project_white'
            ])) {
            $discount = $this->getDiscountBySum($arguments);
        } elseif (isset($arguments[3]) && preg_match('/project\_pink/i',$arguments[3])) {
            $discount = $this->getDiscountForProject($arguments,$arguments[3]);
        } else {
            $discount = $this->getDiscountByFixed($arguments);
        }
        if ($internal_use) {
            return $discount;
        }
        echo $discount;
    }

    public function getDiscountForProject($arguments,$project)
    {
        if (strpos('--num=',@$arguments[2]) == 0) {
            $card_number = str_replace('--num=','',$arguments[2]);
            $db = DB::getInstance($project);
            $discount = 0;
            $sum = 0;
            //echo $project.PHP_EOL;
            $sum_by_card = $db->query("SELECT SUM(price) FROM main.orders WHERE discount_card = '{$card_number}'")->fetch();
            if (is_array($sum_by_card) && isset($sum_by_card[0])) {
                $sum = $sum_by_card[0];
            }
            //echo 'SUM:'.$sum.PHP_EOL;
            if ($sum < 2000) {
                $discount = 5;
            }
            if ($sum >= 2000 && $sum <= 5000) {
                $discount = 7;
            }
            if ($sum > 5000) {
                $discount = 10;
            }
            return $discount;
        } else {
            throw new \Exception('CARD_NUMBER_NOT_FOUND');
        }


    }

    public function getDiscountBySum($arguments)
    {
        if (strpos('--num=',@$arguments[2]) == 0) {
            $card_number = str_replace('--num=','',$arguments[2]);
            $sum = 0;
            $discount = 0;
            foreach (scandir(__DIR__ . '/../') as $file) {
                if (preg_match('/project(.*?)\.php/i',$file)) {
                    $project = str_replace('.php','',$file);
                    //echo 'PROJECT:' . $project . PHP_EOL;
                    $db = DB::getInstance($project);

                    $sum_by_card = $db->query("SELECT SUM(price) FROM main.orders WHERE discount_card = '{$card_number}'")->fetch();
                    if (is_array($sum_by_card) && isset($sum_by_card[0])) {
                        $sum += $sum_by_card[0];
                    }
                }
            }
            if ($sum < 3000) {
                $discount = 10;
            }
            if ($sum >= 3000 && $sum <= 6000) {
                $discount = 15;
            }
            if ($sum > 6000) {
                $discount = 20;
            }
            return $discount;
        } else {
            throw new \Exception('CARD_NUMBER_NOT_FOUND');
        }
    }


    public function getDiscountByFixed($arguments)
    {
        if (strpos('--num=',@$arguments[2]) == 0) {
            $number = str_replace('--num=','',$arguments[2]);
            $card = Card::findByNumber($number);
            if (isset($card['discount'])) {
                if (empty($card['discount'])) {
                    $card['discount'] = 0;
                }
                return $card['discount'];
            } else {
                throw new \Exception('DISCOUNT_NOT_FOUND');
            }
        } else {
            throw new \Exception('CARD_NUMBER_NOT_FOUND');
        }
    }

    public function createCard($arguments)
    {
        $card = new Card();
        $fields = [
            '--num=' => 'number',
            '--discount=' => 'discount'
        ];
        $card = $this->fillClassByArguments($fields,$card,$arguments);

        //can be bug 0.1 -> 0
        if (!empty($card->number)) {
            $card->number = (int)$card->number;
        }
        if (!empty($card->discount)) {
            $card->discount = (float)$card->discount;
        }

        $card->validate();
        if ($card->exists()) {
            throw new \ErrorException('CARD_ALREADY_EXIST');
        }

        $card->save();
        echo 'SAVED' . PHP_EOL;
    }

    public function listOrders()
    {
        $db = DB::getInstance();
        $tbl = new \Console_Table();
        $tbl->setHeaders([
            'ID',
            'Item',
            'Amount',
            'Total',
            'Discount Card',
            'Discount Val'
        ]);

        $rows = $db->query('SELECT * FROM orders ORDER BY id ASC');
        $rows = $rows->fetchAll();

        foreach ($rows as $row) {
            $tbl->addRow([
                $row['id'],
                $row['name'],
                $row['amount'],
                ($row['price'] - (float)$row['discount_val']),
                $row['discount_card'],
                $row['discount_val']
            ]);
        }
        echo $tbl->getTable();
    }

    public function createOrder($arguments)
    {
        $product = new Product();
        $fields = [
            '.php' => 'project',
            '--item=' => 'name',
            '--amount=' => 'amount',
            '--total=' => 'price',
            '--discount_card=' => 'discount_card'
        ];
        $product = $this->fillClassByArguments($fields,$product,$arguments);
        //manual cast
        if (!empty($product->price)) {
            $product->price = (float)$product->price;
        }
        if (!empty($product->amount)) {
            $product->amount = (int)$product->amount;
        }

        $product->validate();
        $product->save();
        echo 'SAVED' . PHP_EOL;
    }

    protected function fillClassByArguments($fields,$class,$arguments)
    {
        /**
         * @var $argv array
         */
        foreach ($arguments as $argument) {
            foreach ($fields as $name => $field) {
                if (strpos($argument,$name) !== false) {
                    $class->$field = str_replace($name,'',$argument);
                }
            }
        }
        return $class;
    }
}