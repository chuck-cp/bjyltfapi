/**
 * Created by Administrator on 2019/7/2.
 */
function verifyCode(){
    var flag = false;
    var verifyCode = $("#verify").val();
    var mobile = $("#apply_mobile").val();
    var verifyUrl = baseApiUrl+"/verifyCode/"+verifyCode+"/mobile/"+mobile;
    $.ajax({
        url: verifyUrl,
        type: "GET",
        async: false,
        success:function (phpdata) {
            if(phpdata){
                return phpdata;
                //$("#idform").append('<input type="hidden" id="code" name="verifyCode" value="'+phpdata+'" >');
            }
            if(!phpdata){
                $('.sy-installed-ts').text('手机验证码不正确！');
                flag = true;
                tippanel();
                return false;
            }
        },error:function (phpdata) {
            $('.sy-installed-ts').text('手机验证码验证失败！');
            flag = true;
            tippanel();
            return false;
        }

    });
}


