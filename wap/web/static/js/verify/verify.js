/**
 * Created by Administrator on 2019/7/2.
 */
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
                url: baseApiUrl+"/verify?type=3&token="+token+"&mobile="+mobile,
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
