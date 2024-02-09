
// 进入就加载
window.onload = function (){
    
    // 获取登录状态
    getLoginStatus();
    
    // 获取页码
    var pageNum = queryURLParams(window.location.href).p;
    
    if(pageNum !== 'undefined'){
        
        // 获取当前页码数据列表
        getJwList(pageNum);
    }else{
        
        // 获取首页
        getJwList();
    }
    
    // clipboard插件
    var clipboard = new ClipboardJS('#ShareJwModal .modal-footer button');
    clipboard.on('success', function(e) {
        
        // 复制成功
        $('#ShareJwModal .modal-footer button').text('已复制');
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
                initialize_Login('login',res.user_admin)
            }else{
                
                // 未登录
                $('#accountInfo').html('<a href="../login/">登录账号</a>');
                
                // 初始化
                initialize_Login('unlogin');
            }
        },
        error: function() {
            
            // 服务器发生错误
            errorPage('data-list','getLoginStatus.php');
        }
    });
}

// 登录后的一些初始化
function initialize_Login(loginStatus,adminStatus){
    
    if(loginStatus == 'login'){
        
        // 显示创建按钮
        $('#button-view').css('display','block');
        
        // 判断管理员权限
        if(adminStatus == 1){
            
            // 显示开放API按钮
            $('#openApi').html(
                '<a href="./openApi.html"><button class="tint-btn" style="margin-left: 5px;">开放API</button></a>'
            );
        }
        
    }else{
        
        // 隐藏创建按钮
        $('#button-view').css('display','none');
        $('#openApi').css('display','none');
    }
}

// 获取抖音跳微信卡片列表
function getJwList(pageNum) {
    
    // 判断是否有pageNum参数传过来
    if(!pageNum){
        
        // 如果没有就默认请求第1页
        reqUrl = "./getJwList.php";
    }else{
        
        // 如果有就请求pageNum的那一页
        reqUrl = "./getJwList.php?p="+pageNum
        
        // 设置URL路由
        setRouter(pageNum);
    }
    
    // AJAX获取
    $.ajax({
        type: "POST",
        url: reqUrl,
        success: function(res){
            
            // 初始化
            initialize_getJwList();
            
            // 表头
            var $thead_HTML = $(
                '<tr>' +
                '   <th>序号</th>' +
                '   <th>标题</th>' +
                '   <th>图标</th>' +
                '   <th>创建时间</th>' +
                '   <th>访问次数</th>' +
                '   <th>点击次数</th>' +
                '   <th style="text-align: right;">操作</th>' +
                '</tr>'
            );
            $("#right .data-list thead").html($thead_HTML);
            
            // 状态码为200代表有数据
            if(res.code == 200){
                
                // 如果有数据
                // 遍历数据
                for (var i=0; i<res.jwList.length; i++) {
                    
                    // 序号
                    var xuhao = i+1;
                    
                    // 标题
                    var llq_title = res.jwList[i].llq_title;
                    
                    // 图标
                    var llq_icon = res.jwList[i].llq_icon;
                    
                    // 创建时间
                    var llq_create_time = res.jwList[i].llq_create_time;
                    
                    // 访问次数
                    var llq_pv = res.jwList[i].llq_pv;
                    
                    // 点击次数
                    var llq_clickNum = res.jwList[i].llq_clickNum;
                    
                    // ID
                    var llq_id = res.jwList[i].llq_id;
                    
                    // 列表
                    var $tbody_HTML = $(
                        '<tr>' +
                        '   <td>'+xuhao+'</td>' +
                        '   <td>'+llq_title+'</td>' +
                        '   <td><img src="'+ llq_icon +'" width="35" /></td>' +
                        '   <td>'+llq_create_time+'</td>' +
                        '   <td>'+llq_pv+'</td>' +
                        '   <td>'+llq_clickNum+'</td>' +
                        '   <td class="dropdown-td">' +
                        '       <div class="dropdown">' +
                        '    	    <button type="button" class="dropdown-btn" data-toggle="dropdown">•••</button>' +
                        '           <div class="dropdown-menu">' +
                        '               <span class="dropdown-item" data-toggle="modal" data-target="#ShareJwModal" onclick="shareJw('+llq_id+')">分享</span>' +
                        '               <span class="dropdown-item" data-toggle="modal" data-target="#editJwModal" onclick="getJwInfo('+llq_id+')">编辑</span>' +
                        '               <span class="dropdown-item" data-toggle="modal" data-target="#delJwModal" onclick="askDelJw('+llq_id+')">删除</span>' +
                        '           </div>' +
                        '       </div>' +
                        '   </td>' +
                        '</tr>'
                    );
                    $("#right .data-list tbody").append($tbody_HTML);
                }
                
                // 分页组件
                fenyeComponent(res.page,res.allpage,res.nextpage,res.prepage);
                
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
        errorPage('data-list','getZjyList.php');
      },
    });
}

// 分页组件
function fenyeComponent(thisPage,allPage,nextPage,prePage){
    
    if(thisPage == 1 && allPage == 1){
        
        // 当前页码=1 且 总页码=1
        // 无需显示分页控件
        $("#right .data-card .fenye").css("display","none");
        
    }else if(thisPage == 1 && allPage > 1){
        
        // 当前页码=1 且 总页码>1
        // 代表还有下一页
        var $fenyeComponent_HTML = $(
        '<ul>' +
        '   <li>'+ 
        '       <button id="'+nextPage+'" onclick="getFenye(this);" title="下一页">'+ 
        '           <img src="../../static/img/nextPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '   <li>'+ 
        '       <button id="'+allPage+'" onclick="getFenye(this);" title="最后一页">'+ 
        '           <img src="../../static/img/lastPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '</ul>'
        );
        $("#right .data-card .fenye").css("display","block");
        
    }else if(thisPage == allPage){
        
        // 当前页码=总页码
        // 代表这是最后一页
        var $fenyeComponent_HTML = $(
        '<ul>' +
        '   <li>'+ 
        '       <button id="1" onclick="getFenye(this);" title="第一页">'+ 
        '           <img src="../../static/img/firstPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '   <li>'+ 
        '   <button id="'+prePage+'" onclick="getFenye(this);" title="上一页">'+ 
        '       <img src="../../static/img/prevPage.png" />'+ 
        '   </button>'+ 
        '   </li>' +
        '</ul>'
        );
        $("#right .data-card .fenye").css("display","block");
        
    }else{
        
        var $fenyeComponent_HTML = $(
        '<ul>' +
        '   <li>'+ 
        '       <button id="1" onclick="getFenye(this);" title="第一页">'+ 
        '           <img src="../../static/img/firstPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '   <li>'+ 
        '       <button id="'+prePage+'" onclick="getFenye(this);" title="上一页">'+ 
        '           <img src="../../static/img/prevPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '   <li>'+ 
        '       <button id="'+nextPage+'" onclick="getFenye(this);" title="下一页">'+ 
        '           <img src="../../static/img/nextPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '   <li>'+ 
        '       <button id="'+allPage+'" onclick="getFenye(this);" title="最后一页">'+ 
        '           <img src="../../static/img/lastPage.png" />'+ 
        '       </button>'+ 
        '   </li>' +
        '</ul>'
        );
        $("#right .data-card .fenye").css("display","block");
        
    }
    
    // 渲染分页组件
    $("#right .data-card .fenye").html($fenyeComponent_HTML);
}

// 获取分页数据
function getFenye(e){
    
    // 页码
    var pageNum = e.id;
    
    // 获取该页列表
    getJwList(pageNum);
}

// 上传文件（创建）
document.addEventListener('DOMContentLoaded', function() {
    
    // 选择素材
    $("#up1").change(function(e) {
        
        // 获取选择的文件
        var fileSelect = e.target.files;
        
        if (fileSelect.length > 0) {
 
            // 获取表单选中的数据
            var imageData = new FormData(document.getElementById("createJw"));
            
            // 上传图标
            uploadIcon(imageData);
        }
        
    });
    
    // 选择素材
    $("#up2").change(function(e) {
        
        // 获取选择的文件
        var fileSelect = e.target.files;
        
        if (fileSelect.length > 0) {
 
            // 获取表单选中的数据
            var imageData = new FormData(document.getElementById("createJw"));
            
            // 上传图标
            uploadbgimg(imageData);
        }
        
    });
    
    // 上传图标
    function uploadIcon(imageData){

        $.ajax({
            url: "./upload1.php",
            type: "POST",
            data: imageData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
                
                if(res.code == 200){
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_1").text('重新上传');
                    
                    // 设置表单Url
                    $('#createJwModal input[name="llq_icon"]').val(res.url);
                }else{
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_1").text('上传失败');
                }
            },
            error: function() {
                
                // 上传失败
                $("#chooseIMG_1").text('上传失败');
            },
            beforeSend: function(res){
                
                // 修改上传按钮文字
                $("#chooseIMG_1").text('上传中...');
            }
        });
    }
    
    // 上传背景
    function uploadbgimg(imageData){

        $.ajax({
            url: "./upload2.php",
            type: "POST",
            data: imageData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
                
                if(res.code == 200){
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_2").text('重新上传');
                    
                    // 设置表单Url
                    $('#createJwModal input[name="llq_bgimg"]').val(res.url);
                }else{
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_2").text('上传失败');
                }
            },
            error: function() {
                
                // 上传失败
                $("#chooseIMG_2").text('上传失败');
            },
            beforeSend: function(res){
                
                // 修改上传按钮文字
                $("#chooseIMG_2").text('上传中...');
            }
        });
    }
    
    $("#up1").val('');
    $("#up2").val('');
    $("#createJwModal input[name='file1']").val('');
    $("#createJwModal input[name='file2']").val('');
})

// 上传文件（编辑）
document.addEventListener('DOMContentLoaded', function() {
    
    // 选择素材
    $("#up3").change(function(e) {
        
        // 获取选择的文件
        var fileSelect = e.target.files;
        
        if (fileSelect.length > 0) {
 
            // 获取表单选中的数据
            var imageData = new FormData(document.getElementById("editJw"));
            
            // 上传图标
            uploadIcon(imageData);
        }
        
    });
    
    // 选择素材
    $("#up4").change(function(e) {
        
        // 获取选择的文件
        var fileSelect = e.target.files;
        
        if (fileSelect.length > 0) {
 
            // 获取表单选中的数据
            var imageData = new FormData(document.getElementById("editJw"));
            
            // 上传图标
            uploadbgimg(imageData);
        }
        
    });
    
    // 上传图标
    function uploadIcon(imageData){

        $.ajax({
            url: "./upload1.php",
            type: "POST",
            data: imageData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
                
                if(res.code == 200){
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_3").text('重新上传');
                    
                    // 设置表单Url
                    $('#editJwModal input[name="llq_icon"]').val(res.url);
                }else{
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_3").text('上传失败');
                }
            },
            error: function() {
                
                // 上传失败
                $("#chooseIMG_3").text('上传失败');
            },
            beforeSend: function(res){
                
                // 修改上传按钮文字
                $("#chooseIMG_3").text('上传中...');
            }
        });
    }
    
    // 上传背景
    function uploadbgimg(imageData){

        $.ajax({
            url: "./upload2.php",
            type: "POST",
            data: imageData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
                
                if(res.code == 200){
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_4").text('重新上传');
                    
                    // 设置表单Url
                    $('#editJwModal input[name="llq_bgimg"]').val(res.url);
                }else{
                    
                    // 修改上传按钮文字
                    $("#chooseIMG_4").text('上传失败');
                }
            },
            error: function() {
                
                // 上传失败
                $("#chooseIMG_4").text('上传失败');
            },
            beforeSend: function(res){
                
                // 修改上传按钮文字
                $("#chooseIMG_4").text('上传中...');
            }
        });
    }
    
    $("#up3").val('');
    $("#up4").val('');
    $("#editJwModal input[name='file1']").val('');
    $("#editJwModal input[name='file2']").val('');
})

// 创建抖音跳微信跳转卡
function createJw(){
    
    $.ajax({
        type: "POST",
        url: "./createJw.php",
        data: $('#createJw').serialize(),
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 操作反馈（操作成功）
                showSuccessResult(res.msg)
                
                // 隐藏modal
                setTimeout('hideModal("createJwModal")', 500);
                
                // 重新加载列表
                setTimeout('getJwList();', 500);
            }else{
                
                // 操作反馈（操作失败）
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('createJw.php');
        }
    });
}

// 编辑跳转卡
function editJw(){
    
    $.ajax({
        type: "POST",
        url: "./editJw.php",
        data: $('#editJw').serialize(),
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 成功
                showSuccessResult(res.msg)
                
                // 隐藏Modal
                setTimeout('hideModal("editJwModal")', 500);
                
                // 重新加载列表
                setTimeout('getJwList();', 500);
            }else{
                
                // 失败
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('editJw.php');
        }
    });
}

// 询问是否要删除
function askDelJw(jwid){
    
    // 将群id添加到button的
    // delJw函数用于传参执行删除
    $('#delJwModal .modal-footer').html(
        '<button type="button" class="default-btn" onclick="delJw('+jwid+');">确定删除</button>'
    )
}

// 删除
function delJw(jwid){

    $.ajax({
        type: "GET",
        url: "./delJw.php?jwid=" + jwid,
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 隐藏Modal
                hideModal("delJwModal");
                
                // 重新加载列表
                setTimeout('getJwList()', 500);
                
                // 显示删除结果
                setTimeout('showNotification("'+res.msg+'")', 600);
            }else{
                
                // 失败
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('delJw.php');
        }
    });
}

// 获取跳转卡详情
function getJwInfo(llq_id){
    
    $.ajax({
        type: "GET",
        url: "./getJwInfo.php?llq_id="+llq_id,
        success: function(res){

            if(res.code == 200){
                
                // 标题
                $('#editJwModal input[name="llq_title"]').val(res.jwInfo.llq_title);
                
                // 获取域名列表
                getDomainNameList('edit');
                
                // 获取当前设置的域名
                $('#editJwModal select[name="llq_yccym"]').append(
                    '<option value="'+res.jwInfo.llq_yccym+'">'+res.jwInfo.llq_yccym+'</option>'
                );
                
                // 图标
                $('#editJwModal input[name="llq_icon"]').val(res.jwInfo.llq_icon);
                
                // 背景图片
                $('#editJwModal input[name="llq_bgimg"]').val(res.jwInfo.llq_bgimg);
                
                // 目标链接
                $('#editJwModal input[name="llq_url"]').val(res.jwInfo.llq_url);
                
                // ID
                $('#editJwModal input[name="llq_id"]').val(res.jwInfo.llq_id);
                            
            }else{
                
                // 失败
                showErrorResult(res.msg)
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('getJwInfo.php');
        }
    });
}

// 使用 appendOptionsToSelect函数来为每个select元素处理选项的添加
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
                
                // 创建
                appendOptionsToSelect($("#createJwModal select[name='llq_yccym']"), res.yccymList);
                
                // 编辑
                appendOptionsToSelect($("#editJwModal select[name='llq_yccym']"), res.yccymList);
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

// 分享跳转卡
function shareJw(jwid){
    
    // 初始化二维码
    $("#shareQrcode").html('');

    $.ajax({
        type: "GET",
        url: "./shareJw.php?llq_id="+jwid,
        success: function(res){
            
            // 成功
            if(res.code == 200){
                
                // 长链接
                //$("#longUrl").html('<span id="llq_'+jwid+'">' + res.longUrl + '&token=' + res.llq_token + '&f=douyin&by=ylb&t=' + res.t + '</span>');
                // 长链接//友源科技2024-01-02修改
                $("#longUrl").html('<span id="llq_'+jwid+'">' + res.longUrl + '</span>');
                
                // 二维码
                new QRCode(document.getElementById("shareQrcode"), res.qrcodeUrl);
                
                // 复制按钮
                $('#ShareJwModal .modal-footer').html(
                    '<button class="default-btn" data-clipboard-action="copy" data-clipboard-target="#llq_'+jwid+'">复制链接</button>'
                );
            }else{
                
                // 失败
                showErrorResult(res.msg);
            }
        },
        error: function() {
            
            // 服务器发生错误
            showErrorResultForphpfileName('shareJw.php');
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

// 设置URL路由
function setRouter(pageNum){
    
    // 第一页不设置
    if(pageNum !== 1){
        
        // 根据页码+token设置路由
        window.history.pushState('', '', '?p='+pageNum+'&token='+creatPageToken(32));
    }
}

// 隐藏全局信息提示弹出提示
function hideNotification() {
	var $notificationContainer = $('#notification');
	$notificationContainer.css('top', '-100px');
}

// 隐藏Modal（传入节点id决定隐藏哪个Modal）
function hideModal(modal_Id){
    $('#'+modal_Id+'').modal('hide');
}

// 显示Modal（传入节点id决定隐藏哪个Modal）
function showModal(modal_Id){
    $('#'+modal_Id+'').modal('show');
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

// 暂无数据
function noData(text){
    
    $("#right .data-list").css('display','none');
    $("#right .data-card .loading").html(
    '<img src="../../static/img/noData.png" class="noData" /><br/>' +
    '<p class="noDataText">'+text+'</p>'
    );
    $("#right .data-card .loading").css('display','block');
}

// 初始化（getJwList获取中间页列表）
function initialize_getJwList(){
    $("#right .data-list").css('display','block');
    $("#right .data-card .loading").css('display','none');
    $("#right .data-list tbody").empty('');
}

// 初始化（获取域名列表）
function initialize_getDomainNameList(module){
    
    if(module == 'create'){
        $('#createJwModal input[name="llq_title"]').val('');
        $('#createJwModal input[name="llq_icon"]').val('');
        $('#createJwModal input[name="llq_bgimg"]').val('');
        $('#createJwModal input[name="llq_url"]').val('');
        $("#chooseIMG_1").text('上传图片');
        $("#chooseIMG_2").text('上传图片');
        $('#createJwModal select[name="llq_yccym"]').empty();
        hideResult();

    }else if(module == 'edit'){
        $('#editJwModal select[name="llq_yccym"]').empty();
        hideResult();
    }
}

// 跳转到指定路径
function jumpUrl(jumpUrl){
    
    // 1秒后跳转至jumpUrl
    setTimeout('location.href="'+jumpUrl+'"',1000);
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