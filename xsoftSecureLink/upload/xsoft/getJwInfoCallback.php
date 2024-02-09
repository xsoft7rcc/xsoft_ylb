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
    
    	if($xFirst == "A"){
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpWXapp')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpWXapp')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "W") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpWeiXin')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpWeiXin')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "J") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpJinShan')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpJinShan')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "C") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpCaoLiao')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpCaoLiao')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "K") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpWXwork')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpWXwork')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "Q") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpQQ')->find(['jw_id'=>$jw_id]); 
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpQQ')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}elseif ($xFirst == "G") {
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpQQqun')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpQQqun')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}else{
        	 // 获取当前jw_id的详情
            $getJwInfo = $db->set_table('ylb_jumpWeChat')->find(['jw_id'=>$jw_id]);  
            
            // 更新访问次数
            $jw_pv = json_decode(json_encode($getJwInfo))->jw_pv;
            $newPV = $jw_pv + 1;
            $db->set_table('ylb_jumpWeChat')->update(['jw_id'=>$jw_id],['jw_pv'=>$newPV]);
    	}
        
        

        // 返回数据
        if($getJwInfo){
            
            // 有结果
            $result = array(
                "code" => 200,
                "msg" => "获取成功",
                "jwInfo" => $getJwInfo
            );
        }else{
            
            // 获取失败
            $result = array(
                "code" => $jw_id,
                "msg" => "获取失败"
            );
        }
    }

    // 输出callback
    $resultCallback = json_encode($result);
    echo $_GET['callback'] . "(" . $resultCallback . ")";
    
?>