/**
 * Created by Administrator on 2016/7/14.
 */
$(".btn6").click(function(){
    var id = $(this).attr("dd");
    var Uid = $(this).attr("uid");
    var txt=  "拒绝原因：<select id='yuanyin'><option value='信用分不足'>信用分不足</option><option value='资料不全'>资料不全</option></select>"
    window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.input,{
        onOk:function(v){
            var yuan = $("#yuanyin").val();
            $.post("{:U('Authorization/RefuseAuthorization')}", {id: id,uid:Uid, refuse: yuan}, function (msg) {
                if (msg == 1) {
                    $("#status"+id).html( "<font color='red'>已拒回</font>");
                    $("#status2"+id).live().html( "<a href='javascript:void(0)' class='jujue' dd='"+id+"'><font color='#006400'>查看拒回原因</font></a>");
                    console.log(v);
                    $("#tip").html("拒回成功").show(300).delay(3000).hide(300);
                } else {
                    $("#tip").html("拒回失败").show(300).delay(3000).hide(300);
                }
            })
        }
    });
});

$(".jujue").click(function (){
    var id = $(this).attr("dd");
    $.post("{:U('Authorization/ReasonsForRefusal')}", {id: id}, function (msg) {
        alert(msg)
    })
})

$(".money").blur(function () {
    var id = $(this).attr("dd");
    var money = $(this).val();
    $.post("{:U('Authorization/EditMonet')}", {id: id, money: money,type:"editmoney"}, function (msg) {
        if (msg == 1) {

            $("#tip").html("修改成功").show(300).delay(3000).hide(300);
        } else {
            $("#tip").html("修改失败").show(300).delay(3000).hide(300);
        }
    })
})
$(".bo").click(function (){
    var id = $(this).attr("dd");
    alert(id)
})
$(".shou").click(function (){
    var id = $(this).attr("dd");
    $.post("{:U('Authorization/Authorization')}", {id:id}, function(msg){
        if(msg==1){
            $("#status"+id).html( "<font color='#a9a9a9'>已授权</font>");
            $("#tip").html("授权成功").show(300).delay(3000).hide(300);
        }else{
            $("#tip").html("授权失败").show(300).delay(3000).hide(300);
        }
    })
})