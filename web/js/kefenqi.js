$(function(){
	$('.can_title li').eq(0).attr('class','can_change');
	$('.can_title li').eq(1).attr('class','');
	$('.can_nocontent').css('display','none');
	$('.can_content1').css('display','block');
	$('.can_choose').html("");
	
	$('.can_title li').tap(function(){//选项卡切换
		$('.can_title li').attr('class','');
		$(this).attr('class','can_change');
		if($(this).index() == 0){
			$('.can_nocontent').css('display','none');
			$('.can_content1').css('display','block');
		}
		else{
			$('.can_choose').html("");
			$('.can_bottom p').html('无交易');
			$('.can_bottom p').css('color','#cfcfcf');
			$('.can_bottom input').css('background','#cfcfcf');
			$('.can_nocontent').css('display','block');
			$('.can_content1').css('display','none');
		}
	});
	
	$('.can_choose').tap(function(){//选择账单
		var i = $(this).index();
		var pay = $(this).closest('li').find('p').find('span').html();//当前账单的金额
		if($(this).html() == ""){
			$(this).append('<img src="../img/fenqizhangdanduigou-icon.png">');
			$('.can_bottom input').css('background','#f97fa4');
			if($('.can_bottom p').html() == '无交易'){
				$('.can_bottom p').html('已选金额：￥');
				$('.can_bottom p').css('color','#4D4D4D');
				$('.can_bottom p').append('<span>'+pay+'</span>');
			}
			else{
				var value=$('.can_bottom p').find('span').html();//需要分期的金额
				$('.can_bottom p').find('span').html(parseFloat(value)+parseFloat(pay));
			}
		}
		else{
			$(this).html("");
			var value=$('.can_bottom p').find('span').html();//需要分期的金额
			$('.can_bottom p').find('span').html(parseFloat(value)-parseFloat(pay));
			if($('.can_bottom p').find('span').html() < 1){
				$('.can_bottom p').html('无交易');
				$('.can_bottom p').css('color','#cfcfcf');
				$('.can_bottom input').css('background','#cfcfcf');
			}
		}
	})
})
