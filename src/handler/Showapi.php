<?php


namespace fize\provider\pinyin\handler;

use RuntimeException;
use fize\net\Http;
use fize\crypt\Json;
use fize\provider\pinyin\PinYinHandler;

/**
 * 万维易源
 */
class Showapi extends PinYinHandler
{

    /**
     * 中文转拼音
     * @param string $words 中文词
     * @param string $delimiter 分隔符
     * @return string
     */
    public function get($words, $delimiter = ' ')
    {
        $pinyins = $this->api($words, 'data');
        $pinyins = explode(' ', $pinyins);
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
        $pinyins = $this->api($words, 'simpleData');
        $pinyins = explode(' ', $pinyins);
        return implode($delimiter, $pinyins);
    }

    /**
     * API请求
     * @param string $words 中文字符串
     * @param string $return_field 返回的字段
     * @return string
     */
    private function api($words, $return_field)
    {
        date_default_timezone_set('PRC');
        $paramArr = [
            'showapi_appid'       => $this->config['appid'],
            'showapi_timestamp'   => date('YmdHis'),
            'showapi_sign_method' => 'md5',
            'showapi_res_gzip'    => '1',
            'content'             => $words
        ];
        $paraStr = "";
        $signStr = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key . $val;
                $paraStr .= $key . '=' . urlencode($val) . '&';
            }
        }
        $signStr .= $this->config['secret'];//排序好的参数加上secret,进行md5
        $sign = strtolower(md5($signStr));
        $paraStr .= 'showapi_sign=' . $sign;//将md5后的值作为参数,便于服务器的效验
        $uri = 'http://route.showapi.com/99-38?' . $paraStr;
        $result = Http::get($uri);
        if (!$result) {
            throw new RuntimeException(Http::getLastErrMsg(), Http::getLastErrCode());
        }
        $json = Json::decode($result);
        if (isset($json['showapi_res_code']) && $json['showapi_res_code'] != 0) {
            throw new RuntimeException($json['showapi_res_error'], $json['showapi_res_code']);
        }
        return $json['showapi_res_body'][$return_field];
    }
}
