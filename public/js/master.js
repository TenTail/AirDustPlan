$(document).ready(function() {

    var s = $(document).height();
    var header_h = $('.header').height();
    var nav_h = $('.navigation').height();
    var footer_h = $('.footer').height();

    $('#belowtopnav').css('min-height', s-header_h-nav_h-footer_h);

    $(window).scroll(function () { 
        // console.log($(window).scrollTop());
        if ($(window).scrollTop() > 300) {
            $(".navigation").addClass("navbar-fixed-top");
            $("#belowtopnav").css({"padding-top" : "50px"});
        }

        if ($(window).scrollTop() < 301) {
            $(".navigation").removeClass("navbar-fixed-top");
            $("#belowtopnav").css({"padding-top" : ""});
        }
    });
});