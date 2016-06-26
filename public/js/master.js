$(document).ready(function() {

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