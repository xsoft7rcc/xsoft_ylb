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
        $llq_title = trim($_POST['llq_title']);
        $llq_yccym = trim($_POST['llq_yccym']);
        $llq_icon = trim($_POST['llq_icon']);
        $llq_bgimg = trim($_POST['llq_bgimg']);
        $llq_url = trim($_POST['llq_url']);
        $llq_id = trim($_POST['llq_id']);
        if(empty($jw_bgimg) || !isset($jw_bgimg)){
           $jw_bgimg = "https://m.chifeng.store/console/plugin/app/xsoftSecureLink/img/bg.png";
        }
        // 过滤参数
        if(empty($llq_title) || !isset($llq_title)){
            
            $result = array(
                'code' => 203,
                'msg' => '标题未填写'
            );
        }else if(empty($llq_yccym) || !isset($llq_yccym)){
            
            $result = array(
                'code' => 203,
                'msg' => '云储存域名未选择'
            );
        }else if(empty($llq_icon) || !isset($llq_icon)){
            
            $result = array(
                'code' => 203,
                'msg' => '图标未上传'
            );
        // }else if(empty($llq_bgimg) || !isset($llq_bgimg)){
            
        //     $result = array(
        //         'code' => 203,
        //         'msg' => '背景图片未上传'
        //     );
        }else if(empty($llq_url) || !isset($llq_url)){
            
            $result = array(
                'code' => 203,
                'msg' => '目标链接未填写'
            );
        }else if(empty($llq_id) || !isset($llq_id)){
            
            $result = array(
                'code' => 203,
                'msg' => '非法请求'
            );
        }else{
            
            // 当前登录的用户
            $LoginUser = $_SESSION["yinliubao"];
            
            // 数据库配置
            include '../Db.php';
        
            // 实例化类
            $db = new DB_API($config);
            
            // 验证当前要编辑的llq_id的发布者是否为当前登录的用户
            $getJwCreatUser = $db->set_table('ylb_jumpLiuLanQi')->find(['llq_id'=>$llq_id]);
            $llq_create_user = json_decode(json_encode($getJwCreatUser))->llq_create_user;
            
            // 用户一致：允许操作
            if($llq_create_user == $LoginUser){
                
                // llq_token
                $llq_token = MD5($llq_id . $llq_title . $llq_url . $llq_create_user);
                
                // 需更新的字段
                $updateData = [
                    'llq_title'=>$llq_title,
                    'llq_yccym'=>$llq_yccym,
                    'llq_icon'=>$llq_icon,
                    'llq_bgimg'=>$llq_bgimg,
                    'llq_url'=>$llq_url,
                    'llq_create_user'=>$llq_create_user,
                    'llq_token'=>$llq_token
                ];
                
                // 更新条件
                $updateCondition = [
                    'llq_id' => $llq_id,
                    'llq_create_user' => $LoginUser
                ];
                
                // 执行更新
                $updateSQL = $db->set_table('ylb_jumpLiuLanQi')->update($updateCondition,$updateData);
                if($updateSQL){
                    
                    // 更新成功
                    $result = array(
                        'code' => 200,
                        'msg' => '更新成功'
                    );
                }else{
                    
                    // 更新失败
                    $result = array(
                        'code' => 202,
                        'msg' => '更新失败'
                    );
                }
                
            }else{
                
                // 用户不一致
                $result = array(
                    'code' => 202,
                    'msg' => '非法操作'
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