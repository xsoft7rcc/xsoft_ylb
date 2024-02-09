<?php

    /**
     * 状态码说明
     * 状态码：200 操作成功
     * 其它状态码自己定义就行
     * 源码用途：安装程序，修改app.json的install=2就是安装成功
     * 作者：友源科技
     */

	// 编码
	header("Content-type:application/json");
	
    function updateFiles($source, $destination) {
        if (is_dir($source)) { // 检查源路径是否是一个目录
            if (!file_exists($destination)) {
                mkdir($destination); // 如果目标目录不存在，则创建目标目录
            }
            
            $files = scandir($source); // 获取源路径下的所有文件和目录
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') { // 遍历源路径下的文件和目录
                    updateFiles($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file); // 递归调用更新函数，处理子目录和文件
                }
            }
        } else {
            copy($source, $destination);
            //echo "替换成功：$destination<br/>";
        }
    }
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
        
        if($status == 1) {
            
            // 未安装
            // 设置为已安装
            $data['install'] = 2;
            
            // 编码为JSON格式
            $appJsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            
            // 写回JSON文件
            file_put_contents($jsonFile, $appJsonData);
            
            // 如果你需要操作数据库
            // 请在这里编写你操作数据库的逻辑
            
            // 连接数据库
            include '../../../../Db.php';
            $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

            // 外部跳微信自建小程序表
            $ylb_jumpWXapp = "CREATE TABLE `ylb_jumpWXapp` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_type` int(2) DEFAULT NULL COMMENT '选择跳转类型',
              `jw_wxappgroup` varchar(32) DEFAULT NULL COMMENT '选择小程序（在落地小程序里面添加）',
              `jw_wxappurl` text COMMENT '小程序页面地址（不填就默认到小程序首页）',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳微信自建小程序'";
            
            // 微信自建小程序配置
            $ylb_jumpWXapp_Config = "CREATE TABLE `ylb_jumpWXapp_Config` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `app_title` varchar(64) DEFAULT NULL COMMENT '小程序名称',
              `app_id` varchar(32) DEFAULT NULL COMMENT '小程序appid',
              `app_secret` varchar(64) DEFAULT NULL COMMENT '小程序appsecret',
              `app_yqid` varchar(32) DEFAULT NULL COMMENT '小程序原始ID',
              `access_token` text COMMENT 'access_token',
              `access_token_expire_time` varchar(32) DEFAULT NULL COMMENT 'access_token_expire_time',
              `jsapi_ticket` text COMMENT 'jsapi_ticket',
              `jsapi_ticket_expire_time` varchar(32) DEFAULT NULL COMMENT 'jsapi_ticket_expire_time'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自建小程序配置'";
            
            
            // 外部跳平台所有人共用的微信小程序表
            $ylb_jumpWeiXin = "CREATE TABLE `ylb_jumpWeiXin` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_type` int(2) DEFAULT NULL COMMENT '选择跳转类型',
              `jw_wxappgroup` varchar(32) DEFAULT NULL COMMENT '选择落地小程序',
              `jw_wxappurl` text COMMENT '上传个人微信二维码（备注：可上传多张二维码，前端随机显示）',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳平台共用小程序'";
            
            // 跳平台共用小程序的微信二维码
            $ylb_jumpWeiXin_zima = "CREATE TABLE `ylb_jumpWeiXin_zima` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT '平台小程序码ID',
              `zm_id` int(10) DEFAULT NULL COMMENT '平台型微信子码ID',
              `zm_yz` int(10) DEFAULT '0' COMMENT '阈值',
              `zm_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问量',
              `zm_qrcode` text COMMENT '二维码URL',
              `zm_num` varchar(32) DEFAULT NULL COMMENT '客服微信号',
              `zm_update_time` varchar(32) DEFAULT NULL COMMENT '更新时间',
              `zm_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1开 2关）	'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台共用小程序下的微信二维码'";
            
            // 外部跳金山文档表
            $ylb_jumpJinShan = "CREATE TABLE `ylb_jumpJinShan` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳金山文档'";
            
             // 外部跳草料二维码小程序表
            $ylb_jumpCaoLiao = "CREATE TABLE `ylb_jumpCaoLiao` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_cluser_id` varchar(32) DEFAULT NULL COMMENT '草料userID',
              `jw_cli_id` varchar(32) DEFAULT NULL COMMENT '草料cliID',
              `jw_caoliaourl` text COMMENT '上传草料二维码（自动识别下面参数）',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳草料二维码小程序'";
            
            // 外部跳转到企业微信表
            $ylb_jumpWXwork = "CREATE TABLE `ylb_jumpWXwork` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳转到企业微信'";
            
            // 外部跳转个人QQ表
            $ylb_jumpQQ = "CREATE TABLE `ylb_jumpQQ` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_type` int(2) DEFAULT NULL COMMENT '选择跳转类型',
              `jw_wxappgroup` varchar(32) DEFAULT NULL COMMENT '请输入QQ号码',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳转个人QQ'";
            
            // 外部跳转QQ群表
            $ylb_jumpQQqun = "CREATE TABLE `ylb_jumpQQqun` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `jw_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `jw_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `jw_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `jw_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `jw_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `jw_type` int(2) DEFAULT NULL COMMENT '选择跳转类型',
              `jw_wxappgroup` varchar(32) DEFAULT NULL COMMENT '请输入QQ群号码',
              `jw_icon` text COMMENT '图标链接',
              `jw_bgimg` text COMMENT '背景图片',
              `jw_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `jw_url` text COMMENT '目标链接',
              `jw_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `jw_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `jw_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `jw_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `jw_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `jw_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳转QQ群'";
            
            // 外部跳转支付宝领红包表
            $ylb_jumpZFB = "CREATE TABLE `ylb_jumpZFB` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `zfb_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `zfb_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `zfb_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `zfb_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `zfb_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `zfb_storeid` varchar(32) DEFAULT NULL COMMENT '支付宝商家ID',
              `zfb_hbtoken` varchar(32) DEFAULT NULL COMMENT '红包码Token',
              `zfb_search_code` varchar(32) DEFAULT NULL COMMENT '红包码搜索码',
              `zfb_icon` text COMMENT '图标链接',
              `zfb_bgimg` text COMMENT '背景图片',
              `zfb_yccym` varchar(255) DEFAULT NULL COMMENT '安全防风域名',
              `zfb_url` text COMMENT '落地域名',
              `zfb_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `zfb_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `zfb_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `zfb_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `zfb_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `zfb_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跳转支付宝领红包'";
            
            // 微信跳到浏览器表
            $ylb_jumpLiuLanQi = "CREATE TABLE `ylb_jumpLiuLanQi` (
              `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT '自增ID',
              `llq_id` int(10) DEFAULT NULL COMMENT 'JWID',
              `llq_title` varchar(64) DEFAULT NULL COMMENT '标题',
              `llq_describe` varchar(64) DEFAULT NULL COMMENT '卡片描述',
              `llq_end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
              `llq_topv` int(10) NOT NULL DEFAULT '0' COMMENT '访问上限',
              `llq_url` text DEFAULT NULL COMMENT '目标链接',
              `llq_type` int(2) DEFAULT NULL COMMENT '访问限制',
              `llq_rkym` text COMMENT '入口域名',
              `llq_zzym` text COMMENT '中转域名',
              `llq_dlym` text COMMENT '短链域名',
              `llq_pv` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
              `llq_clickNum` int(10) DEFAULT '0' COMMENT '点击次数',
              `llq_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（1正常 2停用）',
              `llq_create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `llq_token` varchar(32) DEFAULT NULL COMMENT 'Token',
              `llq_create_user` varchar(32) DEFAULT NULL COMMENT '创建者'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信跳到浏览器'";
            
            
            if(
                $conn->query($ylb_jumpWXapp) === TRUE &&
                $conn->query($ylb_jumpWXapp_Config) === TRUE &&
                $conn->query($ylb_jumpWeiXin) === TRUE &&
                $conn->query($ylb_jumpWeiXin_zima) === TRUE &&
                $conn->query($ylb_jumpJinShan) === TRUE &&
                $conn->query($ylb_jumpCaoLiao) === TRUE &&
                $conn->query($ylb_jumpWXwork) === TRUE &&
                $conn->query($ylb_jumpQQ) === TRUE &&
                $conn->query($ylb_jumpQQqun) === TRUE &&
                $conn->query($ylb_jumpZFB) === TRUE &&
                $conn->query($ylb_jumpLiuLanQi) === TRUE) {
                // 创建表成功,开始复制插件文件
                updateFiles('../../../../../console/plugin/app/xsoftSecureLink/upload', '../../../../../');
                // 安装成功
                $result = array(
        			'code' => 200,
                    'msg' => '安装成功'
        		);
            }else {
                
                // 创建表失败
                // 安装失败
                $result = array(
        			'code' => 201,
                    'msg' => '安装失败：' . $conn->error
        		);
            }
            
        }else {
            
            // 已安装
            $result = array(
    			'code' => 201,
                'msg' => '安装失败'
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