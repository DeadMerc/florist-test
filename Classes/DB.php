<?php

namespace DeadMerc\Test;

use DeadMerc\Test\Classes\Shop;

final class DB
{
    private function __construct()
    {

    }

    public static function getInstance($project = false)
    {
        static $inst = null;
        //temporary instance
        if ($project) {
            $db = new \PDO('sqlite::shop.' . $project . '.data');
            $db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            return $db;
        }
        if ($inst === null) {
            //file instance
            $project = Shop::getProjectFile();
            $inst = new \PDO('sqlite::shop.' . $project . '.data');
            $inst->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        }
        return $inst;
    }

    public static function setup()
    {
        foreach (scandir(__DIR__ . '/../') as $file) {
            if (preg_match('/project(.*?)\.php/i',$file)) {
                $project = str_replace('.php','',$file);
                //echo 'PROJECT:' . $project . PHP_EOL;
                $db = DB::getInstance($project);
                $db->exec("
                    CREATE TABLE IF NOT EXISTS `orders` (
                    id INTEGER PRIMARY KEY, 
                    `name` TEXT,
                    price DOUBLE ,
                    amount INTEGER,
                    discount_card TEXT,
                    discount_val DOUBLE
                    )
                ");
                $db->exec("
                    CREATE TABLE IF NOT EXISTS `discount_cards` (
                    id INTEGER PRIMARY KEY, 
                    `number` INTEGER,
                    `discount` DOUBLE 
                    )
                ");
            }
        }
    }
}