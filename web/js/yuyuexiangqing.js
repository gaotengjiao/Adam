$(function () {
    var ua = navigator.userAgent.toLowerCase();
    if (/iphone|ipad|ipod/.test(ua)) {
        $('#yueList p').css('font-size','0.55rem');
    } else if (/android/.test(ua)) {
        $('#yueList p').css('font-size','0.65rem');
    }
})

function yueAjax(type,url,data,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};
if( getCookie('panDuan') == 100 ){ //判断此处是从预约单进入预约详情
	delCookie('panDuan');
	var pid=JSON.parse(localStorage.getItem('allId')).oneId.toString();
	var uuid=JSON.parse(localStorage.getItem('allId')).uid;
	var tie=JSON.parse(localStorage.getItem('allId')).time;
	yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=SelSubPro","num="+localStorage.getItem("bianHao"),nowYue2);
	function nowYue2(proYue0){
		if( proYue0.status == 0 ){
			$("#yueStaus").append("<span>(成功)</span>");
		}else{
			$("#yueStaus").append("<span>(失败)</span>");
		};
		$("#yueXiang").append("<time>预约日期："+tie+"</time>");
		$("#pNumber").get(0).innerHTML = "预约编号：" + localStorage.getItem("bianHao");
		for( var n = 0; n<proYue0.goods.length; n++ ){
			$("#yueList").append("<li id="+ proYue0.goods[n].id +"><div class='suiImg'><img src="+proYue0.goods[n].humbimg+" /></div><h2>"+ proYue0.goods[n].gname +"</h2><p>"+ proYue0.goods[n].about +"</p><span>查看详情</span><div class='key'><span>"+ proYue0.goods[n].keyword +"</span></div></li>")
		};
		$("#yueList li").tap(function(){
			delCookie("cok2");
			setCookie("cok2",this.id);
			window.location.href="xiangqingye.html";
		});
	};
}else{ //此处是从历史预约进入预约详情
	yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=BookingDetails","num="+getCookie("hisNum")+"&type="+getCookie("hisStype")+"&uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token'),hisXiang);
	function hisXiang(xiangMu){
		if( xiangMu.resultnum == 0 ){
			if( xiangMu.result.status == 0 ){
				$("#yueStaus").append("<span>(成功)</span>");
			}else if( xiangMu.result.status == 5 ){
				$("#yueStaus").append("<span>(取消)</span>");
                $('.noneYue a').eq(0).html('已取消');
			};
			$("#yueXiang").append("<time>预约日期："+xiangMu.result.time+"</time>");
			$("#pNumber").get(0).innerHTML = "预约编号：" + xiangMu.result.num;
			setCookie('hisNum',xiangMu.result.num);
			for( var m=0; m < xiangMu.result.project.length; m++ ){
				$("#yueList").append("<li id="+ xiangMu.result.project[m].id +"><div class='suiImg'><img src="+ xiangMu.result.project[m].img +" ></div><h2>"+ xiangMu.result.project[m].gname +"</h2><p>"+ xiangMu.result.project[m].about +"</p><span>查看详情</span><div class='key'><span>"+ xiangMu.result.project[m].keyword +"</span></div></li>")
			};
            $("#yueList li").tap(function(){
                delCookie("cok2");
                setCookie("cok2",this.id);
                window.location.href="xiangqingye.html";
            });
		};
	}
};
$('.noneYue a').eq(0).tap(function(){ //点击取消预约
	del();
});
function del(){
	var msg=confirm("您确定要取消预约吗？");
	if(msg==true){
		yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=CancelSub","num="+localStorage.getItem("bianHao")+"&uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=itemscancel",movPro);
		function movPro(mpro){
			if(mpro.resultnum == 0){
				alert("删除成功")
			};
		};
		$("#yueStaus span").html('');
		$("#yueXiang").html('');
		$("#pNumber").html('预约编号');
		$("#yueList").html('');
	};
};
$("#close").tap(function(){
	window.location.href = "xiangmu.html";
});


