/**
 * Created by Administrator on 2016/7/6.
 */
$(function(){
    delCookie('comeon');
    $("#footer a").css("color","#989898");
    $("#footer span").css("color","#989898");
    $("#footer a").eq(2).css("color","#F97FA4");
    $("#footer span").eq(2).css("color","#F97FA4");

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
            document.querySelector('#a').onclick=function () {
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
                                console.log(images.serverId);
                                $('.tj').addClass('sub');
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
                        $('#b img').attr('src',localIds);
                        console.log(images.localId)
                        wx.uploadImage({
                            localId: images.localId[1], // 需要上传的图片的本地ID，由chooseImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var serverId = res.serverId; // 返回图片的服务器端ID
                                images.serverId.push(serverId);
                                console.log(images.serverId);
                                $('.tj').addClass('sub');
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
                        $('#c img').attr('src',localIds);
                        console.log(images.localId);
                        wx.uploadImage({
                            localId: images.localId[2], // 需要上传的图片的本地ID，由chooseImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var serverId = res.serverId; // 返回图片的服务器端ID
                                images.serverId.push(serverId);
                                console.log(images.serverId)
                                $('.tj').addClass('sub');
                            }
                        });
                    }
                });
            };
            document.querySelector('.tj').onclick=function () {
                if(images.serverId.length==0){
                    return
                }
                img.num=$('#num').val();
                img.serverId=arrTojson(images.serverId);
                $.post('http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=upload',{userinfo:JSON.stringify(img)},function (data) {
                    console.log(data);
                },'json')
            }
        })
    },'json');
});

