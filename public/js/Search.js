function tishi(context) {
    $('body').append('<div id="msg"></div>');
    $('#msg').html(context).fadeIn(function () {
        setTimeout(function () {
            $('#msg').fadeOut();
        },500)
    });
};
msg('页面加载中');