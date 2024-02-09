window.onload = function (){
    
    // 获取登录状态
    getLoginStatus();
    
    // 获取页码
    var pageNum = queryURLParams(window.location.href).p;
    
    if(pageNum !== 'undefined'){
        
        // 获取当前页码的渠道码数据列表
        getChannelList(pageNum);
    }else{
        
        // 获取不到页码就获取首页
        getChannelList();
    }
    
    // clipboard插件
    var clipboard = new ClipboardJS('#shareChannelHm .modal-footer button');
    clipboard.on('success', function(e) {
        
        // 复制成功
        $('#shareChannelHm .modal-footer button').text('已复制');
    });
}

// 获取登录状态
function getLoginStatus(){
    
    // 获取
    $.ajax({
        type: "POST",
        url: "../login/getLoginStatus.php",
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 已登录
                // 账号信息
                var $accountInfo_HTML = $(
                    '<span class="user_name">'+res.user_name+'</span>' +
                    '<span onclick="exitLogin();">退出</span>'
                );
                $("#accountInfo").html($accountInfo_HTML);
                
                // 初始化
                initialize_Login('login')
            }else{
                
                // 未登录
                $('#accountInfo').html('<a href="../login/">登录账号</a>');
                
                // 初始化
                initialize_Login('unlogin');
            }
        },
        error: function() {
            
            // 服务器发生错误
            errorPage();
        }
    });
}

// 登录后的一些初始化
function initialize_Login(loginStatus){
    
    if(loginStatus == 'login'){
        
        // 显示创建按钮
        $('#button-view').css('display','block');
    }else{
        
        // 隐藏创建按钮
        $('#button-view').css('display','none');
    }
}

// 获取渠道码列表
function getChannelList(pageNum) {
    
    // 判断是否有pageNum参数传过来
    if(!pageNum){
        
        // 如果没有就默认请求第1页
        reqUrl = "./getChannelList.php";
    }else{
        
        // 如果有就请求pageNum的那一页
        reqUrl = "./getChannelList.php?p="+pageNum
    }
    
    // AJAX获取
    $.ajax({
        type: "POST",
        url: reqUrl,
        success: function(res){
            
            // 初始化
            initialize_getchannelList();
            
            // 表头
            var $thead_HTML = $(
                '<tr>' +
                '   <th>序号</th>' +
                '   <th>标题</th>' +
                '   <th>状态</th>' +
                '   <th>创建时间</th>' +
                '   <th>总访问量</th>' +
                '   <th>今天访问量</th>' +
                '   <th style="text-align: right;">操作</th>' +
                '</tr>'
            );
            $("#right .data-list thead").html($thead_HTML);
            
            // 状态码为200代表有数据
            if(res.code == 200){
                
                // 如果有数据
                // 遍历数据
                for (var i=0; i<res.channelList.length; i++) {
                    
                    // 数据判断并处理
                    // （1）序号
                    var xuhao = i+1;
                    
                    // （2）标题
                    var channel_title = res.channelList[i].channel_title;
                    
                    // （3）状态
                    if(res.channelList[i].channel_status == '1'){
                        
                        // 正常
                        var channel_status = '<span>正常</span>';
                    }else{
                        
                        // 关闭
                        var channel_status = '<span class="status_close">停用</span>';
                    }
                    
                    // （4）创建时间
                    var channel_creat_time = res.channelList[i].channel_creat_time;
                    
                    // （5）访问量
                    var channel_pv = res.channelList[i].channel_pv;
                    
                    // （6）渠道码ID
                    var channel_id = res.channelList[i].channel_id;
                    
                    // 今天访问量
                    var channel_today_pv = JSON.parse(res.channelList[i].channel_today_pv.toString()).pv;
                    var channel_today_date = JSON.parse(res.channelList[i].channel_today_pv.toString()).date;
                    
                    // 获取日期
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');
                    const todayDate = `${year}-${month}-${day}`;
                    
                    if(channel_today_date == todayDate){
                        
                        // 日期一致
                        // 显示今天的访问量
                        var channel_pv_today = channel_today_pv;
                    }else{
                        
                        // 日期不一致
                        // 显示0
                        var channel_pv_today = 0;
                    }
                    
                    // 列表
                    var $tbody_HTML = $(
                        '<tr>' +
                        '   <td>'+xuhao+'</td>' +
                        '   <td>'+channel_title+'</td>' +
                        '   <td>'+channel_status+'</td>' +
                        '   <td>'+channel_creat_time+'</td>' +
                        '   <td>'+channel_pv+'</td>' +
                        '   <td>'+channel_pv_today+'</td>' +
                        '   <td class="dropdown-td">' +
                        '       <div class="dropdown">' +
                        '    	    <button type="button" class="dropdown-btn" data-toggle="dropdown">•••</button>' +
                        '           <div class="dropdown-menu">' +
                        '               <span class="dropdown-item" data-toggle="modal" data-target="#shareChannelHm" onclick="shareChannel('+channel_id+')">分享</span>' +
                        '               <span class="dropdown-item" data-toggle="modal" data-target="#editChannelModal" onclick="getChannelInfo(this)" id="'+channel_id+'">编辑</span>' +
                        '               <a class="dropdown-item" href="./channelData.html?channelid='+channel_id+'" title="查看当前渠道的数据">数据</a>' +
                        '               <span class="dropdown-item" id="'+channel_id+'" data-toggle="modal" data-target="#DelChannelHm" onclick="askDelChannel(this)">删除</span>' +
                        '           </div>' +
                        '       </div>' +
                        '   </td>' +
                        '</tr>'
                    );
                    $("#right .data-list tbody").append($tbody_HTML);
                }
                
                // 分页
                if(res.page == 1 && res.allpage == 1){
                    
                    // 当前页码=1 且 总页码>1
                    // 无需显示分页控件
                    $("#right .data-card .fenye").css("display","none");
                }else if(res.page == 1 && res.allpage > 1){
                    
                    // 当前页码=1 且 总页码>1
                    // 代表还有下一页
                    var $ChannelFenye_HTML = $(
                    '<ul>' +
                    '   <li><button id="'+res.nextpage+'" onclick="getFenye(this);" title="下一页"><img src="../../static/img/nextPage.png" /></button></li>' +
                    '   <li><button id="'+res.allpage+'" onclick="getFenye(this);" title="最后一页"><img src="../../static/img/lastPage.png" /></button></li>' +
                    '</ul>'
                    );
                    $("#right .data-card .fenye").css("display","block");
                }else if(res.page == res.allpage){
                    
                    // 当前页码=总页码
                    // 代表这是最后一页
                    var $ChannelFenye_HTML = $(
                    '<ul>' +
                    '   <li><button id="1" onclick="getFenye(this);" title="第一页"><img src="../../static/img/firstPage.png" /></button></li>' +
                    '   <li><button id="'+res.prepage+'" onclick="getFenye(this);" title="上一页"><img src="../../static/img/prevPage.png" /></button></li>' +
                    '</ul>'
                    );
                    $("#right .data-card .fenye").css("display","block");
                }else{
                    
                    var $ChannelFenye_HTML = $(
                    '<ul>' +
                    '   <li><button id="1" onclick="getFenye(this);" title="第一页"><img src="../../static/img/firstPage.png" /></button></li>' +
                    '   <li><button id="'+res.prepage+'" onclick="getFenye(this);" title="上一页"><img src="../../static/img/prevPage.png" /></button></li>' +
                    '   <li><button id="'+res.nextpage+'" onclick="getFenye(this);" title="下一页"><img src="../../static/img/nextPage.png" /></button></li>' +
                    '   <li><button id="'+res.allpage+'" onclick="getFenye(this);" title="最后一页"><img src="../../static/img/lastPage.png" /></button></li>' +
                    '</ul>'
                    );
                    $("#right .data-card .fenye").css("display","block");
                }
                // 渲染分页控件
                $("#right .data-card .fenye").html($ChannelFenye_HTML);
                // 设置URL
                if(res.page !== 1){
                    window.history.pushState('', '', '?p='+res.page+'&token='+creatPageToken(32));
                }
                
            }else{
                
                // 未登录
                if(res.code == 201){
                    
                    // 跳转到登录页面
                    jumpUrl('../login/');
                }
                
                // 非200状态码
                noData(res.msg);
            }
            
      },
      error: function(){
        
        // 发生错误
        errorPage('data-list','getChannelList.php');
      },
    });
}

// 分页
function getFenye(e){
    
    // 页码
    var pageNum = e.id;
    
    // 获取该页列表
    getChannelList(pageNum);
}

// 创建渠道码
function creatChannel(){
    $.ajax({
        type: "POST",
        url: "./createChannel.php",
        data: $('#creatChannel').serialize(),
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 操作反馈（操作成功）
                showSuccessResult(res.msg)
                
                // 隐藏Modal
                setTimeout('hideModal("CreateChannelModal")', 500);
                
                // 重新加载客服码列表
                setTimeout('getChannelList();', 500);
            }else{
                
                // 操作反馈（操作失败）
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('createChannel.php');
        }
    });
}

// 编辑渠道码
function editChannel(){
    $.ajax({
        type: "POST",
        url: "./editChannel.php",
        data: $('#editChannel').serialize(),
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 操作反馈（操作成功）
                showSuccessResult(res.msg)
                
                // 隐藏Modal
                setTimeout('hideModal("editChannelModal")', 500);
                
                // 重新加载渠道码列表
                setTimeout('getChannelList();', 500);
            }else{
                
                // 操作反馈（操作失败）
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('editChannel.php');
        }
    });
}

// 询问是否要删除渠道活码
function askDelChannel(e){
    
    // 获取channel_id
    var channel_id = e.id;
    
    // 将群id添加到button的
    // delChannel函数用于传参执行删除
    $('#DelChannelHm .modal-footer').html(
        '<button type="button" class="default-btn" onclick="delChannel('+channel_id+');">确定删除</button>'
    )
}

// 删除渠道码
function delChannel(channel_id){
    
    // 删除
    $.ajax({
        type: "GET",
        url: "./delChannel.php?channel_id="+channel_id,
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 隐藏Modal
                hideModal("DelChannelHm");
                
                // 重新加载群列表
                setTimeout('getChannelList()', 500);
                
                // 显示删除结果
                setTimeout('showNotification("'+res.msg+'")', 600);
            }else{
                
                // 操作反馈（操作失败）
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('delChannel.php');
        }
    });
}

// 获取渠道码详情
function getChannelInfo(e){

    // 获取channel_id
    var channel_id = e.id;
    
    $.ajax({
        type: "GET",
        url: "./getChannelInfo.php?channel_id="+channel_id,
        success: function(res){

            if(res.code == 200){
                
                // 操作成功
                showSuccessResult(res.msg)
                
                // 标题
                $('input[name="channel_title"]').val(res.channelInfo.channel_title);
                
                // 获取域名列表
                getDomainNameList('edit')
                
                // 获取当前设置的域名
                $('select[name="channel_rkym"]').append(
                    '<option value="'+res.channelInfo.channel_rkym+'">'+res.channelInfo.channel_rkym+'</option>'
                );
                
                $('select[name="channel_ldym"]').append(
                    '<option value="'+res.channelInfo.channel_ldym+'">'+res.channelInfo.channel_ldym+'</option>'
                );
                
                $('select[name="channel_dlym"]').append(
                    '<option value="'+res.channelInfo.channel_dlym+'">'+res.channelInfo.channel_dlym+'</option>'
                );
                
                // 渠道码状态
                if(res.channelInfo.channel_status == '1'){
                    
                    // 正常
                    $('select[name="channel_status"]').html(
                        '<option value="1">正常</option><option value="2">停用</option>'
                    );
                }else{
                    
                    // 停用
                    $('select[name="channel_status"]').html(
                        '<option value="2">停用</option><option value="1">正常</option>'
                    );
                }
                
                // 推广链接
                $('input[name="channel_url"]').val(res.channelInfo.channel_url);
                
                // channel_id
                $('input[name="channel_id"]').val(channel_id);
                            
            }else{
                
                // 操作失败
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('getChannelInfo.php');
        }
    });
}

// 使用appendOptionsToSelect函数来为每个select元素处理选项的添加
function appendOptionsToSelect(selectElement, dataList) {
    
    if (dataList.length > 0) {
        
        // 有域名
        for (var i = 0; i < dataList.length; i++) {
            
            // 添加至指定的节点
            selectElement.append(
                '<option value="' + dataList[i].domain + '">' + dataList[i].domain + '</option>'
            );
        }
    } else {
        
        // 暂无域名
        selectElement.append('<option value="">暂无域名</option>');
    }
}

// 获取域名列表
function getDomainNameList(module){
    
    // 初始化
    initialize_getDomainNameList(module);

    // 获取
    $.ajax({
        type: "GET",
        url: "../public/getDomainNameList.php",
        success: function (res) {
            
            // 成功
            if (res.code == 200) {
                
                // 将入口、落地、短链域名添加至选项中
                appendOptionsToSelect($("select[name='channel_rkym']"), res.rkymList);
                appendOptionsToSelect($("select[name='channel_ldym']"), res.ldymList);
                appendOptionsToSelect($("select[name='channel_dlym']"), res.dlymList);
            } else {
                
                // 操作失败
                showErrorResult(res.msg);
            }
        },
        error: function () {
            
            // 服务器发生错误
            showErrorResult('服务器发生错误！可按F12打开开发者工具点击Network或网络查看返回信息进行排查！');
        }
    });
}

// 分享渠道码
function shareChannel(channel_id){
    
    // 初始化二维码
    $("#shareQrcode").html('');
    
    // 初始化短网址的二维码
    $("#shareSurlQrcode").html('');

    $.ajax({
        type: "GET",
        url: "./shareChannel.php?channel_id="+channel_id,
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 长链接
                $("#longUrl").text(res.longUrl);
                
                // 短链接
                $("#shortUrl").html('<span id="channel_'+channel_id+'">'+res.shortUrl+'</span>');
                
                // 二维码
                new QRCode(document.getElementById("shareQrcode"), res.qrcodeUrl);
                
                // 二维码，友源科技2023-12-31增加短网址的二维码
                new QRCode(document.getElementById("shareSurlQrcode"), res.qrcodeSurl);
                
                // 复制按钮
                $('#shareChannelHm .modal-footer').html(
                    '<button class="default-btn" data-clipboard-action="copy" data-clipboard-target="#channel_'+channel_id+'">复制短链接</button>'
                );
                
            }else{
                
                // 失败
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('shareChannel.php');
        }
    });
}

// 注销登录
function exitLogin(){
    
    $.ajax({
        type: "POST",
        url: "../login/exitLogin.php",
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 刷新
                location.reload();
            }
        },
        error: function() {
            
            // 服务器发生错误
            errorPage('data-list','exitLogin.php');
        }
    });
}

// 生成随机token
function creatPageToken(length) {
    var str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var result = '';
    for (var i = length; i > 0; --i) 
        result += str[Math.floor(Math.random() * str.length)];
    return result;
}

// 隐藏Modal（传入节点id决定隐藏哪个Modal）
function hideModal(modal_Id){
    $('#'+modal_Id+'').modal('hide');
}

// 显示Modal（传入节点id决定隐藏哪个Modal）
function showModal(modal_Id){
    $('#'+modal_Id+'').modal('show');
}

// 显示全局信息提示弹出提示
function showNotification(message) {
    
    // 获取文案
	$('#notification-text').text(message);
	
    // 计算文案长度并设置宽度
	var textLength = message.length * 25;
	$('#notification-text').css('width',textLength+'px');
	
    // 距离顶部的高度
	$('#notification').css('top', '25px');
	
    // 延迟隐藏
	setTimeout(function() {
		hideNotification();
	}, 3000);
}

// 隐藏全局信息提示弹出提示
function hideNotification() {
	var $notificationContainer = $('#notification');
	$notificationContainer.css('top', '-100px');
}

// 排查提示1
function showErrorResultForphpfileName(phpfileName){
    $('#app .result').html('<div class="error">服务器发生错误！可按F12打开开发者工具点击Network或网络查看'+phpfileName+'的返回信息进行排查！<a href="../../static/img/tiaoshi.jpg" target="blank">点击查看排查方法</a></div>');
    $('#app .result .error').css('display','block');
    setTimeout('hideResult()', 3000);
}

// 排查提示2
function errorPage(from,text){
    
    if(from == 'data-list'){
        
        $("#right .data-list").css('display','none');
        $("#right .data-card .loading").html(
            '<img src="../../static/img/errorIcon.png"/><br/>' +
            '<p>服务器发生错误！可按F12打开开发者工具点击Network或网络查看'+text+'的返回信息进行排查！</p>' +
            '<a href="../../static/img/tiaoshi.jpg" target="blank">点击查看排查方法</a>'
        );
        $("#right .data-card .loading").css('display','block');
        
    }else if(from == 'qrcode-list'){

        $("#qunQrcodeListModal table").html(
            '<img src="../../static/img/errorIcon.png"/><br/>' +
            '<p>服务器发生错误！可按F12打开开发者工具点击Network或网络查看'+text+'的返回信息进行排查！</p>' +
            '<a href="../../static/img/tiaoshi.jpg" target="blank">点击查看排查方法</a>'
        );
    }
    
}

// 跳转到指定路径
function jumpUrl(jumpUrl){
    
    // 1秒后跳转至jumpUrl
    setTimeout('location.href="'+jumpUrl+'"',1000);
}

// 暂无数据
function noData(text){
    
    $("#right .data-list").css('display','none');
    $("#right .data-card .loading").html(
    '<img src="../../static/img/noData.png" class="noData" /><br/>' +
    '<p class="noDataText">'+text+'</p>'
    );
    $("#right .data-card .loading").css('display','block');
}

// 初始化（getchannelList获取渠道码列表）
function initialize_getchannelList(){
    $("#right .data-list").css('display','block');
    $("#right .data-card .loading").css('display','none');
    $("#right .data-list tbody").empty('');
}

// 初始化
// 获取域名列表
function initialize_getDomainNameList(module){
    
    // 默认值
    $('#CreateChannelModal input[name="channel_title"]').val('');
    $('#CreateChannelModal input[name="channel_url"]').val('');
    $('select[name="channel_rkym"]').empty();
    $('select[name="channel_ldym"]').empty();
    $('select[name="channel_dlym"]').empty();
    hideResult();
}

// 打开操作反馈（操作成功）
function showSuccessResult(content){
    $('#app .result').html('<div class="success">'+content+'</div>');
    $('#app .result .success').css('display','block');
    setTimeout('hideResult()', 2500); // 2.5秒后自动关闭
}

// 打开操作反馈（操作失败）
function showErrorResult(content){
    $('#app .result').html('<div class="error">'+content+'</div>');
    $('#app .result .error').css('display','block');
    setTimeout('hideResult()', 2500); // 2.5秒后自动关闭
}

// 关闭操作反馈
function hideResult(){
    $("#app .result .success").css("display","none");
    $("#app .result .error").css("display","none");
    $("#app .result .success").text('');
    $("#app .result .error").text('');
}

// 获取URL参数
function queryURLParams(url) {
    var pattern = /(\w+)=(\w+)/ig;
    var parames = {};
    url.replace(pattern, ($, $1, $2) => {
        parames[$1] = $2;
    });
    return parames;
}