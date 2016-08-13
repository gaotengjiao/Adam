var jishu=0;
var addOnoff = true;
//var zouOnoff = false;
/*if(!localStorage.getItem('ceshi')){
	localStorage.setItem('ceshi',0);
	localStorage[localStorage.getItem('ceshi').toString()]= JSON.stringify({fuid:[{'ziid':'0'},{'xiaoid':'0'}]});
}*/
//console.log(getCookie('cok2'));
fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=index&a=Messagedetails","projectid="+getCookie('cok2'),callback);

/*if(aOnoff == false){
    console.log('123456789');
    if(sArr.length != 0){
        for(var i=0;i<sArr.length;i++){
            alert(sArr[i] +' + '+ localStorage.getItem('xiaoid'));
            if(sArr[i] == localStorage.getItem('xiaoid')){
                addOnoff = false;
                //alert(123);
                $("#add").css({
                    background:"#989898"
                });
            }
        }
    }
}*/


$("#add").click(function(event){   //点击预约，logo弹跳效果
	//console.log("uid="+localStorage.getItem('uid')+"&project="+localStorage.getItem('xiaoid')+"&token="+localStorage.getItem('token')+"&type=add");
	if(!getCookie('users')){
		setCookie('lalala','2');
		window.location.href = 'denglu.html';
	}
	else{
		//if(Number(localStorage.getItem('ceshi')) == 0){
        if(addOnoff == true){
            haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&project="+localStorage.getItem('xiaoid')+"&token="+localStorage.getItem('token')+"&type=add",tianjia);
            var offset = $("#yueDan").offset();
            var flyer = $("#tiao");
            var addcar = $(this);
            $("#tiao").css({
                display:"block",
            })
            flyer.fly({
                start: {
                    left: event.pageX,//开始位置（必填）#fly元素会被设置成position: fixed
                    top: event.pageY //开始位置（必填）
                },
                end: {
                    left: offset.left+30, //结束位置（必填）
                    top: offset.top+30, //结束位置（必填）
                    width: 0, //结束时宽度
                    height: 0 //结束时高度
                },
            });
            $(this).css({
                background:"#989898"
            });
        }
		//};
	};
});

		//else{
/*$("#add").click(function(event){   //点击预约，logo弹跳效果
    //console.log("uid="+localStorage.getItem('uid')+"&project="+localStorage.getItem('xiaoid')+"&token="+localStorage.getItem('token')+"&type=add");
    if(!getCookie('users')){
        setCookie('lalala','2');
        window.location.href = 'denglu.html';
    }
    else{
        if(addOnoff == true){
            haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&project="+getCookie('cok2')+"&token="+localStorage.getItem('token')+"&type=add",tianjia);
            var offset = $("#yueDan").offset();
            var flyer = $("#tiao");
            var addcar = $(this);
            $("#tiao").css({
                display:"block",
            })
            flyer.fly({
                start: {
                    left: event.pageX,//开始位置（必填）#fly元素会被设置成position: fixed
                    top: event.pageY //开始位置（必填）
                },
                end: {
                    left: offset.left+30, //结束位置（必填）
                    top: offset.top+30, //结束位置（必填）
                    width: 0, //结束时宽度
                    height: 0 //结束时高度
                },
            });
            $(this).css({
                background:"#989898"
            });
        }
    }
});*/
		//}



function tianjia(tdata){
	if(tdata.resultnum == 0){
		//if(addOnoff == true){
			if( window.localStorage ){
				/*if(!localStorage.getItem('ceshi')){
					localStorage.setItem('ceshi',Number(jishu)+1);
				}
				else{
					localStorage.setItem('ceshi',Number(localStorage.getItem('ceshi'))+1);
				}*/
				$('.danNumber').html(Number(localStorage.getItem('sNum'))+1);
				localStorage.setItem('sNum',Number(localStorage.getItem('sNum'))+1);
				/*var lg = localStorage.getItem('ceshi');
				var fuid = localStorage.getItem('fuid');//fuid存储
				var ziid = localStorage.getItem('ziid');//ziid存储
				var xiaoid = localStorage.getItem('xiaoid');//xiaoid存储
				localStorage[lg.toString()]= JSON.stringify({fuid:[{'ziid':ziid},{'xiaoid':xiaoid}]});*/
				addOnoff = false;
                sArr.push(getCookie('cok2'));
			}
		//}
	}
	else{
		alert(tdata.result_mess);
	}
}

function fengAjax(type,url,data,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};

function callback(xiangXi){
	//alert(xiangXi.result.parameter.id);	
	if(xiangXi.resultnum == 0){
		$('.doctor_tuijian').append('<img src="'+xiangXi.result.doctor.user_url+'">');
		$('.doctor_name').append('<span>'+xiangXi.result.doctor.user_login+'</span>');
		$(".main").append(xiangXi.result.goodsmess.content);
		if(xiangXi.result.parameter !== undefined){
			$(".main").append('<div class="content_five"><div class="content_border"><h2 class="content_head">思美丽妍</h2><h3 class="content_small">手术相关信息</h3></div><ul class="list_information clear"></ul></div>');
			$('.list_information').append('<li><span>手术类型</span><p>'+xiangXi.result.parameter.opstype+'</p></li><li><span>治疗时间</span><p>'+xiangXi.result.parameter.curetime+'</p></li><li><span>体检项目</span><p>'+xiangXi.result.parameter.healthproject+'</p></li><li><span>疼痛指数</span><p>'+xiangXi.result.parameter.painindex+'</p></li><li><span>风险指数</span><p>'+xiangXi.result.parameter.riskindex+'</p></li><li><span>手术材料</span><p>'+xiangXi.result.parameter.opsmaterial+'</p></li><li><span>麻醉方式</span><p>'+xiangXi.result.parameter.hocustype+'</p></li><li><span>拆线天数</span><p>'+xiangXi.result.parameter.stitchestype+'</p></li><li><span>恢复过程</span><p>'+xiangXi.result.parameter.rejuvenationname+'</p></li><li><span>手术优势</span><p>'+xiangXi.result.parameter.surgeryadvantage+'</p></li><li><span>达到最终效果时间</span><p>'+xiangXi.result.parameter.thefinaleffectoftime+'</p></li><li><span>效果维持时间</span><p>'+xiangXi.result.parameter.theeffecttomaintaintime+'</p></li><li><span>治疗次数</span><p>'+xiangXi.result.parameter.treatmenttimes+'</p></li><li><span>住院治疗</span><p>'+xiangXi.result.parameter.hospitalization+'</p></li>');
		}
	}
};

function html_decode(str)
  {
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/>/g, "&");
    s = s.replace(/</g, "<");
    s = s.replace(/>/g, ">");
    s = s.replace(/ /g, " ");
    s = s.replace(/'/g, "\'");
    s = s.replace(/"/g, "\"");
    s = s.replace(/<br>/g, "\n");
    return s;
  }


if( document.getElementById("xiangH")){
	var xiangH = document.getElementById("xiangH");
	var sSpan = xiangH.getElementsByTagName("span");
	var aside = document.getElementsByTagName("aside");
	for( var a = 0; a<sSpan.length; a++ ){
		sSpan[a].index = a;  //添加索引值,记录下标
		sSpan[a].onclick = function (){
			for( var a = 0; a < sSpan.length; a++ ){
				sSpan[a].style.color = "#989898";
				aside[a].style.display = "none";
				sSpan[a].style.borderBottom = "none"
			};
			this.style.color = "#f97fa4";
			this.style.borderBottom = "2px solid #f97fa4";
			aside[this.index].style.display = "block";
		}
		
	};
}
$("#yueDan").click(function(){
	setCookie("cok3",0);
	if(!getCookie('users')){
		setCookie('lalala','2');
		window.location.href = 'denglu.html';
	}
	else{
		//delCookie("cok2");
		fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=select",chaxu);
	}
})
var arr = [];//用来存  预约单中所有的项目的项目id
function chaxu(cData){
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
		alert(result_mess);
	}
}


