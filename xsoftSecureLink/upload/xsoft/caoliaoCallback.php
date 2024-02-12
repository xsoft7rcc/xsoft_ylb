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
    
    	if($xFirst == "C"){
    	    // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpCaoLiao')->find(['jw_id'=>$jw_id]); 
            
            $userID = $getJwInfo['jw_cluser_id'];
            $cliID = $getJwInfo['jw_cli_id'];
    	}else{
    	    $userID = 'oFz2xE';
            $cliID = 'qW1rtCa';
    	}
        // 作者
        // TANKING
        // 博客：https://segmentfault.com/u/tanking
        // 引流宝：https://githubfast.com/likeyun/liKeYun_Ylb
        header("Content-type:application/json");
        
        // 请求Key
        // $reqKey = $_POST['reqKey'];
        
        // // 你可以修改 reqKey 即可防止别人抓你的接口去用
        // if($reqKey !== 'likeyun') {
            
        //     $ret = array(
        //         'code' => 202,
        //         'msg' => '禁止调用'
        //     );
        //     echo json_encode($ret);
        //     exit;
        // }
        
        // 假设你的链接是：https://h5.clewm.net/?url=qr61.cn/o8dGTV/qSmmw9J&hasredirect=1
        // 下面就直接提取对应的内容替换
        // $userID = 'oFz2xE';
        // $cliID = 'qW1rtCa';
    
        // 生成草料二维码的Url Scheme
        $getTicketUrl = file_get_contents('https://nc.cli.im/api/weixin/getWxUrlScheme/?query=q=qr61.cn/'.$userID.'/'.$cliID.'&path=pages/code/code&appid=wx5db79bd23a923e8e&org_coding='.$userID.'');
        $fetchUrl = json_decode($getTicketUrl,true)['data']['wx_url_scheme']['fetchUrl'];
        $fetchUrlData = file_get_contents($fetchUrl);
        $urlScheme = json_decode($fetchUrlData,true)['data']['urlScheme'];

        if (strpos($urlScheme, "weixin://") !== false) {
            
            // 获取成功
            $ret = array(
                'code' => 200,
                'urlScheme' => $urlScheme,
                'msg' => '获取成功'
            );
        } else {
            
            // 获取失败
            $ret = array(
                'code' => 201,
                'msg' => '获取失败'
            );
        }
    }
    // // 输出JSON
    // echo json_encode($ret);
    
    $resultCallback = json_encode($ret);
    echo $_GET['callback'] . "(" . $resultCallback . ")";
    //file_put_contents("xxxxsoft3.json", $resultCallback);
?>