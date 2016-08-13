(function (win) {
    var doc = win.document, html = doc.documentElement, option = html.getAttribute("use-rem");
    if (option === null) {
        return
    }
    var baseWidth = parseInt(option).toString() === "NaN" ? 640 : parseInt(option);
    var grids = baseWidth / 100;
    var clientWidth = html.clientWidth || 320;
    html.style.fontSize = clientWidth / grids + "px";
    var testDomWidth = 0;
    var adjustRatio = 0;
   
    var reCalc = function () {
        var newCW = html.clientWidth;
        if (newCW === clientWidth) {
            return
        }
        clientWidth = newCW;
        html.style.fontSize = newCW * (adjustRatio ? adjustRatio : 1) / grids + "px"
    };
    if (!doc.addEventListener) {
        return
    }
    var resizeEvt = "orientationchange" in win ? "orientationchange" : "resize";
    win.addEventListener(resizeEvt, reCalc, false);
    doc.addEventListener("DOMContentLoaded", reCalc, false)
})(window);
