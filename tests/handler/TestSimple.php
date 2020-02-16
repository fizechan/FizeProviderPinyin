<?php

namespace handler;

use fize\provider\pinyin\handler\Simple;
use PHPUnit\Framework\TestCase;

class TestSimple extends TestCase
{

    public function testGet()
    {
        $simple = new Simple();
        $words = '我是中国人';
        $pinyin = $simple->get($words);
        var_dump($pinyin);
        self::assertEquals('wo shi zhong guo ren', $pinyin);
    }

    public function testGetInitial()
    {
        $simple = new Simple();
        $words = '我是中国人';
        $letters = $simple->getInitial($words);
        var_dump($letters);
        self::assertEquals('wszgr', $letters);
    }
}
