<?php

    /**
     * 状态码说明
     * 200 成功
     * 201 未登录
     * 202 失败
     * 203 空值
     * 204 无结果
     */

    // 页面编码
    header("Content-type:application/json");
    
    // 接收参数
    $x_id = trim($_GET['jwid']);
    
    // 过滤参数
    if(empty($x_id) || !isset($x_id)){
        
        $result = array(
            'code' => 203,
            'msg' => '缺少必要参数'
        );
    }else{
        //折分x参数
        $xFirst = substr($x_id, 0, 1);
        $jw_id = substr($x_id, 1);
        
        // 数据库配置
        include '../console/Db.php';
        // 实例化类
        $db = new DB_API($config);
        
        if($xFirst === "A"){
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpWXapp')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpWXapp')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "W") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpWeiXin')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpWeiXin')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "J") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpJinShan')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpJinShan')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "C") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpCaoLiao')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpCaoLiao')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "K") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpWXwork')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpWXwork')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "Q") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpQQ')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpQQ')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}elseif ($xFirst === "G") {
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpQQqun')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpQQqun')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}else{
            // 获取clickNum
            $clickNum = json_decode(json_encode($db->set_table('ylb_jumpWeChat')->find(['jw_id'=>$jw_id])))->jw_clickNum;
            
            // 新的clickNum
            $jw_clickNum = $clickNum + 1;
            
            // 执行更新
            $updateClickNum = $db->set_table('ylb_jumpWeChat')->update(['jw_id'=>$jw_id],['jw_clickNum'=>$jw_clickNum]);
    	}
        
        if($updateClickNum){
            
            // 更新成功
            $result = array(
                'code' => 200,
                'msg' => $jw_id . '点击次数更新成功'
            );
        }else{
            
            // 更新失败
            $result = array(
                'code' => 202,
                'msg' => $jw_id . '更新失败'
            );
        }
    }

    // 输出callback
    $resultCallback = json_encode($result);
    echo $_GET['callback'] . "(" . $resultCallback . ")";
    
?>