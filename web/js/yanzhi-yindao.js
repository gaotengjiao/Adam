/**
 * Created by Gary Lew on 2016/7/27.
 */
$(function () {
    function phone() {
        var phone=window.localStorage.getItem('iPhone');
        var myphone=phone.substr(3,4);
        return phone.replace(myphone,"****");
    }
    $('.ceshi span:last-child').text('(绑定手机'+phone()+')');
    $('.phone').append('<span>'+phone()+'</span>');
    touch.on('.huanhao','tap',function (ev) {
        window.localStorage.clear();
        window.sessionStorage.clear();
        window.location.href='denglu.html';
    });
    touch.on('.ceshi','tap',function (ev) {
        $('.yindao').animate({marginTop:'-12rem',opacity:0.5},800);
        $('#chuan').css({display:'block'});
    });
});

$.post('http://www.buruwo.com/wx/wx.php',{url:window.location.href},function (data) {
    var img={
        uid:window.localStorage.getItem('uid'),
        token:window.localStorage.getItem('token'),
        type:0,
        accessToken:data.accessToken,
        name:'',
        num:'',
        serverId:{}
    };
    var images = {
        localId: [],
        serverId: []
    };
    console.log(data);
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: data.appId,
        timestamp: data.timestamp,
        nonceStr: data.nonceStr,
        signature: data.signature,
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function () {
        document.querySelector('#a').onclick=function () {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: 'original', // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    images.localId.push(localIds[0]);
                    console.log(images.localId);
                    $('#a').html('<img style="width: 100%;height: 100%;margin: 0;" src="'+localIds+'">');
                    wx.uploadImage({
                        localId: localIds[0], // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            var serverId = res.serverId; // 返回图片的服务器端ID
                            images.serverId.push(serverId);
                            console.log(images.serverId)
                        }
                    });
                }
            });
        };
        document.querySelector('#b').onclick=function () {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: 'original', // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    images.localId.push(localIds[0]);
                    $('#b').html('<img style="width: 100%;height: 100%;margin: 0;" src="'+localIds+'">');
                    console.log(images.localId)
                    wx.uploadImage({
                        localId: images.localId[1], // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            var serverId = res.serverId; // 返回图片的服务器端ID
                            images.serverId.push(serverId);
                            console.log(images.serverId)
                        }
                    });
                }
            });
        };
        document.querySelector('#c').onclick=function () {
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: 'original', // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    console.log(localIds);
                    images.localId.push(localIds[0]);
                    $('#c').html('<img style="width: 100%;height: 100%;margin: 0;" src="'+localIds+'">');
                    console.log(images.localId);
                    wx.uploadImage({
                        localId: images.localId[2], // 需要上传的图片的本地ID，由chooseImage接口获得
                        isShowProgressTips: 1, // 默认为1，显示进度提示
                        success: function (res) {
                            var serverId = res.serverId; // 返回图片的服务器端ID
                            images.serverId.push(serverId);
                            console.log(images.serverId)
                        }
                    });
                }
            });
        };
        document.querySelector('.tijiao').onclick=function () {
            if (!checkName($('#name').val())){
                alert('请输入正确的姓名');
                return
            }
            if(!checkCardId($('#num').val())){
                alert('身份证号错误，请重新填写！')
                return
            }
            img.name=$('#name').val();
            img.num=$('#num').val();
            img.serverId=arrTojson(images.serverId);
            $.post('http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=upload',{userinfo:JSON.stringify(img)},function (data) {
                console.log(data);
            },'json')
        }
    })
},'json');