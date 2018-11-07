<?php
namespace App\Libs;
// 簡訊API
class Sms
{
    static function factory()
	{
	    return new Sms();
	}
	
	
    /**
     * 簡訊 API 來源 - 配帳密
     * 
     * @name = [可空] 使用的API名稱， 預設為 kotsms
     */
    private static function _source($name = 'kotsms')
    {        
        require_once(config('constants.FILEPATH').'/App/Libs/Sms.php');
        echo config('constants.FILEPATH');exit();
        return sms_source($name);
    }
    
    
    /**
     *  三竹API
     */
    private static function _mitake_api($item = array())
    {
        $mobile = str_replace('-', '', strip_tags(trim($item['mobile'])));

        if($mobile == '' || $item['msg'] == '' || mb_strlen($item['msg'], 'UTF-8') > 70)
		{
		    return false;
        }

        $url = $item['url'].'?username='.urlencode($item['account']).'&password='.urlencode($item['password']).'&dstaddr='.urlencode($mobile).'&smbody='.$item['msg'].'&CharsetURL=utf-8';

        $result = file_get_contents($url);

        return $result;
    }
    
    
    /**
     * sms-get API
     */
    private static function _sms_get_api($item = array())
    {
        $mobile = str_replace('-', '', strip_tags(trim($item['mobile'])));
        $msg    = $item['msg'];
        
        if($mobile == '' || $msg == '' || mb_strlen($msg, 'UTF-8') > 70)
		{
		    return false;
        }

        if(mb_detect_encoding($msg) != 'UTF-8')
        {
            $msg = iconv(mb_detect_encoding($msg), 'UTF-8', $msg);
        }

        $url = $item['url'].'?username='.urlencode($item['account']).'&password='.urlencode($item['password']).'&method=1&phone='.urlencode($mobile).'&sms_msg='.urlencode($msg);
        
        $result    = file_get_contents($url);
        $_callback = json_decode($result, true);
        
        //寫入剩餘點數紀錄
        if(empty($_callback) == false)
        {
            $_callback = explode("|", $_callback['error_msg']);
            
            $fp = fopen(FILEPATH."company/lib/txt/sms_point_count.txt", 'w');
            fwrite($fp, $_callback[2]);
            fclose($fp);
        }
        
        return $result;
    }
    
    
    /**
     * 簡訊王 API
     */
    private static function _kotsms_api($item = array())
    {
        $mobile = str_replace('-', '', strip_tags(trim($item['mobile'])));
        $msg    = $item['msg'];
        $url    = $item['url'][1];
        
        if($mobile == '' || $msg == '' || mb_strlen($msg, 'UTF-8') > 70)
		{
		    return false;
        }
        
        //大量模式
        if($item['large'] == true)
        {
            $url = $item['url'][2];
        }

        if(mb_detect_encoding($msg) != 'BIG5')
        {
            $msg = iconv(mb_detect_encoding($msg), 'BIG5', $msg);
        }

        $url       = $url.'?username='.urlencode($item['account']).'&password='.urlencode($item['password']).'&dstaddr='.urlencode($mobile).'&smbody='.urlencode($msg);
        $result    = file_get_contents($url);
        $_callback = json_decode($result, true);
        
        return $result;
    }
    
    
    /**
	 * 發送簡訊
	 * 
	 * @mobile = 手機號碼
	 * @msg    = 簡訊內容， 限定70個字
     * @source = 使用那一個簡訊商 mitake=三竹, sms_get, kotsms=簡訊王
     * @large  = 是否為大量發送模式, 目前只有簡訊王有此模式 true=是, false=否, 預設為false
	 */
    public function send($mobile = null, $msg = null, $source = 'kotsms', $large = false)
	{
        if(config('constants.IS_PRODUCTION') == 2)
        {
            return true;  
        }
        
        $item = Sms::_source($source);

        $item['mobile'] = $mobile;
        $item['msg']    = $msg;
		$item['large']  = $large;
                
		$api_name = "_".$source."_api";
		
        $result = Sms::$api_name($item);

        return $result;
	}
    
    
    /**
     * 簡訊點數查詢
     * 
     * @source = 使用那一個簡訊商 kotsms=簡訊王
     */
    static function checkPoint($source = 'kotsms')
    {
        if($source != "kotsms")
        {
            return false;  
        }
        
        $item = Sms::_source($source);
        
        $url    = $item['url'][3].'?username='.urlencode($item['account']).'&password='.urlencode($item['password']);
        $result = file_get_contents($url);
        
        return $result;
    }
}
?>