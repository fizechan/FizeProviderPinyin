<?php

namespace handler;

use fize\provider\pinyin\handler\Showapi;
use PHPUnit\Framework\TestCase;

class TestShowapi extends TestCase
{

    public function testGet()
    {
        $config = [
            'appid'  => '19707',
            'secret' => '5ca01f4c11a74ae793722ae5866050f1'
        ];
        $api = new Showapi($config);
        $words = '我是中国人';
        $pinyin = $api->get($words);
        var_dump($pinyin);
        self::assertEquals('wo shi zhong guo ren', $pinyin);
    }

    public function testGetInitial()
    {
        $config = [
            'appid'  => '19707',
            'secret' => '5ca01f4c11a74ae793722ae5866050f1'
        ];
        $api = new Showapi($config);
        $words = '我是中国人';
        $letters = $api->getInitial($words);
        var_dump($letters);
        self::assertEquals('wszgr', $letters);
    }
}
