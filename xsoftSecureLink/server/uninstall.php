<?php

    /**
     * 状态码说明
     * 状态码：200 操作成功
     * 其它状态码自己定义就行
     * 源码用途：卸载程序，修改app.json的install=1就是卸载成功
     * 作者：友源科技
     */

	// 编码
	header("Content-type:application/json");
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        // 读取JSON文件内容
        $jsonFile = '../app.json';
        $jsonData = file_get_contents($jsonFile);
        
        // 解码JSON数据
        $data = json_decode($jsonData, true);
        
        // 获取安装状态
        $status = $data['install'];
        
        if($status == 2) {
            
            // 已安装
            // 设置为未安装
            $data['install'] = 1;
            
            // 编码为JSON格式
            $appJsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            
            // 写回JSON文件
            file_put_contents($jsonFile, $appJsonData);
            
            // 如果你需要操作数据库
            // 请在这里编写你操作数据库的逻辑
            
            // 连接数据库
            include '../../../../Db.php';
            $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
            
            // 这里删除所有之前建立的数据表
            $drop_ylb_all = "DROP TABLE ylb_jumpWXapp,ylb_jumpWXapp_Config,ylb_jumpWeiXin,ylb_jumpWeiXin_zima,ylb_jumpJinShan,ylb_jumpCaoLiao,ylb_jumpWXwork,ylb_jumpQQ,ylb_jumpQQqun,ylb_jumpZFB,ylb_jumpLiuLanQi";

            if($conn->query($drop_ylb_all) === TRUE) {
                // 删除表成功
                // 卸载成功
                $result = array(
        			'code' => 200,
                    'msg' => '卸载成功'
        		);
            }else {
                
                // 删除失败
                // 卸载失败
                $result = array(
        			'code' => 201,
                    'msg' => '卸载失败' . $conn->error
        		);
            }

        }else {
            
            // 未安装
            $result = array(
    			'code' => 201,
                'msg' => '卸载失败'
    		);
        }
    }else {
        
        $result = array(
			'code' => 201,
            'msg' => '未登录'
		);
    }
	
    // 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>