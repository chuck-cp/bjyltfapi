<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>LED屏申请</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css?v=1.1"/>
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css?v=?v=201808231911"/>
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css"/>
    <style>
        /*视频安装协议*/
        .sy_install_protocol{display: none; width: 94%; padding: 10px 0; box-sizing: border-box; height: 94%;background:rgba(220,220,220,1); position:fixed; left: 3%; top: 3%; z-index: 99;}
        .sy_dzmask{display: none; position:fixed; top: 0; left: 0; z-index: 12; width: 100%; height: 100%; background-color: rgba(0,0,0,.4);}
        .sy_con_scroll{ height:100%;overflow-y: scroll; padding: 0 10px;}
        .sy_install_protocol .title{ text-align: center; font-size: 18px; color: #333; padding: 10px;}
        .sy_install_protocol .content{ color: #666; font-size: 14px; line-height: 26px; padding-bottom: 50px;}
        .white-mask{ position: absolute; left: 0; bottom: 0; width: 100%; height: 25%;
            background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 0%,rgba(255, 255, 255, 0.2) 20%, rgba(255, 255, 255, 0.4) 40%,rgba(255, 255, 255, 0.4) 60%,rgba(255, 255, 255, 0.5) 60%, rgba(255, 255, 255, 0.6) 98%, rgba(255, 255, 255, 0.6) 100%); }
        .sy_install_btn{ position: fixed;left: 0; bottom:4%; left: 5%; width: 90%;}
        .sy_install_btn button{ width: 40%; height: 40px; line-height: 40px; display:inline-block; border: 1px solid #151515; margin: 0 5% 5px; float: left;}
        .sy_install_btn button:first-child{ background:rgba(220,220,220,0.9); border: 1px solid #151515; color: #333;}
        .sy_install_btn button:last-child{ background: #ee8303; color: #fff; border: 1px solid #ee8303;}
        .display_none{display: none}
        .apply_brokerage_price{ border-bottom:  1px solid #000000; padding: 0 5px;}
        /*安装地址样式补充*/
        .yx_srnr_dqu{ width:100%; height:15px;color:#b2b2b2; border:none; padding-top: 5px;}
        .yx_srnr_dqu{ display: block; line-height:30px; font-style:normal;height:35px;}
        .sc_fail_ts{ position: absolute; left: 0; top: 10px; color: #c03; text-align: center; width: 100%;}
        .sy_color333{ color: #333;}
        .yx_tj{
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div>
    <div class="yx_spxx">
        <form id="idform">
            <!--<div class=""><label>用户名:</label><input type="text" datatype="*" nullmsg="申请人姓名不能为空"  ></div>-->
            <!--个人信息--->
            <h5>个人信息</h5>
            <dl>
                <dt><span>法人代表</span></dt>
                <dd><input id="apply_name" autocomplete="off" class="yx_srnr" nullmsg="nameblank" type="text" datatype="*" placeholder="填写法人代表" ></dd>
            </dl>
            <dl>
                <dt>身份证号码</dt>
                <dd><input maxlength="18" id="identity_card_num" autocomplete="off" class="yx_srnr" type="text" nullmsg="idblank" datatype="IDcard" errormsg="errorid" placeholder="请填写申请人身份证号码" ></dd>
            </dl>
            <dl>
                <dt>手机号码</dt>
                <dd>
                        <input oninput="if(value.length>11)value=value.slice(0,5)" id="apply_mobile" autocomplete="off" class="yx_srnr" readonly="readonly" type="number" nullmsg="numbphone" datatype="phone" errormsg="errorphone" placeholder="请填写申请人手机号码" >
                </dd>
            </dl>

            <style>
                .yx-sendyzm .send-yzm{
                    height: 40px;
                }
            </style>

            <!--店铺信息--->
            <h5>公司信息</h5>
            <dl>
                <dt>公司名称</dt>
                <dd><input id="company_name" autocomplete="off" class="yx_srnr" type="text" placeholder="请输入公司名称" nullmsg="companyname" datatype="*"></dd>
            </dl>

            <dl class="sy_smallscreen">
                <dt>统一社会信用代码</dt>
                <dd><input id="registration_mark" autocomplete="off" class="yx_srnr" type="text" placeholder="请输入营业执照统一社会信用代码" nullmsg="busneyzm" datatype="*"></dd>
            </dl>
            <dl class="yx_anzdz">
                <dt>公司地址</dt>
                <dd>
                    <p class="yx_dzxq azdz" >
                        <input type="hidden" id="area" placeholder="请选择地区" nullmsg="installarea" datatype="*" value="">
                        <cite id="gongsi_dz" class="yx_srnr_dqu sy_color333">请选择地区</cite>
                    </p>
                    <p class="yx_dzxq" id="xxdz"><input autocomplete="off" id="address" class="yx_srnr" type="text" placeholder="请填写详细地址" nullmsg="installaddress" datatype="*"></p>
                </dd>
            </dl>

            <!--上传照片--->
            <h5>上传照片</h5>
            <div class="yx_shangc">
                <div class="yx_sc_img">
                    <p class="yx_scbt">请按示例上传清晰的身份证照片 <span class="yx_scimg_sl sy_tkxgo"><img src="/static/image/xlimg.png">示例</span></p>
                    <div class="yx_sc_img">
                        <p class="yx_scbt">法人代表:</p>
                        <div class="upload" id="upload1">
                            <input id="identity_card_front" class="update_input" type="hidden" placeholder="请上传法人身份证正面照" nullmsg="identity_card_front" datatype="*">
                            <img class="imgspace" src="/static/image/blank.jpg">
                            <img class="addimg" src="/static/image/uploadimg-add.png">
<!--                            <p class="idcardzfti">身份证正面</p>-->
                            <?if ($dev == 'ios'):?>
                                <p class="Upload-imginput" id="up1"></p>
                            <? else:?>
                                <input type="file" class="Upload-imginput" accept="image/*" id="up1">
                            <?endif;?>
                            <div class="progress-bar" step="0"></div>
                        </div>
                        <div class="upload" id="upload2">
                            <input id="identity_card_back" class="update_input" type="hidden" placeholder="请上传法人身份证背面照" nullmsg="identity_card_back" datatype="*">
                            <img class="imgspace" src="/static/image/blank.jpg">
                            <img class="addimg" src="/static/image/uploadimg-add.png">
<!--                            <p class="idcardzfti">身份证反面</p>-->
                            <?if ($dev == 'ios'):?>
                                <p class="Upload-imginput" id="up2"></p>
                            <? else:?>
                                <input type="file" class="Upload-imginput" accept="image/*" id="up2">
                            <?endif;?>
                            <div class="progress-bar" step="0"></div>
                        </div>
                    </div>

                    <div class="yx_sc_img">
                        <p class="yx_scbt">请上传清晰的营业执照<span class="yx_scimg_sl sy_tkxgt"><img src="/static/image/xlimg.png">示例</span></p>
                        <div>
                            <div class="upload" id="screen5">
                                <input id="business_licence" class="update_input" type="hidden" placeholder="请上传营业执照图片" nullmsg="business_licence" datatype="*">
                                <img class="imgspace" src="/static/image/blank.jpg">
                                <img class="addimg" src="/static/image/uploadimg-add.png">
                                <?if ($dev == 'ios'):?>
                                    <p class="Upload-imginput" id="up5"></p>
                                <? else:?>
                                    <input type="file" class="Upload-imginput" accept="image/*" id="up5">
                                <?endif;?>
                                <div class="progress-bar" step="0"></div>
                            </div>
                        </div>
                    </div>
                </div>






                <div class="yx_sc_img">
                    <p class="yx_scbt">其他(选填,最多填写五张)</p>
                    <div id="upload_other_image" class="sy_authorcertif">
                        <div class="upload" id="screen15" style="display: none">
                            <input class="update_input" id="other_image1" type="hidden">
                            <img class="imgspace" src="/static/image/blank.jpg">
                            <img class="addimg" src="/static/image/uploadimg-add.png">
                            <?if ($dev == 'ios'):?>
                                <p class="Upload-imginput" id="up15"></p>
                            <? else:?>
                                <input type="file" class="Upload-imginput" accept="image/*" id="up15">
                            <?endif;?>
                            <div class="progress-bar" step="0"></div>
                            <p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p>
                        </div>



                        <p class="syadd_upload_pic" name="other_upload_image">
                            <img class="imgspace" src="/static/image/blank.jpg">
                            <img class="img" src="/static/image/sy_sqfdadd.png">
                        </p>
                    </div>
                </div>








                <h5>分店信息</h5>
                <div class="sy_fendianlist branch">
                    <div class="each">
                        <dl>
                            <dt>分店名称</dt>
                            <dd>
                                <input id="" autocomplete="off" class="sy_yx_srnr" type="text" placeholder="请输入分店名称" nullmsg="companyname" datatype="*">
                                <span class="sy_addspan"><img class="afimg" src="/static/image/sy_sqfdadd.png"></span>
                            </dd>
                        </dl>
                        <dl class="yx_anzdz">
                            <dt>分店地址</dt>
                            <dd>
                                <p class="yx_dzxq azdz">
                                    <input type="hidden" class="br_area_id" id="area" placeholder="请选择地区" nullmsg="installarea" datatype="*" value="">
                                    <cite  class="yx_srnr_dqu sy_color333">请选择地区</cite>
                                </p>
                                <p class="yx_dzxq" id="xxdz"><input autocomplete="off" id="" class="yx_srnr" type="text" placeholder="请填写详细地址" nullmsg="installaddress" datatype="*"></p>
                                <span class="sy_addspan_red"><img src="/static/images/sy_sqfdreduce.png"></span>
                            </dd>
                        </dl>
                    </div>
                </div>


                <div id="areaMask" class="dzmask"></div>
                <!--                <input type="hidden" id="apply_brokerage" value="0" name="apply_brokerage" nullmsg="apply_brokerage" datatype="*">-->
                <!--                <input type="hidden" id="apply_brokerage_token" name="apply_brokerage_token" nullmsg="apply_brokerage_token" datatype="*">-->
                <p id="fdnum">共<span> 1 </span>家分店信息</p>
                <p class="yx_tj"> <button  type="submit" class="yx_tjsc_c" >提交申请</button></p>
            </div>
        </form>
    </div>
    <div class="mask"></div>
    <!--身份证提示弹框-->
    <div class="yx_tcylimg" id="ylbigimg">
        <h5>说明</h5>
        <ul class="yx_sm_nr">
            <li>1、证件信息清晰可见</li>
            <li>2、证件照片不要经过软件处理</li>
            <li>3、身份证信息我们将进行保密处理，仅用于本次<br/>
                &nbsp;活动
            </li>
        </ul>
        <p class="yx_shili">示例</p>
        <div class="yx_sz_img">
            <img src="/static/image/yssfzzf.png">
        </div>
        <p class="yx_guanbi"> <button type="submit" class="yx_gbylan" >关闭</button></p>
    </div>
    <!--营业执照提示弹框-->
    <div class="yx_tcylimg" id="ylbigimgt">
        <h5>说明</h5>
        <ul class="yx_sm_nr">
            <li>1、营业执照信息清晰可见</li>
            <li>2、不要经过软件处理</li>
            <li>3、营业执照信息将进行保密处理，仅用于设备<br/>
                &nbsp;申请
            </li>
        </ul>
        <p class="yx_shili">示例</p>
        <div class="yx_sz_img">
            <img src="/static/image/syyyzzsl.jpg">
        </div>
        <p class="yx_guanbi"> <button type="submit" class="yx_gbylan" >关闭</button></p>
    </div>
    <!--提交失败提示-->
    <p class="sy-installed-ts">提交失败</p>
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
    <!--正在提交-->
    <div class="zztj_zz"></div>
    <p class="yx_zztj" id="zztj">
        正在提交<img src="/static/image/loading.gif">
    </p>
    <!--遮罩层-->
    <div  class="sy_dzmask"></div>
    <!--视屏播放设备安装协议-->
    <div class="sy_install_protocol">
        <div class="sy_con_scroll">
            <h3 class="title">视频播放设备安装协议</h3>
            <div class="content">
                <span class="apply_brokerage_price" id="month_price" style="display: none"></span><span class="apply_brokerage_price" id="apply_brokerage_price" style="display: none"></span>
                <p>以下为协议条款，如您勾选"同意"则表示您已经同意了该协议的所有条款。</p>
                <p>1.您同意由北京玉龙腾飞影视传媒有限公司（以下简称玉龙传媒或我公司）买断在您的理发店独家安装视频播放设备权，并对其进行独立经营，视频播放内容由我公司决定。</p>
                <p>2.您同意本协议中所出现的视频播放设备指的是通过终端软件控制、网络信息传输和多媒体终端显示构成一个完整的视频或者图片等播控系统，并通过图片、文字、视频、小插件（天气、汇率等）等多媒体素材进行播放的设备统称。您同意楼宇广告机、网络广告机、户外液晶广告机、led显示屏、电视广告机、dlp无拼接大屏、触屏显示器、超窄边液晶显示屏等具有和我公司设备同样或类似功能的投放设备均属于视频播放设备。</p>
                <p>3.您同意"联系人/业务合作人"是指与我公司合作，同时和您建立联系并促成我公司和您签订本协议的自然人，"联系人/业务合作人"和我公司是合作关系，不是劳动或者雇佣关系；"联系人/业务合作人"只能在我公司授权范围内开展工作，尤其不能直接代表我公司以任何名义直接收取任何费用和款项。</p>
                <p>4.您同意买断指我公司买断您理发店独家安装视频播放设备的权利，并对其进行独立经营，视频播放内容以及播放时间由我公司决定。同时您同意我公司买断独家安装并经营视频播放设备的期限为五年， 五年合作期满后，在同等条件下，您应优先与我公司签订继续合作协议。</p>
                <p>5.您同意，如果您的类型为连锁店或者同一营业执照超过2家（含2家）理发店，那么您的权利义务参照本协议第1、2、3、4、5、6、7、8、9、10、11、12、13、14、15、16、18、19、20、21项，并且同时您须向我公司提供您下含所有店铺的信息，您同意此过程通过"玉龙传媒APP"实现，此部分内容作为协议的重要组成部分，与本协议具有同等法律效力，您提供的店铺信息以我公司后台实际数据为准；如果您的类型为单独店面，那么您的权利义务参照本协议第1、2、3、4、5、6、7、8、9、10、11、12、13、14、15、17、18、19、20、21项。</p>
                <p>6.您同意买断的条件为：<br>
                    1）我公司在您的理发店里安装视频播放，我公司给予您一定买断费用，买断费用包含店铺费用以及设备维护费用，依据您的理发店所在的区域对应的费用标准，第一年店铺费用一次性支付，第二年店铺费用我公司按月支付给您，；我公司会根据您对设备的维护情况给予一定额的维护费用，维护费用按月支付，具体金额以"玉龙传媒"APP结算为准，同时您认可该费用支付过程通过"玉龙传媒"APP进行，您的费用接收账户您的对公账户或您的法人代表的账户（如是租赁情况下为承租人的账户）。<br>
                    2）您同意我公司有权利对您的配合进行定期电话回访，回访内容以及周期由我公司决定，您有义务对我公司的行为进行配合。<br>
                    3）您认可我公司付费程序由系统控制。您必须保证设备开机时长不低于每天10小时，具体播放时间由我公司决定，否则，我公司有权利不予支付您当天的维护费用。<br>
                    4）您充分认可我公司的视频播放设备本身就是对您的理发店形象的提升，也是增强理发店用户体验的有效手段，对此，我公司不收取您的相应费用。
                </p>
                <p>7.您承诺当设备出现故障时，应及时联系我公司指定人员进行维修以确保设备正常运行。如只涉及调整遥控器和简单的接通电源就可解决的问题，您同意这是您随手的分内工作，可以自行处理；如涉及需要设备寄回返厂的操作，您应配合我公司按照我公司提供的信息将设备寄回，邮寄费用由我公司承担。</p>
                <p>您充分理解我公司布局一个理发店的视频播放设备其成本远高于设备本身的价格、安装与运输的价格、买断安装权的费用和中介推荐人的报酬。您如有违约理应赔偿：<br>
                    1）您同意并承诺设备因人为原因出现损坏致无法使用以及视频设备遗失的，您需要按照该设备成本价值（包括软件系统）以及安装费用（合计为1850元人民币/块）的一定比例赔偿我公司，第一年的比例为80%，第二年为50%，第三年为25%，第四年为10%。<br>
                    2）您同意并承诺当您的理发店由于自身原因需要重新装修或者搬迁时，您会保管好我公司设备不致损坏或者遗失，待装修或者搬迁结束时及时联系我公司人员进行重新安装。如致设备损坏或者遗失，您需按照在理发店重新装修或者搬迁时的设备价值（包括软件系统）以及安装费用的一定比例赔偿我公司，第一年是成本价的80%，第二年为50%，第三年为25%，第四年为10%。如未及时联系我公司人员进行重新安装还须退还我公司已支付给您的一切费用。
                </p>
                <p>9. 本协议为不可撤销的排他性的独家买断协议，您同意并承诺和我公司合作期间不再接受其他视频媒体的安装与使用，如接受了其他视频媒体的安装与使用属于严重违约，须双倍赔偿我公司给予您的一切费用，以及双倍赔偿设备成本价值、安装费用以及和设备相关的运营业务成本管理费用。除此之外，还须即刻撤掉其他公司或者所有者视频播放设备，同时换置为我公司视频播放设备，不得以任何理由拒绝。</p>
                <p>10. 您同意在我公司在线申请平台中您自身填写或上传的内容为本协议的有效组成部分并具有法律效力。</p>
                <p>11. 您同意在本协议生效后因自身原因不能实现我公司设备安装，您需要赔偿我公司设备往来运输费用以及安装人员费用。</p>
                <p>12. 您同意我公司在您的理发店安装视频播放设备这一行为是依据您当初注册申请而执行的， 我公司支付给您的相应款项也是依据您在注册申请中填写的费用接收信息进行支付的，只要我公司视频播放设备在您的理发店安装，就是您对该协议的事实执行，以及对注册申请人和费用接收人作为您的店铺联系人身份的认可。</p>
                <p>13. 您同意如果因我公司原因连续6个自然月未向您支付买断费用，双方认可本协议自动解除，我公司不再对视频设备进行收回，作为对您买断费用的完全补偿，我公司和您双方互不追究责任。</p>
                <p>14. 您同意该理发店法人代表为本协议的第一责任人，该理发店的店铺联系人为本协议的第二责任人（租赁情况下店铺联系人为第一责任人）。本协议自您注册当日起生效。</p>
                <p>15. 您同意向我公司提供的所有信息，以及您在"玉龙传媒"APP中的行为记录（包含但不限于视频设备安装数量、买断费用金额和支付方式等）都作为本协议的重要组成部分，具有法律效力。</p>
                <p>16. 您同意我公司支付的买断费用的受益人为您或者您的法定代表人，并且您下含所有受您管理的分支理发店对您以及您的法人代表的权利和义务已经充分认可。</p>
                <p>17. 您同意您的店铺联系人将此协议事项已经清晰告知您以及您的法定代表人，并且您以及您的法定代表人对店铺联系人在本协议中的权利和义务已经充分认可。</p>
                <p>18. 您同意我公司以"玉龙传媒" APP发放方式向您支付买断费用。具体领取日期和领取规则以"玉龙传媒"APP公示为准。</p>
                <p>19. 您同意和我公司买断费用的结算以我公司"玉龙传媒APP"实际数据为准。</p>
                <p>20. 您同意遇到纠纷时，双方应该本着平等互利、友好协商的原则进行协商解决，协商不成时向我公司注册地有管辖权的法院提起诉讼。</p>
                <p>21. 您同意您旗下的理发店，每台设备B屏广告位每20分钟10帧广告由您独立管理，广告为图片格式。<br>
                    1）您通过玉龙传媒APP自行操作上传广告内容，该广告位仅用于您合法产品的宣传与推广。<br>
                    2）您承诺对您上传的内容负责，上传的内容须符合国家法律法规（包括但不限于《广告法》），如果因您自身内容导致的一切损失由您独立承担；因您自身上传内容不当给我公司带来的一切损失，由您负责赔偿。<br>
                    3）如遇国家相关权力部门对您的广告内容质疑，您有义务立刻删除您的广告内容，同时我公司有权终止您使用该广告位的权利。
                </p>
                <p style="text-align: right;">北京玉龙腾飞影视传媒有限公司</p>
            </div>
        </div>
        <div class="white-mask">

        </div>
        <!--同意关闭按钮-->
        <div class="sy_install_btn">
            <button type="button">关闭</button>
            <button type="button">同意</button>
        </div>
    </div>
    <input type="hidden" id="h_id" value="">
    <!--删除弹框添加操作-->
    <div class="sy_removepanel">
    	 <p class="con">是否确认删除</p>
    	 <div class="btn">
    	 	  <a href="javascript:;" class="sy_remove_qx">取消</a> 
    	 	  <a href="javascript:;" class="sy_remove_qr">确定</a>
    	 </div>
    </div>
    <div id="tip"  style="width: 80%;text-align: center;position: fixed; top: 30%; left: 10%;z-index: 100;display: none">
        <p style="font-weight: bold;font-size: 16px;color: #ff7d09;padding: 44px 0;background: #fff;border-radius: 10px 10px 0 0;">安装修改申请提交成功！</p>
        <p id="achieve" style="background: linear-gradient(to right, #FF7D09 , #EF4E06);color: #fff;padding: 13px 0; border-radius: 0 0 10px 10px;">完成</p>
    </div>
    <script>
        var myFolder = '/member/<?=$member_id?>/';
        var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
        var type = "<?=$type?>";
    </script>
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
    <script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
    <script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
    <script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
    <script type="text/javascript" src="/static/js/img-upload.js?v=2.2"></script>
    <!--    <script type="text/javascript" src="/static/js/jquery.area.js?v=1.6"></script>-->
    <script type="text/javascript" src="/static/js/jquery.area_sec.js?v=1.9"></script>
    <script type="text/javascript" src="/static/js/select_time.js"></script>
    <script type="text/javascript" src="/static/js/swiper.min.js"></script>
    <script type="text/javascript" src="/static/js/sy_installteampub.js"></script>
    <script>
        //初始化分店数量
        $('.sy_addspan').live('click',function(){
            $('.sy_fendianlist').append(
                '<div class="each"><dl><dt>分店名称</dt><dd><input id="" autocomplete="off" class="sy_yx_srnr" type="text" placeholder="请输入分店名称" nullmsg="companyname" datatype="*"><span class="sy_addspan"><img src="/static/image/sy_sqfdadd.png"></span></dd></dl><dl class="yx_anzdz"><dt>分店地址</dt><dd><p class="yx_dzxq azdz"><input type="hidden" class="br_area_id" id="area" placeholder="请选择地区" nullmsg="installarea" datatype="*" value=""><cite class="yx_srnr_dqu">请选择地区</cite></p><p class="yx_dzxq" id="xxdz"><input autocomplete="off" id="" class="yx_srnr" type="text" placeholder="请填写详细地址" nullmsg="installaddress" datatype="*"></p><span class="sy_addspan_red"><img src="/static/images/sy_sqfdreduce.png"></span></dd></dl></div>')
            $(this).remove();
            //店铺数加1
            var numobj = $('#fdnum span').html();
            $('#fdnum span').html(parseInt(numobj) + 1);
        })
    </script>
    <script>   	
        /********************************************/
        //初始化信息
        $.ajax({
            url:baseApiUrl+'/shop-head/get-head-branches/'+'<?=$head_id?>'+'?token='+'<?=$token?>',
            type:'GET',
            async:true,
            success:function (phpdata) {
                //console.log(phpdata);
                //总店
                $('#h_id').val(phpdata.data.head.id);
                $('#apply_name').val(phpdata.data.head.name);
                $('#identity_card_num').val(phpdata.data.head.identity_card_num);
                $('#apply_mobile').val(phpdata.data.head.mobile);
                $('#company_name').val(phpdata.data.head.company_name);
                $('#registration_mark').val(phpdata.data.head.registration_mark);
                $('#upload1 .addimg').attr('src',phpdata.data.head.identity_card_front);
                $('#identity_card_front').val(phpdata.data.head.identity_card_front);
                $('#upload2 .addimg').attr('src',phpdata.data.head.identity_card_back);
                $('#identity_card_back').val(phpdata.data.head.identity_card_back);
                $('#screen5 .addimg').attr('src',phpdata.data.head.business_licence);
                $('#business_licence').val(phpdata.data.head.business_licence);
                $('#area').val(phpdata.data.head.company_area_id);
                $('#gongsi_dz').html(phpdata.data.head.company_area_name);
                $('#name').val(phpdata.data.head.company_name);
                $('#address').val(phpdata.data.head.company_address);
                //$('#apply_brokerage_price').html(phpdata.data.by_year/100);
                //$('#month_price').html(phpdata.data.by_month/100);
                //其他图片
//                <div class="upload" id="screen16">
//                <input class="update_input" id="other_image1" type="hidden">
//
//                <?//if ($dev == 'ios'):?>
//                <p class="Upload-imginput" id="up16"></p>
//                <?// else:?>
//                <input type="file" class="Upload-imginput" accept="image/*" id="up16">
//                <?//endif;?>
//                <div class="progress-bar" step="0"></div>
//                <p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p> </div>
                var other_html = '';
                var original = 16;
                var dev = '<?=$dev?>';
                var tag = '';
                if(phpdata.data.head.other_image){
                    var other_images = phpdata.data.head.other_image.split(',');
                    if(other_images.length > 4){
                        $('.syadd_upload_pic').hide();
                    }
                    $.each(other_images,function (index,value) {
                        if(dev == 'ios'){
                            tag = '<p class="Upload-imginput" id="up'+(original+index)+'"></p>';
                        }else{
                            tag = '<input type="file" class="Upload-imginput" accept="image/*" id="up'+(original+index)+'">';
                        }
                        other_html += '<div class="upload" id="screen'+(original+index)+'">' +
                                      '<input class="update_input" value="'+value+'" id="other_image'+(index+1)+'" type="hidden"><img class="imgspace" src="/static/image/blank.jpg"> <img class="addimg" src="'+value+'">'+tag+
                                  '<div class="progress-bar" step="0"></div><p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p> </div>';

                    })
                }else{
                    if(dev == 'ios'){
                        tag = '<p class="Upload-imginput" id="up1"></p>';
                    }else{
                        tag = '<input type="file" class="Upload-imginput" accept="image/*" id="up1">';
                    }
                    $('.sy_authorcertif').prepend('<div class="upload" id="screen16"><input class="update_input" id="other_image1" type="hidden"><img class="imgspace" src="/static/image/blank.jpg"><img class="addimg" src="/static/image/uploadimg-add.png">'+tag+'<div class="progress-bar" step="0"></div><p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p></div>');
                }
                $('#screen15').after(other_html);
                //分店
                var shtml = '';
                var  harr=phpdata.data.branch;
                var arrleg = harr.length;
                $.each(harr,function (index,value) {
                    if(arrleg > 0){
                        $('.each').eq(0).remove();
                        shtml += '<div class="each"><dl><dt>分店名称</dt><dd><input type="hidden" class="branch_id" value="'+value.id+'"><input id="" autocomplete="off" class="sy_yx_srnr" type="text" placeholder="请输入分店名称" nullmsg="companyname" datatype="*" value="'+value.branch_shop_name+'"></dd></dl><dl class="yx_anzdz"><dt>分店地址</dt><dd><p class="yx_dzxq azdz"><input type="hidden" class="br_area_id" id="area" placeholder="请选择地区" nullmsg="installarea" datatype="*" value="'+value.branch_shop_area_id+'"><cite class="yx_srnr_dqu sy_color333">'+value.branch_shop_area_name+'</cite></p><p class="yx_dzxq" id="xxdz"><input autocomplete="off" id="" class="yx_srnr" type="text" placeholder="请填写详细地址" nullmsg="installaddress" datatype="*" value="'+value.branch_shop_address+'"></p><span class="sy_addspan_red"><img src="/static/images/sy_sqfdreduce.png"></span></dd></dl></div>';
                    }

                })
                $('.sy_fendianlist').append(shtml);
                var dlleg=$('.each:last').index();
                $('.each').eq(dlleg).find('dl:first dd').append('<span class="sy_addspan"><img src="/static/image/sy_sqfdadd.png"></span>');
                var nowNum = $('.sy_fendianlist .each').length;
                $('#fdnum span').html(parseInt(nowNum));

            },error:function (phpdata) {

            }

        });
        /********************************************/
        function tippanel(){
            $('.sy-installed-ts').show();
            setTimeout(function(){$('.sy-installed-ts').hide()},2000);
        }
        /*表单验证*/
        $("#idform").Validform({
            tipSweep:true,
            //tiptype:3,
            tiptype:function(msg,o,cssctl){
                if(msg=='nameblank'){
                    $('.sy-installed-ts').text('申请人姓名不能为空');
                    tippanel();
                };
                if(msg=='idblank'){
                    $('.sy-installed-ts').text('身份证号码不能为空');
                    tippanel();
                };
                if(msg=='errorid'){
                    $('.sy-installed-ts').text('请输入正确的身份证号码');
                    tippanel();
                };
                if(msg=='numbphone'){
                    $('.sy-installed-ts').text('申请人手机号码不能为空');
                    tippanel();
                };
                if(msg=='errorphone'){
                    $('.sy-installed-ts').text('请输入正确的手机号');
                    tippanel();
                };
//                if(msg=='numbyzm'){
//                    $('.sy-installed-ts').text('验证码不能为空');
//                    tippanel();
//                };

                if(msg=='companyname'){
                    $('.sy-installed-ts').text('公司名称不能为空');
                    tippanel();
                };
                if(msg=='shopname'){
                    $('.sy-installed-ts').text('店铺名称不能为空');
                    tippanel();
                };
                if(msg=='busneyzm'){
                    $('.sy-installed-ts').text('营业执照注册码不能为空');
                    tippanel();
                };
                if(msg=='installarea'){
                    $('.sy-installed-ts').text('请选择地区');
                    tippanel();
                };
                if(msg=='identity_card_front'){
                    $('.sy-installed-ts').text('请上传法人身份证正面照');
                    tippanel();
                };
                if(msg=='identity_card_back'){
                    $('.sy-installed-ts').text('请上传法人身份证正面照');
                    tippanel();
                };
                if(msg=='business_licence'){
                    $('.sy-installed-ts').text('请上传营业执照照片');
                    tippanel();
                };

            },
            beforeSubmit:function(curform){
//                var flag = false;
//                var verifyCode = $("#verify").val();
//                var mobile = $("#apply_mobile").val();
//                var verifyUrl = "<?//=Yii::$app->params['baseApiUrl']?>//"+"/verifyCode/"+verifyCode+"/mobile/"+mobile;

                var screen_num = $('#apply_screen_number').val();
                $("#block").html(screen_num);
//                $.ajax({
//                    url: verifyUrl,
//                    type: "GET",
//                    async: false,
//                    success:function (phpdata) {
//                        if(phpdata){
//                            $("#idform").append('<input type="hidden" id="code" name="verifyCode" value="'+phpdata+'" >');
//                        }
//                        if(!phpdata){
//                            $('.sy-installed-ts').text('手机验证码不正确！');
//                            flag = true;
//                            tippanel();
//                            return false;
//                        }
//                    },error:function (phpdata) {
//                        $('.sy-installed-ts').text('手机验证码验证失败！');
//                        flag = true;
//                        tippanel();
//                        return false;
//                    }
//
//                });
//                if(flag){
//                    return false;
//                }
                installprotocol();
                return false;
            },
            datatype:{
                "IDcard":/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[0-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i,
                "phone":/^1[0-9]{10}$/,
                'j_cont':/^[1-9]$|^[1-9]\d$|^[1-9]\d{2}$|^[1-9]\d{3}$/
            }
        })
        /*选择省市地区 */
        $(function (){
            //公司地址
            $("#gongsi_dz").click(function (){
                var fuji=$(this).parents(".yx_anzdz")
                var _this = $(this).parents(".yx_anzdz").find(".azdz")
                var __this = _this.find(".yx_srnr_dqu");
                zxcs(_this,__this,fuji)
            })
            //分公司地址

            /*分店地址 */
            $(".sy_fendianlist").delegate(".yx_srnr_dqu","click",function(){
                var fuji=$(this).parents(".yx_anzdz")
                var _this = $(this).parents(".yx_anzdz").find(".azdz")
                var __this = _this.find(".yx_srnr_dqu");
                zxcs(_this,__this,fuji)
            });

        })
        $(function (){
            //查看大图
            $(".sy_tkxgo").click(function(){
                tcyc("ylbigimg")
            });
            $(".sy_tkxgt").click(function(){
                tcyc("ylbigimgt")
            });
            //关闭
            $(".yx_gbylan").click(function(){
                gb_qxss("ylbigimg")
            });
            //关闭
            $(".yx_gbylan").click(function(){
                gb_qxss("ylbigimgt")
            });
            //关闭
            $(".mask").click(function(){
                gb_qxss("ylbigimg")
            });
        })

        //发送验证码倒计时
        var countdown=60;
        function settime(val){
            if (countdown == 0) {
                val.removeAttribute("disabled");
                val.value = "再次获取验证码";
                countdown = 60;
                return false;
            } else {
                if(countdown == 60){
                    var reg = /^1[0-9]{10}$/;
                    var mobile = $('#apply_mobile').val();
                    var re = new RegExp(reg);
                    if (!re.test(mobile)) {
                        $('.sy-installed-ts').text('请输入正确的手机号');
                        tippanel();
                        return true;
                    }
                    if(mobile == ''){
                        $('.sy-installed-ts').text('申请人手机号不能为空');
                        tippanel();
                        return false;
                    }
                    $.ajax({
                        type: "GET",
                        url: baseApiUrl+"/verify?type=3&token=<?=$token?>&wechat_id=<?=$wechat_id?>&mobile="+mobile,
                        success:function(data){
                            if(data.status == 200){
                                $('.sy-installed-ts').text('发送成功');
                                tippanel();
                            }else{
                                $('.sy-installed-ts').text('发送验证码失败');
                                tippanel();
                            }
                            return false;
                        }
                    });
                }
                val.setAttribute("disabled", "disabled");
                val.value = "("+countdown+")秒后重新获取";
                countdown--;
            }
            setTimeout(function() { settime(val) },1000)
        }
        //图片上传Upload-imginput
        $('.Upload-imginput').live('click',function(){
            var ua = navigator.userAgent.toLowerCase();
            if(/android/.test(ua)) {
                var input_id = $(this).parent().attr('id');
                var result = {"action":input_id}
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }else{
                //uploadimg('Upload-imginput');
                var input_id = $(this).parent().attr('id');
                var result = {"action":input_id};
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }
        })
        //弹出窗口统一调用部分
        function tcyc(tcid){
            $(".mask").css('opacity','0.6').show();
            var ymheight=$(document).height()+ "px";
            $(".mask").css("height",ymheight);
            showDialog(tcid);
            $("#"+tcid).show();
        }
        //撤销退款申请  关闭
        function gb_qxss(gbid){
            $("#"+gbid).hide();
            $(".mask").hide();
        }
        /*
         * 根据当前页面于滚动条的位置，设置提示对话框的TOP和left
         */
        function showDialog(dqtc){
            var objw=$(window);//当前窗口
            var objc=$("#"+dqtc);//当前对话框
            var brsw=objw.width();
            var brsh=objw.height();
            var sclL=objw.scrollLeft();
            var sclT=objw.scrollTop();
            var curw=objc.width();
            var curh=objc.height();
            //计算对话框居中时的左边距
            var left=parseInt(sclL+(brsw -curw)/2);
            var top=parseInt(sclT+(brsh-curh)/2);
            //设置对话框居中
            objc.css({"top":top});
        }

        function installprotocol(){
            $('.sy_install_protocol').show();
            $('.sy_dzmask').show();
            //禁止页面滑动
            scrollTop=($(window).scrollTop() || $("body").scrollTop());
            $('body').addClass('modal-open');
            document.body.style.top = -scrollTop + 'px';

        }
        $('.sy_install_btn button:first-child').click(function(){
            $('.sy_dzmask').hide();
            $('.sy_install_protocol').hide();
            $('body').removeClass('modal-open');
            document.scrollingElement.scrollTop = scrollTop;
        })
        //点击删除分店
        var delArr = [];
        var cr_this;
        $('.sy_addspan_red').live('click',function () {
        	tankuangparama('sy_removepanel','是否确认删除');
        	cr_this=$(this);
            
        })
        //分店--删除弹框-确定按钮
        $('.sy_remove_qr').click(function(){
        	closetankuana('sy_removepanel');
        	var nextDoc = cr_this.parents('.each').next('.each').index();
            if(nextDoc == -1){
                cr_this.parents('.each').prev('.each').find('dl:first dd').append('<span class="sy_addspan"><img src="/static/image/sy_sqfdadd.png"></span>');
            }
            pushArr(delArr,cr_this.parents('.yx_anzdz').prev('dl').find('.branch_id').val());
            var numobj = $('#fdnum span').html();
            $('#fdnum span').html(parseInt(numobj) - 1);
            cr_this.parents('.each').remove();
        })
        ////分店--删除弹框-取消按钮
        $('.sy_remove_qx').click(function(){
        	closetankuana('sy_removepanel');
        })
        
        //同意后提交
        $('.sy_install_btn button:last-child').click(function(){
            zztj();
            //获取提交数据
            var branchShopName = [];
            var branchArea = [];
            var branchAddress = [];
            //原有分店ids
            var branchIds = [];
            //其他图片
            var other_image_arr = [];
            //分店名称
            $('.branch .sy_yx_srnr').each(function () {
                var shopName = $(this).val();
                pushArr(branchShopName,shopName);
            })
            //分店地址id
            $('.branch .br_area_id').each(function () {
                pushArr(branchArea,$(this).val());
            })
            //分店详细地址
            $('.branch .yx_srnr').each(function () {
                pushArr(branchAddress,$(this).val());
            })
            //原有分店ids
            $('.branch .branch_id').each(function () {
                pushArr(branchIds,$(this).val())
            })
            //其他图片
            $('#upload_other_image .update_input').each(function () {
                pushArr(other_image_arr,$(this).val());
            })
            var postShop = {
                'id':$('#h_id').val(),
                'business_licence':$('#business_licence').val(),
                'identity_card_front':$('#identity_card_front').val(),
                'identity_card_back':$('#identity_card_back').val(),
                'identity_card_num':$('#identity_card_num').val(),
                'mobile':$('#apply_mobile').val(),
                'verify':$('#verify').val(),
                'company_name':$('#company_name').val(),
                'name':$('#apply_name').val(),
                'company_area_id':$("#area").val(),
                'company_area_name':$("#gongsi_dz").html(),
                'registration_mark':$('#registration_mark').val(),
                //'apply_screen_number':$('#apply_screen_number').val(),
                'area':$('#area').val(),
                'company_address':$('#address').val(),
                'verify_code': $('#code').val(),
                'wx_member_id':<?=$wechat_id?>,
                'token':"<?=$token?>",
                'branchName': branchShopName,
                'branchArea':branchArea,
                'branchAddress':branchAddress,
                'branchIds':branchIds,
                //delete branch shop
                'delIds':delArr,
                'other_image':other_image_arr,
            };
            $.ajax({
                type: "POST",
                data: postShop,
                url: baseApiUrl+"/shop-head/shop-head-modify?token=<?=$token?>&wechat_id=<?=$wechat_id?>&dev=<?=$dev?>",
                success:function(data){
                    if(data.status == 200){
                        //alert('修改成功');
                        //跳转
//                        var shop_id = data.data.shop_id;
//                        window.location.href = "<?//=\yii\helpers\Url::to(['success'])?>//"+"?shopid="+shop_id+"&token="+"<?//=$token?>//&modify=modify&shop_operate_type=4";
                        $('#zztj').hide();
                        $('.zztj_zz').hide();
                        $('.sy_install_protocol').hide();
                        $('.sy_dzmask').show();
                        $('#tip').show();

                    }else{
                        tjgb();
                        $('.sy-installed-ts').text(data.message);
                        tippanel();
                    }
                },
                error:function(data){
                    tjgb();
                    $('.sy-installed-ts').text(data);
                    tippanel();
                }
            });
        });
        //想数组中压入数据
        function pushArr(arr,val) {
            if(!Array.isArray(arr) || !val || val == '请选择地区'){
                return false;
            }
            return arr.push(val);
        }
        /*请上传授权证明  点击加号显示授权证明上传框*/
        $('.syadd_upload_pic').click(function(){
            //默认图片个数
            var inputleg=$(this).parents('.sy_authorcertif').find('.upload').length;
            //获取上传证明 upload的id 及Upload-imginput的id并赋值
            //当前点击时的id值
            var uploadid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).attr('id');
            //当前点击时的id值【除去数字】
            var uploadpre=uploadid.substr(0,uploadid.length-1);
            //当前点击的id值的最后一个数字+1
            var uploadidlast=parseInt(uploadid.substr(uploadid.length-1,1))+1;
            //重新生成一个id值  当前点击的值的id数+1
            var newuploadid=uploadpre+uploadidlast;
            //当前点击的 Upload-imginput【添加图片层】的id
            var upid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).find('.Upload-imginput').attr('id')
            //当前点击的 Upload-imginput【添加图片层】的id除去最后一个数字 id="up1*"
            var upidpre=upid.substr(0,upid.length-1);
            //当前点击的 Upload-imginput【添加图片层】当前点击的id值的最后一个数字+1
            var upidlast=parseInt(upid.substr(upid.length-1,1))+1;
            //当前点击的 Upload-imginput【添加图片层】重新生成一个id值  当前点击的值的id数+1
            var newupid=upidpre+upidlast;
            //authoriz_image1
            // <input class="update_input" id="other_image1" type="hidden">  这个的id号+1
            var update_inputid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).find('.update_input').attr('id')
            var update_inputidpre=update_inputid.substr(0,update_inputid.length-1);
            var update_inputidlast=parseInt(update_inputid.substr(update_inputid.length-1,1))+1;
            var newupdate_inputid=update_inputidpre+update_inputidlast;

            if(inputleg<6){
                if($(this).attr('name') == 'other_upload_image'){
                    var inputHtml = '<input class="update_input" id="'+newupdate_inputid+'" type="hidden">';
                }else{
                    var inputHtml = '<input class="update_input" id="'+newupdate_inputid+'" type="hidden" placeholder="请上传授权证明图片" nullmsg="authoriz_image" datatype="*">';
                }
                $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').before('<div class="upload" id="'+newuploadid+'">'
                    + inputHtml
                    +'<img class="imgspace" src="/static/image/blank.jpg"><img class="addimg" src="/static/image/uploadimg-add.png">'
                    +'<?if ($dev == "ios"):?><p class="Upload-imginput" id="'+newupid+'"></p><? else:?><input type="file" class="Upload-imginput" accept="image/*" id="'+newupid+'">'
                    +'<?endif;?><div class="progress-bar" step="0"></div><p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p></div>')
                var lolg=$(this).parents('.sy_authorcertif').find('.upload').length;
                if(lolg==6){
                    $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').hide();
                }
            }
        })
        //减号 删除添加的授权证明图片
        $('.syred_upload_pic').live('click',function(){
            var lolg=$(this).parents('.sy_authorcertif').find('.upload').length;
            if(lolg<7){
                $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').show();
            }
            $(this).parent('.upload').remove();
        })
        //点击完成跳转到店铺选择
        $('#achieve').on('click',function () {
            var ua = navigator.userAgent.toLowerCase();
            var result = {"action":"closewebview"}
            if(/android/.test(ua)) {
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }else{
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }
            //window.location.href = "<?=\yii\helpers\Url::to(['choose-shop-type'])?>"+"?&token="+"<?=$token?>"+"&dev=<?=$dev?>";

        })
    </script>
</body>
</html>