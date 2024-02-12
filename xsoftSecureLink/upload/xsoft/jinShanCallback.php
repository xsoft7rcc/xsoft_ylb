<?php
    // 获取jwid
	$x_id = trim($_GET['jwid']);
        
    // 过滤参数
    if(empty($x_id) || !isset($x_id)){
        
        // 缺少必要参数
        $result = array(
            "code" => 201,
            "msg" => "缺少必要参数"
        );
    }else{
        //折分x参数
        $xFirst = substr($x_id, 0, 1);
        //$jw_id = substr($x_id, 1);
        $jw_id = trim(intval(substr($x_id, 1)));
        // 数据库配置
    	include '../console/Db.php';
    	// 实例化类
    	$db = new DB_API($config);
    
    	if($xFirst == "J"){
    	    // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpJinShan')->find(['jw_id'=>$jw_id]); 
            $sid = $getJwInfo['jw_url'];
    	}else{
    	    $sid = 'chl3ncTni1pg';
    	}
        // 编码
        // header("Content-type:application/json");
        // sid
        // 来源：【金山文档】 波罗蜜多
        //分享的链接是：https://kdocs.cn/l/chl3ncTni1pg
        // 在金山文档创建完成分享出来的链接最后的字符串就是sid
        //$sid = 'chl3ncTni1pg';
    }
    // 参数
    $curlParams = [
        'appid' => 'wx5b97b0686831c076',
        'path' => 'pages/navigate/navigate',
        'query' => http_build_query([
          'url' => 'pages/preview/preview?from=wxminiprogram&fid=256465035925&sid=' . $sid . '&fname=' . urlencode('扫码加微信.docx'),
            'scene' => '102',
            'jump_from' => 'wechatlogin_guide_passive',
            'comp' => 'docx',
            'dw' => '1',
        ]),
        'env_version' => 'release',
        'is_expire' => 'true',
        'expire_time' => time() + 7200,
    ];
    
    // 获取wxurl
    $wxaurlData = cUrlPost("https://account.kdocs.cn/api/v3/miniprogram/urllink", $curlParams);
    
    function cUrlPost($url, $params)
    {
        $url .= '?' . http_build_query($params);
    
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36',
            ],
        ]);
    
        $result = curl_exec($ch);
    
        if ($result === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            return json_encode(['code' => 500, 'msg' => "cURL Error: $error (Code: $errno)"]);
        }
    
        curl_close($ch);
    
        return $result;
    }

    // 提取wxaurl
    $response = json_decode($wxaurlData, true);
    $result = $response['result'];
    $url_link = $response['url_link'];
    
    if ($result == 'ok') {
        $wxaurlCode = basename($url_link);
        $ret = ['code' => 200, 'urlScheme' => 'weixin://dl/business/?t='.$wxaurlCode];
    } else {
        $ret = ['code' => 201, 'msg' => '获取失败'];
    }
    
    $resultCallback = json_encode($ret);
    echo $_GET['callback'] . "(" . $resultCallback . ")";
?>
