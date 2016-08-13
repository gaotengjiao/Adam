/**
 * Created by Gary Lew on 2016/8/9.
 */
$(function () {
    $('#tou img').attr('src',window.localStorage.getItem('iUser_img'));
    $('.name').text(window.localStorage.getItem('iNickname'));
    $('.sex').text(window.localStorage.getItem('iSex'));
    $('.age').text(window.localStorage.getItem('iPoints'));
    $.post('http://www.buruwo.com/wx/wx.php',{url:window.location.href},function (data) {
        var img={
            uid:window.localStorage.getItem('uid'),
            token:window.localStorage.getItem('token'),
            type:1,
            accessToken:data.accessToken,
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
            document.querySelector('#tou').onclick=function () {
                wx.chooseImage({
                    count: 1, // 默认9
                    sizeType: 'original', // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                        images.localId.push(localIds[0]);
                        console.log(images.localId);
                        $('#a img').attr('src',localIds);
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
        })
    },'json')
})