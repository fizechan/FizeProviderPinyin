<?php

namespace fize\provider\pinyin\handler;

use SQLite3;
use fize\provider\pinyin\PinYinHandler;

/**
 * 本地全功能
 */
class Local extends PinYinHandler
{

    /**
     * @var SQLite3 数据库
     */
    private $db;

    /**
     *  构造
     * @param array $config 配置
     */
    public function __construct(array $config = null)
    {
        parent::__construct($config);
        $this->db = new SQLite3(dirname(dirname(__DIR__)) . "/data/pinyin.sqlite3", SQLITE3_OPEN_READWRITE);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * 中文转拼音
     * @param string $words 中文词
     * @param string $delimiter 分隔符
     * @return string
     */
    public function get($words, $delimiter = ' ')
    {
        $words = self::stringToArray($words);
        $pinyins = [];
        foreach ($words as $word) {
            $pinyins[] = self::wordToPinyin($word);
        }
        return implode($delimiter, $pinyins);
    }

    /**
     * 获取首字母
     * @param string $words 中文词
     * @param string $delimiter 分隔符
     * @return string
     */
    public function getInitial($words, $delimiter = '')
    {
        $words = self::stringToArray($words);
        $pinyins = [];
        foreach ($words as $word) {
            $pinyins[] = substr(self::wordToPinyin($word), 0, 1);
        }
        return implode($delimiter, $pinyins);
    }

    /**
     * @param $word
     * @return string
     */
    private function wordToPinyin($word)
    {
        $py = $this->db->querySingle("SELECT py FROM py_zi WHERE zi = '{$word}'");
        if (strstr($py, ',') !== false) {
            $py = explode(',', $py);
            $py = $py[0];  // 多音字取第一个
        }
        return $py;
    }

    /**
     * 字符串转数组
     * @param string $str 字符串
     * @return array
     */
    private static function stringToArray($str)
    {
        $result = [];
        $len = strlen($str);
        $i = 0;
        while ($i < $len) {
            $chr = ord($str[$i]);
            if ($chr == 9 || $chr == 10 || (32 <= $chr && $chr <= 126)) {
                $result[] = substr($str, $i, 1);
                $i += 1;
            } elseif (192 <= $chr && $chr <= 223) {
                $result[] = substr($str, $i, 2);
                $i += 2;
            } elseif (224 <= $chr && $chr <= 239) {
                $result[] = substr($str, $i, 3);
                $i += 3;
            } elseif (240 <= $chr && $chr <= 247) {
                $result[] = substr($str, $i, 4);
                $i += 4;
            } elseif (248 <= $chr && $chr <= 251) {
                $result[] = substr($str, $i, 5);
                $i += 5;
            } elseif (252 <= $chr && $chr <= 253) {
                $result[] = substr($str, $i, 6);
                $i += 6;
            }
        }
        return $result;
    }

}
