$(function(){
	$("#table tr").tap(function(){
		//console.log($("#table tr"))
		$("#table tr input").attr('checked','false');
		$(this).find('input').attr('checked','true');
	})
})
