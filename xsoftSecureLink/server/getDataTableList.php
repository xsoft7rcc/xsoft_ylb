<?php

    /**
     * 状态码说明
     * 状态码：200 获取成功
     * 其它状态码自己定义就行
     * 源码用途：获取数据列表
     * 作者：友源科技
     */

	// 编码
	header("Content-type:application/json");
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        $dataList = array(
            array(
                'news_id' => '10000',
                'news_title' => '友源科技产品一：实景无人直播助手',
                'news_addtime' => '2023/8/28',
                'news_pv' => '4316511',
                'news_type' => '真人语音，实时互动，助力商家轻松开播',
                'news_status' => 1
            ),
            array(
                'news_id' => '10001',
                'news_title' => '友源科技产品三：娱乐智播直播助手',
                'news_addtime' => '2023/11/13',
                'news_pv' => '3872553',
                'news_type' => '几十种游戏互动玩法，有效提高打赏率',
                'news_status' => 1
            ),
            array(
                'news_id' => '10002',
                'news_title' => '友源科技产品三：财神直播系统PC版',
                'news_addtime' => '2024/02/08',
                'news_pv' => '3516699',
                'news_type' => '支持VR场景，数字人实时控制直播互动',
                'news_status' => 1
            )
        );
        
        $result = array(
			'code' => 200,
            'msg' => '获取成功',
            'dataList' => $dataList
		);
    }else {
        
        $result = array(
			'code' => 201,
            'msg' => '未登录'
		);
    }
    
    // 输出JSON
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	
?>