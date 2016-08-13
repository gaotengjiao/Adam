
$(function(){
    $('#pro').css('left',0);
	haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Index&a=project","typeoneid=13",callback);
	$("#footer a").css("color","#989898");
	$("#footer span").css("color","#989898");
	$("#footer a").eq(1).css("color","#F97FA4");
	$("#footer span").eq(1).css("color","#F97FA4");
    
    $('.pro_search').tap(function(){
        window.location.href = 'sousuoyemian.html';
    });

    /*var ua = navigator.userAgent.toLowerCase();
     if (/iphone|ipad|ipod/.test(ua)) {
     $('.shuru2').css('margin-top','0.15rem');
     } else if (/android/.test(ua)) {
     $('.shuru2').css('margin-top','0.25rem');
     }*/
	/*var tnav = document.getElementById("tnav");
	var proUl = document.getElementById("pro");
	var proLi = proUl.getElementsByTagName("li");
	var downLeft1 = 0;
	var downX1 = 0;
	var bBtn1 = true;
	var downTime1 = 0;
	proUl.addEventListener("touchstart",fnStart1,false);
	function fnStart1(ev){
		var ev = ev||window.event;
		var touchs = ev.changedTouches[0];
		downLeft1 = this.offsetLeft;
		downX1 = touchs.pageX;
	};
	//proUl.addEventListener("touchmove",fnMove1,false);
	function fnMove1(ev){
		var ev = ev||window.event;
		var touchs = ev.changedTouches[0];
			if( this.offsetLeft >= 0 ){
				if(bBtn1){
					bBtn1 = false;
					downX1 = touchs.pageX;		
				}
				this.style.left = (touchs.pageX - downX1)/3 + 'px';
			}else if(this.offsetLeft == tnav.offsetWidth - this.offsetWidth || this.offsetLeft < tnav.offsetWidth - this.offsetWidth){
				if(bBtn1){
					bBtn1 = false;
					downX1 = touchs.pageX;		
				}
				this.style.left = (touchs.pageX - downX1)/3 + (tnav.offsetWidth - this.offsetWidth) + 'px';
			}else{
				this.style.left = touchs.pageX - downX1 + downLeft1 + 'px';
			}
		};
	//proUl.addEventListener("touchend",fnEnd1,false);
	function fnEnd1(ev){
		var ev = ev||window.event;
		this.ontouchmove = null;
		this.ontouchend = null;
		var touchs = ev.changedTouches[0];
		if( downX1 < touchs.pageX ){  //→
			if(touchs.pageX - downX1 > tnav.offsetWidth/3 || Date.now() - downTime1 < 200 &&  touchs.pageX - downX1 > 50){
				startMove(proUl,{left : 0},400,'easeOut');
			}
		}else{  //←
			if(downX1 - touchs.pageX > tnav.offsetWidth/3 || Date.now() - downTime1 < 200 && downX1- touchs.pageX > 50){
				startMove(proUl,{left : tnav.offsetWidth - this.offsetWidth},400,'easeOut');	
			}
			
		};
	};*/
    $(".content_left li").click(function(){//项目大类点击
        $(".content_left li").attr('class','');
        $(this).attr('class','left_now');
        $("#pro").html("");
        $(".right_plist").html("");
        haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Index&a=project","typeoneid="+($(this).index()+13),callback);
    })


})

//滚动事件
window.onload = function(){
    var ra = document.getElementsByClassName('right_ad')[0];
    var pContent = document.getElementsByClassName('project_content')[0];
    var rtitle = document.getElementsByClassName('right_title')[0];
    var raHeight = ra.offsetHeight;

    window.onscroll = function () {
         var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
         if (scrollTop < raHeight) {
            rtitle.style.position = 'static';
         } else {
             rtitle.style.width = '11.85rem';
             rtitle.style.position = 'fixed';
             rtitle.style.top = '2.05rem';
             rtitle.style.left = '3.7rem';
         }
    };
};

function haoAjax(type,url, data ,callback){//回调方法
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};

function callback(vall){
    var allWidth=0;
    if(vall.resultnum == 0){
	    for(var i = 0; i < vall.result.type.length; i++){//页面加载小菜单
	    	$("#pro").append('<li id="'+ vall.result.type[i].id+'" style="width:'+vall.result.type[i].len+'rem;"><a href="javascript:;">'+vall.result.type[i].typename+'</a></li>');
            $("#pro").find('li').eq(0).attr('class','list_now');
	        allWidth = allWidth + Number(vall.result.type[i].len);
		};
		$("#pro li").tap(function(){//小菜单点击事件
	    	bianlianL = this.id;
			haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=index&a=Projectmess","projectid="+this.id,callback1);
			$("#pro li").attr('class','');
			$("#pro li").eq($(this).index()).attr('class','list_now');
            ziid = $("#pro li").eq($(this).id);
            /*if($(this).index()>1 && $(this).index()<vall.result.type.length-2){
                //var oLeft = $('#tnav').offset().left;
                var oWidth = $('#tnav').width();
                var sLeft = $(this).offset().left;
				var swidth = $(this).width();
                //var pLeft = $('#pro').css('left');
                //console.log(sLeft);
				$('#pro').css('transition','0.3s');
                $('#pro').css('left',-(sLeft-((oWidth-swidth)/2))/40+'rem');
               // console.log( $('#pro').css('left'));
            }*/
	   });
	    for(var m = 0; m < vall.result.goods.length; m++){//页面加载数据
            var df = 0;
            var iii='预约';
            if(sArr.length != 0){
                for(var k=0;k<sArr.length;k++){
                    if(sArr[k] == vall.result.goods[m].id){
                        df = 1;
                        iii = '取消'
                    }
                }
            }
	    	$(".right_plist").append('<li class="clear" data-flag="'+df+'" data-dnum="'+ vall.result.goods[m].id +'"><div class="right_img"><img src="'+ vall.result.goods[m].humbimg +'"><a href="xiangqingye.html"></a></div><div class="right_text"><h3>'+ vall.result.goods[m].gname +'</h3><p>'+ vall.result.goods[m].about +'</p><span>'+ vall.result.goods[m].keyword +'</span><a href="xiangqingye.html"></a></div><div class="tianJia"><span></span><i>'+iii+'</i></div></li>');
        };
	    $("#pro").css({
	    	width:allWidth +"rem"
	    });
	    addlocalstorage();
	    contlocalstorage();
   	}
};

function callback1(proMess){//点击小菜单，循环数据
	proMess=JSON.parse(proMess);
	$(".right_plist").html("");
	for(var n = 0; n < proMess.length; n++){
		var df = 0;
		var iii='预约';
		if(sArr.length != 0){
			for(var k=0;k<sArr.length;k++){
				if(sArr[k] == proMess[n].id){
					df = 1;
					iii = '取消'
				}
			}
		}
    	$(".right_plist").append('<li class="clear" data-flag="0" data-dnum="'+ proMess[n].id +'"><div class="right_img"><img src="'+ proMess[n].humbimg +'"><a href="xiangqingye.html"></a></div><div class="right_text"><h3>'+ proMess[n].gname +'</h3><p>'+ proMess[n].about +'</p><span>'+ proMess[n].keyword +'</span><a href="xiangqingye.html"></a></div><div class="tianJia"><span></span><i>预约</i></div></li>');
    }
	addlocalstorage();
    contlocalstorage();//内容点击localstorage存储事件
}
$('#proIcon').tap(function(ev){//项目预约单  点击查询用户的所有项目
    ev.stopPropagation();
	if(!getCookie('users')){
		setCookie('gogo','0');
		window.location.href = 'denglu.html';
	}
	else{
		haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=select",chaxun);
	}
}); 

var arr = [];//用来存  预约单中所有的项目的项目id
function chaxun(cData){
	if(cData.resultnum == 0){
		for(var a=0;a<cData.result.length;a++){
			arr.push(cData.result[a].id);
		}
		localStorage.setItem('arr',arr);
		window.location.href = 'yuyuedan.html';
	}
	else if(cData.resultnum == 404){
		//alert('用户您还没有预约任何项目哦~');
		window.location.href = 'yuyuedan.html';
	}
	else{
		//alert(cData.result_mess);
	}
}
