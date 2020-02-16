<?php


namespace fize\provider\pinyin;

/**
 * 接口：中文转拼音
 */
abstract class PinYinHandler
{

    /**
     * @var array 配置
     */
    protected $config;

    /**
     *  构造
     * @param array $config 配置
     */
    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * 中文转拼音
     * @param string $words 中文词
     * @param string $delimiter 分隔符
     * @return string
     */
    abstract public function get($words, $delimiter = ' ');

    /**
     * 获取首字母
     * @param string $words 中文词
     * @param string $delimiter 分隔符
     * @return string
     */
    abstract public function getInitial($words, $delimiter = '');
}
