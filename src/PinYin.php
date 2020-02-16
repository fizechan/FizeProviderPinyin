<?php

namespace fize\provider\pinyin;

/**
 * 中文转拼音
 */
class PinYin
{

    /**
     * @var PinYinHandler 接口处理器
     */
    protected static $handler;

    /**
     * 取得单例
     * @param string $handler 使用的实际接口名称
     * @param array $config 配置项
     * @return PinYinHandler
     */
    public static function getInstance($handler, array $config = [])
    {
        if (empty(self::$handler)) {
            $class = '\\' . __NAMESPACE__ . '\\handler\\' . $handler;
            self::$handler = new $class($config);
        }
        return self::$handler;
    }

}
