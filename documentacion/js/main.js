jQuery(document).ready(function(t) {
    function e(e) {
        e.preventDefault(), i.removeClass("slide-in").find("li").show(), c.removeClass("move-left"), t("body").removeClass("cd-overlay")
    }

    function s() {
        a(), o()
    }

    function a() {
        var e = t(".cd-full").offset().top,
            s = jQuery(".cd-full").height() - jQuery(".left-section").height(),
            a = 20;
        if (e - a <= t(window).scrollTop() && e - a + s > t(window).scrollTop()) {
            var o = d.offset().left;
            d.width();
            d.addClass("is-fixed").css({
                left: o,
                top: a,
                "-moz-transform": "translateZ(0)",
                "-webkit-transform": "translateZ(0)",
                "-ms-transform": "translateZ(0)",
                "-o-transform": "translateZ(0)",
                transform: "translateZ(0)"
            })
        } else if (e - a + s <= t(window).scrollTop()) {
            var r = e - a + s - t(window).scrollTop();
            d.css({
                "-moz-transform": "translateZ(0) translateY(" + r + "px)",
                "-webkit-transform": "translateZ(0) translateY(" + r + "px)",
                "-ms-transform": "translateZ(0) translateY(" + r + "px)",
                "-o-transform": "translateZ(0) translateY(" + r + "px)",
                transform: "translateZ(0) translateY(" + r + "px)"
            })
        } else d.removeClass("is-fixed").css({
            left: 0,
            top: 0
        })
    }

    function o() {
        l.each(function() {
            var e = t(this),
                s = parseInt(t(".s-title").eq(1).css("marginTop").replace("px", "")),
                a = t('.left-section a[href="#' + e.attr("id") + '"]'),
                o = a.parent("li").is(":first-child") ? 0 : Math.round(e.offset().top);
            o - 20 <= t(window).scrollTop() && Math.round(e.offset().top) + e.height() + s - 20 > t(window).scrollTop() ? a.addClass("selected") : a.removeClass("selected")
        })
    }
    var r = 768,
        n = 1024,
        l = t(".cd-full-group"),
        i = t(".right-section"),
        d = t(".left-section"),
        f = d.find("a"),
        c = t(".cd-close-panel");
    f.on("click", function(e) {
        e.preventDefault();
        var s = t(this).attr("href"),
            a = t(s);
        t(window).width() < r ? (i.scrollTop(0).addClass("slide-in").children("ul").removeClass("selected").end().children(s).addClass("selected"), c.addClass("move-left"), t("body").addClass("cd-overlay")) : t("body,html").animate({
            scrollTop: a.offset().top - 19
        }, 200)
    }), t("body").bind("click touchstart", function(s) {
        (t(s.target).is("body.cd-overlay") || t(s.target).is(".cd-close-panel")) && e(s)
    }), i.on("swiperight", function(t) {
        e(t)
    }), t(window).on("scroll", function() {
        t(window).width() > n && (window.requestAnimationFrame ? window.requestAnimationFrame(s) : s())
    }), t(window).on("resize", function() {
        t(window).width() <= n && d.removeClass("is-fixed").css({
            "-moz-transform": "translateY(0)",
            "-webkit-transform": "translateY(0)",
            "-ms-transform": "translateY(0)",
            "-o-transform": "translateY(0)",
            transform: "translateY(0)"
        }), d.hasClass("is-fixed") && d.css({
            left: i.offset().left
        })
    })
});