window.onload = function(){
	var oPicList=document.getElementById("bannerList");
	var aBtns=document.getElementById("btn").children;
	var iScroll=0;
	var iStartX=0;
	var iStartPageX=0;
	var iNow=0;
	var oTimer=0;
	oPicList.innerHTML+=oPicList.innerHTML;
	oPicList.style.width=oPicList.clientWidth*2+"px";
	function autoPlay()
	{
		oTimer=setInterval(function(){
			iNow++;	
			next();
		},2000);
	}
	autoPlay();
	oPicList.addEventListener("touchstart",fnStart,false);
	function fnStart(ev)
	{
		clearInterval(oTimer);
		clearInterval(oPicList.timer);
		if(iNow<=0)
		{
			iNow+=aBtns.length;
			iScroll=-iNow*window.screen.width;
			css(oPicList, "translateX", iScroll);
		}
		iStartPageX=ev.changedTouches[0].pageX;
		iStartX=iScroll;
		ev.stopPropagation()
	};
	oPicList.addEventListener("touchmove",fnMove,false);
	function fnMove(ev)
	{
		var iDis=ev.changedTouches[0].pageX-iStartPageX;
		iScroll=iStartX+iDis;
		css(oPicList, "translateX", iScroll);
		ev.stopPropagation()
	};
	oPicList.addEventListener("touchend",fnEnd,false);
	function fnEnd(ev)
	{
		var iDis=ev.changedTouches[0].pageX-iStartPageX;
		var iNub=Math.round(iDis/window.screen.width);
		iNow-=iNub;
		next();
		autoPlay();
		ev.stopPropagation()
	};
	function next()
	{
		iScroll=-iNow*window.screen.width;
		for(var i=0;i<aBtns.length;i++)
		{
			aBtns[i].style.background="#fff";
		}
		aBtns[iNow%aBtns.length].style.background="#F97FA4";
		if(iNow>=aBtns.length)
		{
			tweenMove(oPicList,{translateX:iScroll},300,"easeOut",function(){
				iNow=iNow%aBtns.length;
				iScroll=-iNow*window.screen.width;
				css(oPicList, "translateX", iScroll);
			});
		}
		else
		{
			tweenMove(oPicList,{translateX:iScroll},300,"easeOut");
		}
	}
}
//	----------------以上是首页轮播-------------------------------------------------------------------------
$(function(){
	var oSpan = $("#footer span");
	var imgSrc = $("#footer img")
	var arr1 = ["public/images/icon61.png","public/images/icon71.png","public/images/icon81.png","public/images/icon91.png","public/images/icon101.png"]
	var arr2 = ["public/images/icon6.png","public/images/icon7.png","public/images/icon8.png","public/images/icon9.png","public/images/icon10.png"]
	var oAll = document.getElementById("all");
	var sec = oAll.getElementsByTagName("section");
	imgSrc[0].src = arr1[0]
	$("#footer li").tap(function(){
		for( var n = 0; n<imgSrc.length; n++ ){
			imgSrc[n].src = arr2[n];
		}
		oSpan.removeClass("select");
		oSpan.eq($(this).index()).addClass("select");
		imgSrc[($(this).index())].src = arr1[($(this).index())]
		oAll.style.left = -(($(this).index())*window.screen.width)+"px";
	});
})
//-------------------首页搜索栏手机适配(开始)-------------------------------------------------------------------
var shuru = document.getElementById("shuru");
var local = document.getElementById("local");
var action1 = document.getElementById("action1");
var action2 = document.getElementById("action2");
action1.style.borderRight = "1px solid #d6d7dc";
action2.style.borderBottom = "1px solid #d6d7dc";
var ua = navigator.userAgent.toLowerCase();	
	if (/iphone|ipad|ipod/.test(ua)) {
		shuru.style.marginTop = "0.23rem";
		local.style.marginRight = "0.2rem"
	} else if (/android/.test(ua)) {
		shuru.style.marginTop = "0.375rem";
		local.style.marginRight = "0.2rem"
	}
//-------------------首页搜索栏手机适配(结束)-------------------------------------------------------------------






















