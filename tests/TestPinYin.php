<?php


use fize\provider\pinyin\PinYin;
use PHPUnit\Framework\TestCase;

class TestPinYin extends TestCase
{

    public function testGetInstance()
    {
        $handler = PinYin::getInstance('Local');

        $words = '我是中国人';
        $pinyin = $handler->get($words);
        var_dump($pinyin);
        self::assertEquals('wo shi zhong guo ren', $pinyin);

        $words = '我是中国人';
        $letters = $handler->getInitial($words);
        var_dump($letters);
        self::assertEquals('wszgr', $letters);
    }
}
