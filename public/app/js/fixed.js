;(function(designPercent){
	var mainWidth = document.body.offsetWidth;
    var fontSize = mainWidth/designPercent + 'px';
    document.documentElement.style.fontSize = fontSize;
//视窗变化时需要再次适配，这种情况实际价值不是很大！最主要的只是首次适配
    window.onresize = function(){
        var mainWidth = document.body.clientWidth;
        var fontSize = mainWidth/designPercent + 'px';
        document.documentElement.style.fontSize = fontSize;
    }
})(750/40);
