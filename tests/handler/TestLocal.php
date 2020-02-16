<?php

namespace handler;

use fize\provider\pinyin\handler\Local;
use PHPUnit\Framework\TestCase;

class TestLocal extends TestCase
{

    public function testGet()
    {
        $local = new Local();
        $words = '我是中国人';
        $pinyin = $local->get($words);
        var_dump($pinyin);
        self::assertEquals('wo shi zhong guo ren', $pinyin);
    }

    public function testGetInitial()
    {
        $local = new Local();
        $words = '我是中国人';
        $letters = $local->getInitial($words);
        var_dump($letters);
        self::assertEquals('wszgr', $letters);
    }
}
