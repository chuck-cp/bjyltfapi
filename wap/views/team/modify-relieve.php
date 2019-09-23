<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>小组设置</title>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<link rel="stylesheet" href="/static/css/reset.css" />
<link rel="stylesheet" href="/static/css/sy_installteamset.css?v=1808161401" />
</head>
<body class="sy-body">
<div class="sy_installset">
	 <div class="list">
	 	<p class="lf">小组名称</p>
	 	<p class="rh"><input type="text" value="小组名称" id="rowname"></p>
	 </div>	 
	 <div class="list">
	 	<p class="lf">组长姓名</p>
	 	<p class="rh" id="zuz">张三</p>
	 </div>	 
	 <div class="list">
	 	<p class="lf">联系方式</p>
	 	<p class="rh" id="lx">老王的小组</p>
	 </div>	 
<!--	 <div class="list">-->
<!--	 	<p class="lf">小组名称</p>-->
<!--	 	<p class="rh" id="zm">18899996666</p>-->
<!--	 </div>	 -->
	 <div class="list">
	 	<p class="lf">现住地址</p>
	 	<p class="rh" id="xianzhu_area">
        	<span class="txt select_dizhi" id="address"></span>
            <span class="tag"><img src="/static/image/sy_installteamtag.png"></span>
	 	</p>
	 </div>	
	 <!--详细地址注意：-->
	 <p class="half_address"><input id="xzxx" type="text" value="航丰路一号时代财富天地2015"></p>
	 <div class="list">
	 	<p class="lf">公司名称</p>
	 	<p class="rh" id="company"><input type="text" placeholder="未填写"></p>
	 </div>	 
	 <div class="list">
	 	<p class="lf">公司地址</p>
	 	<p class="rh"  id="gongsi_area">
	 	       <span class="txt select_dizhi" id="gsdz">未填写</span>
            <span class="tag"><img src="/static/image/sy_installteamtag.png"></span>
	 	</p>
	 </div>	
	 <!--详细地址注意：-->
	 <p class="half_address"><input type="text" id="detail" value="" placeholder="未填写"></p>
</div>
<a href="javascript:;" class="sy_insteamset_btn jiesan">解散小组</a>
<a href="javascript:;" class="sy_insteamset_btn modify">提交修改</a>
<!--mask遮罩层-->
<div class="mask"></div>
<!--解除团队后，所有成员将遣散是否解除？   弹框-->
<div class="sy_installset_doing">
	 <div class="img">
	 	  <img src="/static/image/sy_insteamset_panelbg.png">
	 	  <p>解除团队后，所有成员将遣散是否解除？</p>
	 </div>	 	 
	 <div class="btn clearfix">
	 	  <a href="javascript:;" class="qr">确认</a>
	 	  <a href="javascript:;" class="qx">取消</a>
	 </div>
</div>
<input type="hidden" name="" id="tid" value="">
<!--成员还有未完成的指派任务无法解除团队-->
<div class="sy_installset_choicetwo">
	 <div class="img">	 	  
	 	  <p>成员还有未完成的指派任务<br>无法解除团队</p>
	 	  <img class="bgimg" src="/static/image/sy_waitinstall_panelbgtwo.png">
	 	  <span class="sy_waitins_close"><img src="/static/image/sy_waitinstall_close.png"></span>
	 </div>	 
</div>
<!--选择省市区地区弹层-->
<section id="areaLayer" class="express-area-box">
    <header>
        <h3>选择地区</h3>
        <a id="backUp" class="back" href="javascript:void(0)" title="返回"></a>
        <a id="closeArea" class="close" href="javascript:void(0)" title="关闭"></a>
    </header>
    <article id="areaBox">
        <ul id="areaList" class="area-list"></ul>
    </article>
</section>

<!--遮罩层-->
<div id="areaMask" class="dzmask"></div>
<!--数据传参-->
<span id="chuaru_dz" style="display: none;"></span>
<input type="hidden" name="" id="xianzhu_id">
<input type="hidden" name="" id="gongsi_id">
<!--选择省市区地区弹层 end-->
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript"  src="/static/js/sy_installteampub.js" ></script>

<script>
//<!--解除团队后，所有成员将遣散是否解除？   弹框-->	

//关闭 closetankuan('sy_installset_doing');

//成员还有未完成的指派任务无法解除团队
//tankuangparam('sy_installset_choicetwo','fafafafffaf')
//关闭 closetankuan('sy_installset_choicetwo');
	
</script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token = "<?=\yii\helpers\Html::encode($token)?>";
    var team_id = "<?=$tid?>";
    var rote = baseApiUrl+'/team/'+team_id;
    /*获取团队信息*/
    $.ajax({
        url:rote,
        type:'GET',
        async:false,
        data:{'token':token},
        success:function (phpdata) {
            //console.log(phpdata);
            if(phpdata.status == 200){
                $('#rowname').val(phpdata.data.team_name);
                $('#zuz').html(phpdata.data.team_member_name);
                $('#lx').html(phpdata.data.team_member_mobile);
                $('#address').html(phpdata.data.live_area_name);
                $('#company input').val(phpdata.data.company_name);
                $('#gsdz').html(phpdata.data.company_area_name);
                $('#detail').html(phpdata.data.company_address);
                $('#tid').val(phpdata.data.id);
                $('#gongsi_id').val(phpdata.data.company_area_id);
                $('#xianzhu_id').val(phpdata.data.live_area_id);
                $('#xzxx').val(phpdata.data.live_address);
                $('#detail').val(phpdata.data.company_address);
                if(phpdata.data.status == 2){
                    //$('.jiesan').css('display','none');
                }
            }
        },error:function (phpdata) {
            
        }
    });
    /*解散团队*/
    $('.jiesan').click(function () {
        tankuang('sy_installset_doing');
    });
    $('.qr').click(function () {
        var tid = "<?=$tid?>";
        $.ajax({
            url:baseApiUrl+'/team/dismiss/'+tid+'?token='+token,
            type:'GET',
            async:true,
            success:function (phpdata) {
                if(phpdata.status == 200){
                    //tankuangparam('sy_installset_choicetwo','解散成功');
                    var ua = navigator.userAgent.toLowerCase();
                    if(/android/.test(ua)) {
                        var result = {"action":'modifyteam'};
                        window.jsObj.HtmlcallJava(JSON.stringify(result));
                    }else{
                        var result = {"action":'dissolveteam'};
                        webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
                    }
                }
                if(phpdata.message == 'TEAM_NOT_COMPLETE_TASK'){
                    closetankuan('sy_installset_doing');
                    tankuangparam('sy_installset_choicetwo','存在未完成的任务，不能解散');
                }
                if(phpdata.message == 'TEAM_ALREADY_DISSOLVE'){
                    closetankuan('sy_installset_doing');
                    tankuangparam('sy_installset_choicetwo','该团队已经解散成功！');
                }
            },error:function (phpdata) {

            }
        })
    })
    /*修改团队信息*/
    $('.modify').click(function () {
        var tid = $('#tid').val();
        var postData = {
            _method:"PUT",
            'team_name':$('#rowname').val(),
            'live_address':$('#xzxx').val(),
            'live_area_name': $('#address').html(),
            'company_name': $('#company input').val(),
            'company_area_name': $('#gsdz').html(),
            'company_address': $('#detail').val(),
            'id': tid,
            'company_area_id':$('#gongsi_id').val(),
            'live_area_id':$('#xianzhu_id').val(),
            'token':token,
        };
        var modify_url = baseApiUrl+'/team/'+tid;
        $.ajax({
            url:modify_url,
            data:postData,
            type:'POST',
            success:function (data) {
                if(data.status == 200){
                    tankuangparam('sy_installset_choicetwo','设置成功');
                    setTimeout(function () {
                        window.location.reload();
                    },1000);

                }
                if(data.message == 'TEAM_NAME_ALREADY_EXISTED'){
                    tankuangparam('sy_installset_choicetwo','该店铺名已存在');
                }
            }
        });
    })
    /*选择省市地区 */
    $(function (){
		$("#chuaru_dz").html(baseApiUrl);
        $("#xianzhu_area").click(function (){
            zxcs("xianzhu_area")
        })
        $("#gongsi_area").click(function (){
            zxcs("gongsi_area")
	})
    /*关闭提示框*/
    $('.sy_waitins_close').click(function () {
        closetankuan('sy_installset_choicetwo');
    })
    $('.qx').click(function () {
        closetankuan('sy_installset_doing');
    })
	
})
</script>
<!--选择地区js select_area-->
<script type="text/javascript" src="/static/js/select_area.js?v=0.1"></script>
</body>
</html>
