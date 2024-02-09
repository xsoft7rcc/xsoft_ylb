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
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        // 已登录
        $llq_id = trim($_GET['llq_id']);
        
        // 过滤参数
        if(empty($llq_id) || !isset($llq_id)){
            
            // 非法请求
            $result = array(
			    'code' => 203,
                'msg' => '非法请求'
		    );
        }else{
            
            // 数据库配置
        	include '../Db.php';
        
        	// 实例化类
        	$db = new DB_API($config);
        	
            // 获取详情
            $getJwInfo = $db->set_table('ylb_jumpLiuLanQi')->find(['llq_id'=>$llq_id]);
            
            // 返回数据
            if($getJwInfo){
                // token
                $llq_token = json_decode(json_encode($getJwInfo))->llq_token;
                
                // 时间戳
                $timeNum = time();
                
                 // 云储存域名
                $llq_yccym = json_decode(json_encode($getJwInfo))->llq_yccym;
                
                // 根据域名决定生成的longUrl格式
                if(strpos($llq_yccym,'qcloud.la') !== false){ 
                    
                    // 微信云托管//友源科技2024-01-02修改
                    $longUrl = $llq_yccym . '/index.html?jwid=' . $llq_id. '&token=' . $llq_token . '&t=' . $timeNum . 'f=douyin&by=yy';
                }elseif(strpos($llq_yccym,'volces.com') !== false){ 
                    
                    // 微信云托管//友源科技2024-01-02修改
                    $longUrl = $llq_yccym . '#jwid=' . $llq_id;
                }else {
                    
                    // 其他//友源科技2024-01-02修改
                    $longUrl = $llq_yccym . '/?jwid=' . $llq_id . '&token=' . $llq_token . '&t=' . $timeNum . 'f=douyin&by=ylb';
                }
                // 有结果
                $result = array(
        		    'code' => 200,
        		    'msg' => '获取成功',
        		    'longUrl' => $longUrl,
        		    'llq_token' => $llq_token,
        		    'qrcodeUrl' => $longUrl,////友源科技2024-01-02修改
        		    't' => $timeNum
    		    );
            }else{
                
                // 无结果
                $result = array(
        		    'code' => 204,
        		    'msg' => '获取失败'
    		    );
            }
        }
    	
    }else{
        
        // 未登录
        $result = array(
			'code' => 201,
            'msg' => '未登录或登录过期'
		);
    }

	// 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>