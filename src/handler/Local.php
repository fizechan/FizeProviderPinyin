<?php
namespace ThinkMU\Tool;

/**
 * 中文转拼音类
 */
class PinYin{
	
	private $_driver;
	
	public $errCode = 0;
	public $errMsg = "";
	
	private static $CONFIG_SHOWAPI = array(
		'appid' => '19707',
		'secret' => '5ca01f4c11a74ae793722ae5866050f1'
	);
	
	/**
	 * 本地使用的SQLite3对象
	 * @var \SQLite3 
	 */
	private $_sqlite3 = null;


	/**
	 * 构造函数
	 * @param string $p_driver 指定要使用的接口
	 */
	public function __construct($p_driver = "local"){
		$this->_driver = $p_driver;
		
		$this->_sqlite3 = new \SQLite3(dirname(dirname(__FILE__)) . "/Data/PinYin.sqlite3", SQLITE3_OPEN_READWRITE);
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		$this->_sqlite3->close();
	}

	/**
	 * 使用SHOWAPI接口
	 * 官网：https://www.showapi.com
	 * 性质：免费，未认证版本，每秒1次，无上限
	 * @param type $cn_str
	 * @return boolean
	 */
	private function _showapi($cn_str){
		$paramArr = array(
			'showapi_appid' => self::$CONFIG_SHOWAPI['appid'],
			'showapi_timestamp' => date('YmdHis'),
			'showapi_sign_method' => 'md5',
			'showapi_res_gzip' => '1',
			'content' => $cn_str
		);
		$paraStr = "";
		$signStr = "";
		ksort($paramArr);
		foreach ($paramArr as $key => $val) {
			if ($key != '' && $val != '') {
				$signStr .= $key.$val;
				$paraStr .= $key.'='.urlencode($val).'&';
			}
		}
		$signStr .= self::$CONFIG_SHOWAPI['secret'];//排序好的参数加上secret,进行md5
		$sign = strtolower(md5($signStr));
		$paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
		$url = 'http://route.showapi.com/99-38?'.$paraStr; 
		$json = $this->_http_get($url);
		if($json){
			//var_dump($json);
			$array = json_decode($json, true);
			return isset($array['showapi_res_body']['data']) ? $array['showapi_res_body']['data'] : false;
		}else{
			return false;
		}
	}
	
	/**
	 * 本地转化中文为拼音
	 * 性质：本地，有多音词问题，有词库限制
	 * @param string $cn_str
	 * @return string
	 */
	private function _local($cn_str){
		$sGBK = iconv('UTF-8', 'GBK', $cn_str);  
        $aBuf = array();  
        for ($i=0, $iLoop=strlen($sGBK); $i<$iLoop; $i++) {  
            $iChr = ord($sGBK{$i});  
			if ($iChr>160){
				$iChr = ($iChr<<8) + ord($sGBK{++$i}) - 65536;
			}
			$aBuf[] = self::_local_zh2py($iChr);
        }  
		return implode(' ', $aBuf);  
	}

	/**
	 * 转化中文字符串成拼音
	 * @param string $cn_str 要转化的中文字符串
	 * @return mixed
	 */
	public function get($cn_str){
		switch ($this->_driver){
			case 'showapi' :
				return $this->_showapi($cn_str);
			case 'local' :
				return $this->_local($cn_str);
			default :
				$this->errCode = 2;
				$this->errMsg = "接口驱动错误";
				return false;
		}
	}
}