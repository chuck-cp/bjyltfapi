<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>玉龙传媒</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css">
    <link rel="stylesheet" type="text/css" href="/static/css/chanquan.css">
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
</head>
<body>
<!--头部-->
<div class="head">
    <div class="wrap">
        <div class="yx_logo">
            <img src="/static/images/logo_name.png">
        </div>
    </div>
</div>
<div class="wrap dingwe clearfix">
    <!--左侧菜单-->
    <div class="yx_left_menu fl">
        <!--用记信息-->
        <div class="yx_exit">
            <p class="yx_tcdl"><a href="/index/logout">退出登录</a></p>
            <p class="yx_photo"><img src="<?=\Yii::$app->user->identity->avatar;?>"></p>
            <p class="yx_name"><a href="javascript:;"><?=\Yii::$app->user->identity->name;?></a></p>
        </div>
        <!--菜单内容-->
        <ul class="yx_menu_nr">
            <li><span><img src="/static/images/tfgl.png"></span><a href="/delivery/index">投放素材</a></li>
            <li class="yx_gaoliang"><span><img src="/static/images/cqgl.png"></span><a href="/property/index">产权管理</a></li>
            <li><span><img src="/static/images/lsgl.png"></span><a href="/history/delivery">历史管理</a></li>
        </ul>    
    </div>
    <!--右侧菜单-->
    <div class="yx_right_menu fr">
       <div class="yx_cqgl_tjbj">
           <p class="sy_upload-p fl"><input type="file" class="Upload-imginput">+添加产权证明</p>
           
           <p class="yx_cqbianji fl" id="cq_edit" <?php if(empty($data)){?>  style="display:none; "  <?php }?> ><img src="/static/images/bianji.png" >编辑</p>
           
           <p class="yx_pldel fl" id="cq_pldel"><img src="/static/images/cqbj.png">批量删除</p>
           <p class="yx_pldel fl" id="cq_plsave"><img src="/static/images/cqbj.png">批量保存</p>
       </div>
        <input type="hidden" id="myFolder" value="<?php echo "/member/".\Yii::$app->user->id."/";?>">
      <div class="yx_upimg_list clearfix">
       <?php foreach($data as $k=>$v){ ?>
           <div class="yx_cq_lb_xq" data-cqid="<?php echo $v['id'] ?>">
              <p class="yx_cq_img"><img src="<?php echo $v['image_url']."?imageView2/0/w/280/h/200" ?>"></p>
              <p class="yx_cq_name"><?php echo $v['name'] ?></p>
              <p class="yx_cq_name_xg"><input type="text" value="<?php echo $v['name'] ?>" name="name" class="YX_bjcqbt" /></p>
              <p class="yx_bjcq_zt">
                 <span class="yx_del_cqimg fl"><img src="">删除</span>
                 <span class="yx_cq_bc fl"><img src="/static/images/cqbj.png">保存</span>
              </p>
              <!--点击编辑-->               
              <p class="yx_xz_bjcq"> 
                <input id="<?php echo $v['id'] ?>" type="checkbox" name="" class="yx_xz_bjcq_input">
                <span class="yx_xz_bjcq_false"></span>
              </p>
           </div>
       <?php } ?>
      </div>
        <div class="sy_tctskuan">
            <!-- 标题 -->
            <div class="sy_tctskuan_bt">
                <p class="fl" id="tsk_namet">提示</p>
            </div>
            <div class="sy_scttsk_nr" style="display: block;">
                <p>只能上传图片格式文件！</p>
                <p class="sy_tctskuan_an">
                    <button type="button"  class="sy_tjcq">确定</button>
                    <button type="button" class="sy_qxcq">取消</button>
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<!--高度自适应-->
<script type="text/javascript" src="/static/js/sc_js.js"></script>
<!--编辑产权-->
<script type="text/javascript" src="/static/js/cq_edit.js"></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
</script>
<!--上传图片-->
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
<script type="text/javascript" src="/static/js/chanquan-upload.js"></script>
<script>
 $(function(){
 	//上传图片
 	$('.Upload-imginput').click(function(){
	  uploadimg('Upload-imginput');	
          
    })
     $('.sy_tjcq').click(function(){
         $('.sy_tctskuan').hide();
     });
     $('.sy_qxcq').click(function(){
         $('.sy_tctskuan').hide();

     });
   
 })
</script>
<script>
 $(function(){
     
    //修改名称 保存 
    $(".yx_cq_bc").live("click",function(){
       var xg_name=$(this).parent().siblings(".yx_cq_name_xg").find(".YX_bjcqbt").val();
      $(this).parent().siblings(".yx_cq_name").html(xg_name);
      $(this).parent().siblings(".yx_cq_name_xg").find(".YX_bjcqbt").attr("value",xg_name);
    	//初始化
    	$(".yx_xz_bjcq").hide();
    	$(".yx_cq_name_xg").hide();
    	$(".yx_cq_name").show();
    	$(".yx_bjcq_zt").hide();	
    	$(".yx_pldel").hide();
      var cqid=$(this).parents(".yx_cq_lb_xq").attr("data-cqid");
          $.ajax({url:"/property/modifyname",type:"post",data:{id:cqid,name:xg_name},
              success:function(data){
                  
              }
          });	
      })
    //选择删除框样式  批量删除
    var plid=[];
    
    $(".yx_xz_bjcq_input").live("click",function(){
        if($(this).attr("checked") == "checked"){
            $(this).siblings("span").attr("class","yx_xz_bjcq_true");
            
            plid.push($(this).parents(".yx_cq_lb_xq").attr("data-cqid"));
            
        }else{
            $(this).siblings("span").attr("class","yx_xz_bjcq_false")
        }
		
    })
    //批量删除
    $("#cq_pldel").click(function (){
        //alert(plid);return false;
        $.ajax({url:"/property/delall",type:"post",data:{ids:plid},
            success:function(data){
                data=eval("("+data+")");
                $.each(data,function(i,v){
                    $(".yx_upimg_list").find("div[data-cqid="+v+"]").hide();
                });
                
                //初始化
                $(".yx_xz_bjcq").hide();
                $(".yx_cq_name_xg").hide();
                $(".yx_cq_name").show();
                $(".yx_bjcq_zt").hide();
                $(".yx_pldel").hide();	
                //初始分数组
                plid=[];
                location.reload() ;

            }
        });
    })
    
    //删除
	$(".yx_del_cqimg").live("click",function(){
		var cqid=$(this).parents(".yx_cq_lb_xq").attr("data-cqid");
                $.ajax({
                    url:"/property/delid",
                    type:"post",
                    data:{id:cqid},
                    success:function(data){
                        if(data){
                            //初始化
                            $(".yx_xz_bjcq").hide();
                            $(".yx_cq_name_xg").hide();
                            $(".yx_cq_name").show();
                            $(".yx_bjcq_zt").hide();
                            $(".yx_pldel").hide();                           
                            $(".yx_upimg_list").find("div[data-cqid="+cqid+"]").hide();
                            location.reload() ;
                        }
                    }
                });
		
	})
    var savedata = [];
    //名称框变化事件
     $(".YX_bjcqbt").live("change",function(){
        var cid = $(this).parents(".yx_cq_lb_xq").attr("data-cqid");
        var cname = $(this).val();
        savedata.push([cid,cname]);
    })
    //批量保存
    $("#cq_plsave").click(function (){
        $.ajax({url:"/property/saveall",type:"post",data:{savedata:savedata},
            success:function(data){
                //初始化
                $(".yx_xz_bjcq").hide();
                $(".yx_cq_name_xg").hide();
                $(".yx_cq_name").show();
                $(".yx_bjcq_zt").hide();
                $(".yx_pldel").hide();	
                //初始分数组
                savedata=[];
                location.reload() ;

            }
        });
    })
 })
</script>
</html>