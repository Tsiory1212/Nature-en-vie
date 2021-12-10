!(function (i) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], i) : "undefined" != typeof exports ? (module.exports = i(require("jquery"))) : i(jQuery);
})(function (i) {
    "use strict";
    var e = window.Slick || {};
    (e = (function () {
        function e(e, s) {
            var o,
                n = this;
            (n.defaults = {
                accessibility: !0,
                adaptiveHeight: !1,
                appendArrows: i(e),
                appendDots: i(e),
                arrows: !0,
                asNavFor: null,
                prevArrow: '<button class="slick-prev" aria-label="Previous" type="button">Previous</button>',
                nextArrow: '<button class="slick-next" aria-label="Next" type="button">Next</button>',
                autoplay: !1,
                autoplaySpeed: 3e3,
                centerMode: !1,
                centerPadding: "50px",
                slidesMargin: 30,
                cssEase: "ease",
                customPaging: function (e, t) {
                    return i('<button type="button" />').text(t + 1);
                },
                dots: !1,
                dotsClass: "slick-dots",
                draggable: !0,
                easing: "linear",
                edgeFriction: 0.35,
                fade: !1,
                focusOnSelect: !1,
                focusOnChange: !1,
                infinite: !0,
                initialSlide: 0,
                lazyLoad: "ondemand",
                mobileFirst: !1,
                pauseOnHover: !0,
                pauseOnFocus: !0,
                pauseOnDotsHover: !1,
                respondTo: "window",
                responsive: null,
                rows: 1,
                rtl: !1,
                slide: "",
                slidesPerRow: 1,
                slidesToShow: 1,
                slidesToScroll: 1,
                speed: 500,
                swipe: !0,
                swipeToSlide: !0,
                touchMove: !0,
                touchThreshold: 5,
                useCSS: !0,
                useTransform: !0,
                variableWidth: !1,
                vertical: !1,
                verticalSwiping: !1,
                waitForAnimate: !0,
                zIndex: 1e3,
            }),
                (n.initials = {
                    animating: !1,
                    dragging: !1,
                    autoPlayTimer: null,
                    currentDirection: 0,
                    currentLeft: null,
                    currentSlide: 0,
                    direction: 1,
                    $dots: null,
                    listWidth: null,
                    listHeight: null,
                    loadIndex: 0,
                    $nextArrow: null,
                    $prevArrow: null,
                    scrolling: !1,
                    slideCount: null,
                    slideWidth: null,
                    $slideTrack: null,
                    $slides: null,
                    sliding: !1,
                    slideOffset: 0,
                    swipeLeft: null,
                    swiping: !1,
                    $list: null,
                    touchObject: {},
                    transformsEnabled: !1,
                    unslicked: !1,
                }),
                i.extend(n, n.initials),
                (n.activeBreakpoint = null),
                (n.animType = null),
                (n.animProp = null),
                (n.breakpoints = []),
                (n.breakpointSettings = []),
                (n.cssTransitions = !1),
                (n.focussed = !1),
                (n.interrupted = !1),
                (n.hidden = "hidden"),
                (n.paused = !0),
                (n.positionProp = null),
                (n.respondTo = null),
                (n.rowCount = 1),
                (n.shouldClick = !0),
                (n.$slider = i(e)),
                (n.$slidesCache = null),
                (n.transformType = null),
                (n.transitionType = null),
                (n.visibilityChange = "visibilitychange"),
                (n.windowWidth = 0),
                (n.windowTimer = null),
                (o = i(e).data("slick") || {}),
                (n.options = i.extend({}, n.defaults, s, o)),
                n.options.fade === !0 && (n.options.slidesMargin = 0),
                n.options.vertical === !0 && (n.options.variableWidth = !1),
                (n.currentSlide = n.options.initialSlide),
                (n.originalSettings = n.options),
                "undefined" != typeof document.mozHidden
                    ? ((n.hidden = "mozHidden"), (n.visibilityChange = "mozvisibilitychange"))
                    : "undefined" != typeof document.webkitHidden && ((n.hidden = "webkitHidden"), (n.visibilityChange = "webkitvisibilitychange")),
                (n.autoPlay = i.proxy(n.autoPlay, n)),
                (n.autoPlayClear = i.proxy(n.autoPlayClear, n)),
                (n.autoPlayIterator = i.proxy(n.autoPlayIterator, n)),
                (n.changeSlide = i.proxy(n.changeSlide, n)),
                (n.clickHandler = i.proxy(n.clickHandler, n)),
                (n.selectHandler = i.proxy(n.selectHandler, n)),
                (n.setPosition = i.proxy(n.setPosition, n)),
                (n.swipeHandler = i.proxy(n.swipeHandler, n)),
                (n.dragHandler = i.proxy(n.dragHandler, n)),
                (n.keyHandler = i.proxy(n.keyHandler, n)),
                (n.instanceUid = t++),
                (n.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/),
                n.registerBreakpoints(),
                n.init(!0);
        }
        var t = 0;
        return e;
    })()),
        (e.prototype.activateADA = function () {
            var i = this;
            i.$slideTrack.find(".slick-active").attr({ "aria-hidden": "false" }).find("a, input, button, select").attr({ tabindex: "0" });
        }),
        (e.prototype.addSlide = e.prototype.slickAdd = function (e, t, s) {
            var o = this;
            if ("boolean" == typeof t) (s = t), (t = null);
            else if (0 > t || t >= o.slideCount) return !1;
            o.unload(),
                "number" == typeof t
                    ? 0 === t && 0 === o.$slides.length
                        ? i(e).appendTo(o.$slideTrack)
                        : s
                        ? i(e).insertBefore(o.$slides.eq(t))
                        : i(e).insertAfter(o.$slides.eq(t))
                    : s === !0
                    ? i(e).prependTo(o.$slideTrack)
                    : i(e).appendTo(o.$slideTrack),
                (o.$slides = o.$slideTrack.children(this.options.slide)),
                o.$slideTrack.children(this.options.slide).detach(),
                o.$slideTrack.append(o.$slides),
                o.$slides.each(function (e, t) {
                    i(t).attr("data-slick-index", e);
                }),
                (o.$slidesCache = o.$slides),
                o.reinit();
        }),
        (e.prototype.animateHeight = function () {
            var i = this;
            if (1 === i.options.slidesToShow && i.options.adaptiveHeight === !0 && i.options.vertical === !1) {
                var e = i.$slides.eq(i.currentSlide).outerHeight(!0);
                i.$list.animate({ height: e }, i.options.speed);
            }
        }),
        (e.prototype.animateSlide = function (e, t) {
            var s = {},
                o = this;
            o.animateHeight(),
                o.options.rtl === !0 && o.options.vertical === !1 && (e = -e),
                o.transformsEnabled === !1
                    ? o.options.vertical === !1
                        ? o.$slideTrack.animate({ left: e }, o.options.speed, o.options.easing, t)
                        : o.$slideTrack.animate({ top: e }, o.options.speed, o.options.easing, t)
                    : o.cssTransitions === !1
                    ? (o.options.rtl === !0 && (o.currentLeft = -o.currentLeft),
                      i({ animStart: o.currentLeft }).animate(
                          { animStart: e },
                          {
                              duration: o.options.speed,
                              easing: o.options.easing,
                              step: function (i) {
                                  (i = Math.ceil(i)), o.options.vertical === !1 ? ((s[o.animType] = "translate(" + i + "px, 0px)"), o.$slideTrack.css(s)) : ((s[o.animType] = "translate(0px," + i + "px)"), o.$slideTrack.css(s));
                              },
                              complete: function () {
                                  t && t.call();
                              },
                          }
                      ))
                    : (o.applyTransition(),
                      (e = Math.ceil(e)),
                      o.options.vertical === !1 ? (s[o.animType] = "translate3d(" + e + "px, 0px, 0px)") : (s[o.animType] = "translate3d(0px," + e + "px, 0px)"),
                      o.$slideTrack.css(s),
                      t &&
                          setTimeout(function () {
                              o.disableTransition(), t.call();
                          }, o.options.speed));
        }),
        (e.prototype.getNavTarget = function () {
            var e = this,
                t = e.options.asNavFor;
            return t && null !== t && (t = i(t).not(e.$slider)), t;
        }),
        (e.prototype.asNavFor = function (e) {
            var t = this,
                s = t.getNavTarget();
            null !== s &&
                "object" == typeof s &&
                s.each(function () {
                    var t = i(this).slick("getSlick");
                    t.unslicked || t.slideHandler(e, !0);
                });
        }),
        (e.prototype.applyTransition = function (i) {
            var e = this,
                t = {};
            e.options.fade === !1 ? (t[e.transitionType] = e.transformType + " " + e.options.speed + "ms " + e.options.cssEase) : (t[e.transitionType] = "opacity " + e.options.speed + "ms " + e.options.cssEase),
                e.options.fade === !1 ? e.$slideTrack.css(t) : e.$slides.eq(i).css(t);
        }),
        (e.prototype.autoPlay = function () {
            var i = this;
            i.autoPlayClear(), i.slideCount > i.options.slidesToShow && (i.autoPlayTimer = setInterval(i.autoPlayIterator, i.options.autoplaySpeed));
        }),
        (e.prototype.autoPlayClear = function () {
            var i = this;
            i.autoPlayTimer && clearInterval(i.autoPlayTimer);
        }),
        (e.prototype.autoPlayIterator = function () {
            var i = this,
                e = i.currentSlide + i.options.slidesToScroll;
            i.paused ||
                i.interrupted ||
                i.focussed ||
                (i.options.infinite === !1 &&
                    (1 === i.direction && i.currentSlide + 1 === i.slideCount - 1 ? (i.direction = 0) : 0 === i.direction && ((e = i.currentSlide - i.options.slidesToScroll), i.currentSlide - 1 === 0 && (i.direction = 1))),
                i.slideHandler(e));
        }),
        (e.prototype.buildArrows = function () {
            var e = this;
            e.options.arrows === !0 &&
                ((e.$prevArrow = i(e.options.prevArrow).addClass("slick-arrow")),
                (e.$nextArrow = i(e.options.nextArrow).addClass("slick-arrow")),
                e.slideCount > e.options.slidesToShow
                    ? (e.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),
                      e.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),
                      e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.prependTo(e.options.appendArrows),
                      e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.appendTo(e.options.appendArrows),
                      e.options.infinite !== !0 && e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"))
                    : e.$prevArrow.add(e.$nextArrow).addClass("slick-hidden").attr({ "aria-disabled": "true", tabindex: "-1" }));
        }),
        (e.prototype.buildDots = function () {
            var e,
                t,
                s = this;
            if (s.options.dots === !0 && s.slideCount > s.options.slidesToShow) {
                for (s.$slider.addClass("slick-dotted"), t = i("<ul />").addClass(s.options.dotsClass), e = 0; e <= s.getDotCount(); e += 1) t.append(i("<li />").append(s.options.customPaging.call(this, s, e)));
                (s.$dots = t.appendTo(s.options.appendDots)), s.$dots.find("li").first().addClass("slick-active");
            }
        }),
        (e.prototype.buildOut = function () {
            var e = this;
            (e.$slides = e.$slider.children(e.options.slide + ":not(.slick-cloned)").addClass("slick-slide")),
                (e.slideCount = e.$slides.length),
                e.$slides.each(function (e, t) {
                    i(t)
                        .attr("data-slick-index", e)
                        .data("originalStyling", i(t).attr("style") || "");
                }),
                e.$slider.addClass("slick-slider"),
                1 === e.options.slidesToShow && (e.options.centerMode = !1),
                1 == e.options.centerMode && e.$slider.addClass("slick-center-mode"),
                (e.$slideTrack = 0 === e.slideCount ? i('<div class="slick-track"/>').appendTo(e.$slider) : e.$slides.wrapAll('<div class="slick-track"/>').parent()),
                (e.$list = e.$slideTrack.wrap('<div class="slick-list"/>').parent()),
                e.$slideTrack.css("opacity", 0),
                (e.options.centerMode === !0 || e.options.swipeToSlide === !0) && (e.options.slidesToScroll = 1),
                i("img[data-lazy]", e.$slider).not("[src]").addClass("slick-loading"),
                e.setupInfinite(),
                e.buildArrows(),
                e.buildDots(),
                e.updateDots(),
                e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0),
                e.options.draggable === !0 && e.$list.addClass("draggable");
        }),
        (e.prototype.buildRows = function () {
            var i,
                e,
                t,
                s,
                o,
                n,
                r,
                l = this;
            if (((s = document.createDocumentFragment()), (n = l.$slider.children()), l.options.rows > 1)) {
                for (r = l.options.slidesPerRow * l.options.rows, o = Math.ceil(n.length / r), i = 0; o > i; i++) {
                    var a = document.createElement("div");
                    for (e = 0; e < l.options.rows; e++) {
                        var d = document.createElement("div");
                        for (d.className = "row-item", t = 0; t < l.options.slidesPerRow; t++) {
                            var c = i * r + (e * l.options.slidesPerRow + t);
                            n.get(c) && d.appendChild(n.get(c));
                        }
                        a.appendChild(d);
                    }
                    s.appendChild(a);
                }
                l.$slider.empty().append(s),
                    l.$slider
                        .children()
                        .children()
                        .children()
                        .css({ width: 100 / l.options.slidesPerRow + "%", display: "inline-block" });
            }
        }),
        (e.prototype.checkResponsive = function (e, t) {
            var s,
                o,
                n,
                r = this,
                l = !1,
                a = r.$slider.width(),
                d = window.innerWidth || i(window).width();
            if (("window" === r.respondTo ? (n = d) : "slider" === r.respondTo ? (n = a) : "min" === r.respondTo && (n = Math.min(d, a)), r.options.responsive && r.options.responsive.length && null !== r.options.responsive)) {
                o = null;
                for (s in r.breakpoints) r.breakpoints.hasOwnProperty(s) && (r.originalSettings.mobileFirst === !1 ? n < r.breakpoints[s] && (o = r.breakpoints[s]) : n > r.breakpoints[s] && (o = r.breakpoints[s]));
                null !== o
                    ? null !== r.activeBreakpoint
                        ? (o !== r.activeBreakpoint || t) &&
                          ((r.activeBreakpoint = o),
                          "unslick" === r.breakpointSettings[o] ? r.unslick(o) : ((r.options = i.extend({}, r.originalSettings, r.breakpointSettings[o])), e === !0 && (r.currentSlide = r.options.initialSlide), r.refresh(e)),
                          (l = o))
                        : ((r.activeBreakpoint = o),
                          "unslick" === r.breakpointSettings[o] ? r.unslick(o) : ((r.options = i.extend({}, r.originalSettings, r.breakpointSettings[o])), e === !0 && (r.currentSlide = r.options.initialSlide), r.refresh(e)),
                          (l = o))
                    : null !== r.activeBreakpoint && ((r.activeBreakpoint = null), (r.options = r.originalSettings), e === !0 && (r.currentSlide = r.options.initialSlide), r.refresh(e), (l = o)),
                    e || l === !1 || r.$slider.trigger("breakpoint", [r, l]);
            }
        }),
        (e.prototype.changeSlide = function (e, t) {
            var s,
                o,
                n,
                r = this,
                l = i(e.currentTarget);
            switch ((l.is("a") && e.preventDefault(), l.is("li") || (l = l.closest("li")), (n = r.slideCount % r.options.slidesToScroll !== 0), (s = n ? 0 : (r.slideCount - r.currentSlide) % r.options.slidesToScroll), e.data.message)) {
                case "previous":
                    (o = 0 === s ? r.options.slidesToScroll : r.options.slidesToShow - s), r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide - o, !1, t);
                    break;
                case "next":
                    (o = 0 === s ? r.options.slidesToScroll : s), r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide + o, !1, t);
                    break;
                case "index":
                    var a = 0 === e.data.index ? 0 : e.data.index || l.index() * r.options.slidesToScroll;
                    r.slideHandler(r.checkNavigable(a), !1, t), l.children().trigger("focus");
                    break;
                default:
                    return;
            }
        }),
        (e.prototype.checkNavigable = function (i) {
            var e,
                t,
                s = this;
            if (((e = s.getNavigableIndexes()), (t = 0), i > e[e.length - 1])) i = e[e.length - 1];
            else
                for (var o in e) {
                    if (i < e[o]) {
                        i = t;
                        break;
                    }
                    t = e[o];
                }
            return i;
        }),
        (e.prototype.cleanUpEvents = function () {
            var e = this;
            e.options.dots &&
                null !== e.$dots &&
                (i("li", e.$dots).off("click.slick", e.changeSlide).off("mouseenter.slick", i.proxy(e.interrupt, e, !0)).off("mouseleave.slick", i.proxy(e.interrupt, e, !1)),
                e.options.accessibility === !0 && e.$dots.off("keydown.slick", e.keyHandler)),
                e.$slider.off("focus.slick blur.slick"),
                e.options.arrows === !0 &&
                    e.slideCount > e.options.slidesToShow &&
                    (e.$prevArrow && e.$prevArrow.off("click.slick", e.changeSlide),
                    e.$nextArrow && e.$nextArrow.off("click.slick", e.changeSlide),
                    e.options.accessibility === !0 && (e.$prevArrow && e.$prevArrow.off("keydown.slick", e.keyHandler), e.$nextArrow && e.$nextArrow.off("keydown.slick", e.keyHandler))),
                e.$list.off("touchstart.slick mousedown.slick", e.swipeHandler),
                e.$list.off("touchmove.slick mousemove.slick", e.swipeHandler),
                e.$list.off("touchend.slick mouseup.slick", e.swipeHandler),
                e.$list.off("touchcancel.slick mouseleave.slick", e.swipeHandler),
                e.$list.off("click.slick", e.clickHandler),
                i(document).off(e.visibilityChange, e.visibility),
                e.cleanUpSlideEvents(),
                e.options.accessibility === !0 && e.$list.off("keydown.slick", e.keyHandler),
                e.options.focusOnSelect === !0 && i(e.$slideTrack).children().off("click.slick", e.selectHandler),
                i(window).off("orientationchange.slick.slick-" + e.instanceUid, e.orientationChange),
                i(window).off("resize.slick.slick-" + e.instanceUid, e.resize),
                i("[draggable!=true]", e.$slideTrack).off("dragstart", e.preventDefault),
                i(window).off("load.slick.slick-" + e.instanceUid, e.setPosition);
        }),
        (e.prototype.cleanUpSlideEvents = function () {
            var e = this;
            e.$list.off("mouseenter.slick", i.proxy(e.interrupt, e, !0)), e.$list.off("mouseleave.slick", i.proxy(e.interrupt, e, !1));
        }),
        (e.prototype.cleanUpRows = function () {
            var i,
                e = this;
            e.options.rows > 0 && e.$slider.find(".row-item").length && ((i = e.$slides.children().children()), i.removeAttr("style"), i.removeClass("first-slick last-slick"), e.$slider.empty().append(i));
        }),
        (e.prototype.clickHandler = function (i) {
            var e = this;
            e.shouldClick === !1 && (i.stopImmediatePropagation(), i.stopPropagation(), i.preventDefault());
        }),
        (e.prototype.destroy = function (e) {
            var t = this;
            t.autoPlayClear(),
                (t.touchObject = {}),
                t.cleanUpEvents(),
                i(".slick-cloned", t.$slider).detach(),
                t.$dots && t.$dots.remove(),
                t.$prevArrow &&
                    t.$prevArrow.length &&
                    (t.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.prevArrow) && t.$prevArrow.remove()),
                t.$nextArrow &&
                    t.$nextArrow.length &&
                    (t.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.nextArrow) && t.$nextArrow.remove()),
                t.$slides &&
                    (t.$slides
                        .removeClass("slick-slide slick-active slick-center slick-visible slick-current")
                        .removeAttr("aria-hidden")
                        .removeAttr("data-slick-index")
                        .each(function () {
                            i(this).attr("style", i(this).data("originalStyling"));
                        }),
                    t.$slideTrack.children(this.options.slide).detach(),
                    t.$slideTrack.detach(),
                    t.$list.detach(),
                    t.$slider.append(t.$slides)),
                t.cleanUpRows(),
                t.$slider.removeClass("slick-slider"),
                t.$slider.removeClass("slick-center-mode"),
                t.$slider.removeClass("slick-initialized"),
                t.$slider.removeClass("slick-dotted"),
                (t.unslicked = !0),
                e || t.$slider.trigger("destroy", [t]);
        }),
        (e.prototype.disableTransition = function (i) {
            var e = this,
                t = {};
            (t[e.transitionType] = ""), e.options.fade === !1 ? e.$slideTrack.css(t) : e.$slides.eq(i).css(t);
        }),
        (e.prototype.fadeSlide = function (i, e) {
            var t = this;
            t.cssTransitions === !1
                ? (t.$slides.eq(i).css({ zIndex: t.options.zIndex }), t.$slides.eq(i).animate({ opacity: 1 }, t.options.speed, t.options.easing, e))
                : (t.applyTransition(i),
                  t.$slides.eq(i).css({ opacity: 1, zIndex: t.options.zIndex }),
                  e &&
                      setTimeout(function () {
                          t.disableTransition(i), e.call();
                      }, t.options.speed));
        }),
        (e.prototype.fadeSlideOut = function (i) {
            var e = this;
            e.cssTransitions === !1 ? e.$slides.eq(i).animate({ opacity: 0, zIndex: e.options.zIndex - 2 }, e.options.speed, e.options.easing) : (e.applyTransition(i), e.$slides.eq(i).css({ opacity: 0, zIndex: e.options.zIndex - 2 }));
        }),
        (e.prototype.filterSlides = e.prototype.slickFilter = function (i) {
            var e = this;
            null !== i && ((e.$slidesCache = e.$slides), e.unload(), e.$slideTrack.children(this.options.slide).detach(), e.$slidesCache.filter(i).appendTo(e.$slideTrack), e.reinit());
        }),
        (e.prototype.focusHandler = function () {
            var e = this;
            e.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick", "*", function (t) {
                t.stopImmediatePropagation();
                var s = i(this);
                setTimeout(function () {
                    e.options.pauseOnFocus && ((e.focussed = s.is(":focus")), e.autoPlay());
                }, 0);
            });
        }),
        (e.prototype.getCurrent = e.prototype.slickCurrentSlide = function () {
            var i = this;
            return i.currentSlide;
        }),
        (e.prototype.getDotCount = function () {
            var i = this,
                e = 0,
                t = 0,
                s = 0;
            if (i.options.infinite === !0)
                if (i.slideCount <= i.options.slidesToShow) ++s;
                else for (; e < i.slideCount; ) ++s, (e = t + i.options.slidesToScroll), (t += i.options.slidesToScroll <= i.options.slidesToShow ? i.options.slidesToScroll : i.options.slidesToShow);
            else if (i.options.centerMode === !0) s = i.slideCount;
            else if (i.options.asNavFor) for (; e < i.slideCount; ) ++s, (e = t + i.options.slidesToScroll), (t += i.options.slidesToScroll <= i.options.slidesToShow ? i.options.slidesToScroll : i.options.slidesToShow);
            else s = 1 + Math.ceil((i.slideCount - i.options.slidesToShow) / i.options.slidesToScroll);
            return s - 1;
        }),
        (e.prototype.getValueMargin = function () {
            var i = this.options.slidesMargin;
            return this.options.fade === !0 && (i = 0), i;
        }),
        (e.prototype.getLeft = function (i) {
            var e,
                t,
                s,
                o,
                n = this,
                r = 0,
                l = 0,
                a = 0,
                d = 0;
            return (
                (a = n.getValueMargin()),
                (d = (n.listWidth - (n.options.slidesToShow - 1) * n.options.slidesMargin) / n.options.slidesToShow + n.options.slidesMargin),
                (r = Math.abs(a / n.options.slidesToShow)),
                (e = n.$slides.first().outerHeight(!0)),
                (n.slideOffset = -(i * r)),
                n.options.infinite === !0
                    ? (n.slideCount > n.options.slidesToShow &&
                          ((n.slideOffset = -1 * (n.slideWidth * n.options.slidesToShow + a + i * r)),
                          (o = -1),
                          n.options.vertical === !0 && n.options.centerMode === !0 && (2 === n.options.slidesToShow ? (o = -1.5) : 1 === n.options.slidesToShow && (o = -2)),
                          (l = e * n.options.slidesToShow * o)),
                      n.slideCount % n.options.slidesToScroll !== 0 &&
                          i + n.options.slidesToScroll > n.slideCount &&
                          n.slideCount > n.options.slidesToShow &&
                          (i > n.slideCount
                              ? ((n.slideOffset = (n.options.slidesToShow - (i - n.slideCount)) * n.slideWidth * -1), (l = (n.options.slidesToShow - (i - n.slideCount)) * e * -1))
                              : ((n.slideOffset = -1 * ((n.slideCount % n.options.slidesToScroll) * n.slideWidth + n.slideWidth / 2 + n.options.valueMargin / 2)), (l = (n.slideCount % n.options.slidesToScroll) * e * -1))))
                    : i + n.options.slidesToShow > n.slideCount && 1 === n.options.slidesToScroll && ((n.slideOffset = (i + n.options.slidesToShow - n.slideCount) * n.slideWidth - r), (l = (i + n.options.slidesToShow - n.slideCount) * e)),
                n.slideCount <= n.options.slidesToShow && ((n.slideOffset = 0), (l = 0)),
                n.options.centerMode === !0 &&
                    n.slideCount > n.options.slidesToShow &&
                    (n.options.centerMode === !0 && n.options.infinite === !0
                        ? 2 === n.options.slidesToShow
                            ? (n.slideOffset -= d * Math.abs(0.5))
                            : (n.slideOffset += d * Math.abs((n.options.slidesToShow - 3) / 2))
                        : n.options.centerMode === !0 && (n.slideOffset += d * Math.abs((n.options.slidesToShow - 1) / 2))),
                (s = n.options.vertical === !1 ? i * n.slideWidth * -1 + n.slideOffset : i * e * -1 + l),
                n.options.variableWidth === !0 &&
                    ((t = n.slideCount <= n.options.slidesToShow || n.options.infinite === !1 ? n.$slideTrack.children(".slick-slide").eq(i) : n.$slideTrack.children(".slick-slide").eq(i + n.options.slidesToShow)),
                    (s = n.options.rtl === !0 ? (t[0] ? -1 * (n.$slideTrack.width() - t[0].offsetLeft - t.width()) : 0) : t[0] ? -1 * t[0].offsetLeft : 0),
                    n.options.centerMode === !0 &&
                        ((t = n.slideCount <= n.options.slidesToShow || n.options.infinite === !1 ? n.$slideTrack.children(".slick-slide").eq(i) : n.$slideTrack.children(".slick-slide").eq(i + n.options.slidesToShow + 1)),
                        (s = n.options.rtl === !0 ? (t[0] ? -1 * (n.$slideTrack.width() - t[0].offsetLeft - t.width()) : 0) : t[0] ? -1 * t[0].offsetLeft : 0),
                        (s += (n.$list.width() - t.outerWidth()) / 2))),
                s
            );
        }),
        (e.prototype.getOption = e.prototype.slickGetOption = function (i) {
            var e = this;
            return e.options[i];
        }),
        (e.prototype.getNavigableIndexes = function () {
            var i,
                e = this,
                t = 0,
                s = 0,
                o = [];
            for (e.options.infinite === !1 ? (i = e.slideCount) : ((t = -1 * e.options.slideCount), (s = -1 * e.options.slideCount), (i = 2 * e.slideCount)); i > t; )
                o.push(t), (t = s + e.options.slidesToScroll), (s += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow);
            return o;
        }),
        (e.prototype.getSlick = function () {
            return this;
        }),
        (e.prototype.getSlideCount = function () {
            var e,
                t,
                s,
                o,
                n = this;
            return (
                (o = n.options.centerMode === !0 ? n.slideWidth * Math.floor(n.options.slidesToShow / 2) : 0),
                n.options.swipeToSlide === !0
                    ? (n.$slideTrack.find(".slick-slide").each(function (e, t) {
                          return t.offsetLeft - o + i(t).outerWidth() / 2 > -1 * n.swipeLeft ? ((s = t), !1) : void 0;
                      }),
                      (e = Math.abs(i(s).data("slick-index") - n.currentSlide) || 1),
                      (t = Math.abs(i(s).data("slick-index")) + 1),
                      t + e >= n.slideCount && n.options.infinite === !1 && n.options.centerMode === !1 && (e = Math.abs(n.slideCount - t) || 1),
                      e)
                    : n.options.slidesToScroll
            );
        }),
        (e.prototype.goTo = e.prototype.slickGoTo = function (i, e) {
            var t = this;
            t.changeSlide({ data: { message: "index", index: parseInt(i) } }, e);
        }),
        (e.prototype.init = function (e) {
            var t = this;
            i(t.$slider).hasClass("slick-initialized") ||
                (i(t.$slider).addClass("slick-initialized"), t.buildRows(), t.buildOut(), t.setProps(), t.startLoad(), t.loadSlider(), t.initializeEvents(), t.updateArrows(), t.updateDots(), t.checkResponsive(!0), t.focusHandler()),
                e && t.$slider.trigger("init", [t]),
                t.options.accessibility === !0 && t.initADA(),
                t.options.autoplay && ((t.paused = !1), t.autoPlay());
        }),
        (e.prototype.initADA = function () {
            var e = this,
                t = Math.ceil(e.slideCount / e.options.slidesToShow),
                s = e.getNavigableIndexes().filter(function (i) {
                    return i >= 0 && i < e.slideCount;
                });
            e.$slides.add(e.$slideTrack.find(".slick-cloned")).attr({ "aria-hidden": "true", tabindex: "-1" }).find("a, input, button, select").attr({ tabindex: "-1" }),
                null !== e.$dots &&
                    (e.$slides.not(e.$slideTrack.find(".slick-cloned")).each(function (t) {
                        var o = s.indexOf(t);
                        i(this).attr({ role: "tabpanel", id: "slick-slide" + e.instanceUid + t, tabindex: -1 }), -1 !== o && i(this).attr({ "aria-describedby": "slick-slide-control" + e.instanceUid + o });
                    }),
                    e.$dots
                        .attr("role", "tablist")
                        .find("li")
                        .each(function (o) {
                            var n = s[o];
                            i(this).attr({ role: "presentation" }),
                                i(this)
                                    .find("button")
                                    .first()
                                    .attr({ role: "tab", id: "slick-slide-control" + e.instanceUid + o, "aria-controls": "slick-slide" + e.instanceUid + n, "aria-label": o + 1 + " of " + t, "aria-selected": null, tabindex: "-1" });
                        })
                        .eq(e.currentSlide)
                        .find("button")
                        .attr({ "aria-selected": "true", tabindex: "0" })
                        .end());
            for (var o = e.currentSlide, n = o + e.options.slidesToShow; n > o; o++) e.$slides.eq(o).attr("tabindex", 0);
            e.activateADA();
        }),
        (e.prototype.initArrowEvents = function () {
            var i = this;
            i.options.arrows === !0 &&
                i.slideCount > i.options.slidesToShow &&
                (i.$prevArrow.off("click.slick").on("click.slick", { message: "previous" }, i.changeSlide),
                i.$nextArrow.off("click.slick").on("click.slick", { message: "next" }, i.changeSlide),
                i.options.accessibility === !0 && (i.$prevArrow.on("keydown.slick", i.keyHandler), i.$nextArrow.on("keydown.slick", i.keyHandler)));
        }),
        (e.prototype.initDotEvents = function () {
            var e = this;
            e.options.dots === !0 && e.slideCount > e.options.slidesToShow && (i("li", e.$dots).on("click.slick", { message: "index" }, e.changeSlide), e.options.accessibility === !0 && e.$dots.on("keydown.slick", e.keyHandler)),
                e.options.dots === !0 && e.options.pauseOnDotsHover === !0 && i("li", e.$dots).on("mouseenter.slick", i.proxy(e.interrupt, e, !0)).on("mouseleave.slick", i.proxy(e.interrupt, e, !1));
        }),
        (e.prototype.initSlideEvents = function () {
            var e = this;
            e.options.pauseOnHover && (e.$list.on("mouseenter.slick", i.proxy(e.interrupt, e, !0)), e.$list.on("mouseleave.slick", i.proxy(e.interrupt, e, !1)));
        }),
        (e.prototype.initializeEvents = function () {
            var e = this;
            e.initArrowEvents(),
                e.initDotEvents(),
                e.initSlideEvents(),
                e.$list.on("touchstart.slick mousedown.slick", { action: "start" }, e.swipeHandler),
                e.$list.on("touchmove.slick mousemove.slick", { action: "move" }, e.swipeHandler),
                e.$list.on("touchend.slick mouseup.slick", { action: "end" }, e.swipeHandler),
                e.$list.on("touchcancel.slick mouseleave.slick", { action: "end" }, e.swipeHandler),
                e.$list.on("click.slick", e.clickHandler),
                i(document).on(e.visibilityChange, i.proxy(e.visibility, e)),
                e.options.accessibility === !0 && e.$list.on("keydown.slick", e.keyHandler),
                e.options.focusOnSelect === !0 && i(e.$slideTrack).children().on("click.slick", e.selectHandler),
                i(window).on("orientationchange.slick.slick-" + e.instanceUid, i.proxy(e.orientationChange, e)),
                i(window).on("resize.slick.slick-" + e.instanceUid, i.proxy(e.resize, e)),
                i("[draggable!=true]", e.$slideTrack).on("dragstart", e.preventDefault),
                i(window).on("load.slick.slick-" + e.instanceUid, e.setPosition),
                i(e.setPosition);
        }),
        (e.prototype.initUI = function () {
            var i = this;
            i.options.arrows === !0 && i.slideCount > i.options.slidesToShow && (i.$prevArrow.show(), i.$nextArrow.show()), i.options.dots === !0 && i.slideCount > i.options.slidesToShow && i.$dots.show();
        }),
        (e.prototype.keyHandler = function (i) {
            var e = this;
            i.target.tagName.match("TEXTAREA|INPUT|SELECT") ||
                (37 === i.keyCode && e.options.accessibility === !0
                    ? e.changeSlide({ data: { message: e.options.rtl === !0 ? "next" : "previous" } })
                    : 39 === i.keyCode && e.options.accessibility === !0 && e.changeSlide({ data: { message: e.options.rtl === !0 ? "previous" : "next" } }));
        }),
        (e.prototype.lazyLoad = function () {
            function e(e) {
                i("img[data-lazy]", e).each(function () {
                    var e = i(this),
                        t = i(this).attr("data-lazy"),
                        s = i(this).attr("data-srcset"),
                        o = i(this).attr("data-sizes") || r.$slider.attr("data-sizes"),
                        n = document.createElement("img");
                    (n.onload = function () {
                        e.animate({ opacity: 0 }, 100, function () {
                            s && (e.attr("srcset", s), o && e.attr("sizes", o)),
                                e.attr("src", t).animate({ opacity: 1 }, 200, function () {
                                    e.removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading");
                                }),
                                r.$slider.trigger("lazyLoaded", [r, e, t]);
                        });
                    }),
                        (n.onerror = function () {
                            e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), r.$slider.trigger("lazyLoadError", [r, e, t]);
                        }),
                        (n.src = t);
                });
            }
            var t,
                s,
                o,
                n,
                r = this;
            if (
                (r.options.centerMode === !0
                    ? r.options.infinite === !0
                        ? ((o = r.currentSlide + (r.options.slidesToShow / 2 + 1)), (n = o + r.options.slidesToShow + 2))
                        : ((o = Math.max(0, r.currentSlide - (r.options.slidesToShow / 2 + 1))), (n = 2 + (r.options.slidesToShow / 2 + 1) + r.currentSlide))
                    : ((o = r.options.infinite ? r.options.slidesToShow + r.currentSlide : r.currentSlide), (n = Math.ceil(o + r.options.slidesToShow)), r.options.fade === !0 && (o > 0 && o--, n <= r.slideCount && n++)),
                (t = r.$slider.find(".slick-slide").slice(o, n)),
                "anticipated" === r.options.lazyLoad)
            )
                for (var l = o - 1, a = n, d = r.$slider.find(".slick-slide"), c = 0; c < r.options.slidesToScroll; c++) 0 > l && (l = r.slideCount - 1), (t = t.add(d.eq(l))), (t = t.add(d.eq(a))), l--, a++;
            e(t),
                r.slideCount <= r.options.slidesToShow
                    ? ((s = r.$slider.find(".slick-slide")), e(s))
                    : r.currentSlide >= r.slideCount - r.options.slidesToShow
                    ? ((s = r.$slider.find(".slick-cloned").slice(0, r.options.slidesToShow)), e(s))
                    : 0 === r.currentSlide && ((s = r.$slider.find(".slick-cloned").slice(-1 * r.options.slidesToShow)), e(s));
        }),
        (e.prototype.loadSlider = function () {
            var i = this;
            i.setPosition(), i.$slideTrack.css({ opacity: 1 }), i.$slider.removeClass("slick-loading"), i.initUI(), "progressive" === i.options.lazyLoad && i.progressiveLazyLoad();
        }),
        (e.prototype.next = e.prototype.slickNext = function () {
            var i = this;
            i.changeSlide({ data: { message: "next" } });
        }),
        (e.prototype.orientationChange = function () {
            var i = this;
            i.checkResponsive(), i.setPosition();
        }),
        (e.prototype.pause = e.prototype.slickPause = function () {
            var i = this;
            i.autoPlayClear(), (i.paused = !0);
        }),
        (e.prototype.play = e.prototype.slickPlay = function () {
            var i = this;
            i.autoPlay(), (i.options.autoplay = !0), (i.paused = !1), (i.focussed = !1), (i.interrupted = !1);
        }),
        (e.prototype.postSlide = function (e) {
            var t = this;
            if (
                !t.unslicked &&
                (t.$slider.trigger("afterChange", [t, e]),
                (t.animating = !1),
                t.slideCount > t.options.slidesToShow && t.setPosition(),
                (t.swipeLeft = null),
                t.options.autoplay && t.autoPlay(),
                t.options.accessibility === !0 && (t.initADA(), t.options.focusOnChange))
            ) {
                var s = i(t.$slides.get(t.currentSlide));
                s.attr("tabindex", 0).focus();
            }
        }),
        (e.prototype.prev = e.prototype.slickPrev = function () {
            var i = this;
            i.changeSlide({ data: { message: "previous" } });
        }),
        (e.prototype.preventDefault = function (i) {
            i.preventDefault();
        }),
        (e.prototype.progressiveLazyLoad = function (e) {
            e = e || 1;
            var t,
                s,
                o,
                n,
                r,
                l = this,
                a = i("img[data-lazy]", l.$slider);
            a.length
                ? ((t = a.first()),
                  (s = t.attr("data-lazy")),
                  (o = t.attr("data-srcset")),
                  (n = t.attr("data-sizes") || l.$slider.attr("data-sizes")),
                  (r = document.createElement("img")),
                  (r.onload = function () {
                      o && (t.attr("srcset", o), n && t.attr("sizes", n)),
                          t.attr("src", s).removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading"),
                          l.options.adaptiveHeight === !0 && l.setPosition(),
                          l.$slider.trigger("lazyLoaded", [l, t, s]),
                          l.progressiveLazyLoad();
                  }),
                  (r.onerror = function () {
                      3 > e
                          ? setTimeout(function () {
                                l.progressiveLazyLoad(e + 1);
                            }, 500)
                          : (t.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), l.$slider.trigger("lazyLoadError", [l, t, s]), l.progressiveLazyLoad());
                  }),
                  (r.src = s))
                : l.$slider.trigger("allImagesLoaded", [l]);
        }),
        (e.prototype.refresh = function (e) {
            var t,
                s,
                o = this;
            (s = o.slideCount - o.options.slidesToShow),
                !o.options.infinite && o.currentSlide > s && (o.currentSlide = s),
                o.slideCount <= o.options.slidesToShow && (o.currentSlide = 0),
                (t = o.currentSlide),
                o.destroy(!0),
                i.extend(o, o.initials, { currentSlide: t }),
                o.init(),
                e || o.changeSlide({ data: { message: "index", index: t } }, !1);
        }),
        (e.prototype.registerBreakpoints = function () {
            var e,
                t,
                s,
                o = this,
                n = o.options.responsive || null;
            if ("array" === i.type(n) && n.length) {
                o.respondTo = o.options.respondTo || "window";
                for (e in n)
                    if (((s = o.breakpoints.length - 1), n.hasOwnProperty(e))) {
                        for (t = n[e].breakpoint; s >= 0; ) o.breakpoints[s] && o.breakpoints[s] === t && o.breakpoints.splice(s, 1), s--;
                        o.breakpoints.push(t), (o.breakpointSettings[t] = n[e].settings);
                    }
                o.breakpoints.sort(function (i, e) {
                    return o.options.mobileFirst ? i - e : e - i;
                });
            }
        }),
        (e.prototype.reinit = function () {
            var e = this;
            (e.$slides = e.$slideTrack.children(e.options.slide).addClass("slick-slide")),
                (e.slideCount = e.$slides.length),
                e.currentSlide >= e.slideCount && 0 !== e.currentSlide && (e.currentSlide = e.currentSlide - e.options.slidesToScroll),
                e.slideCount <= e.options.slidesToShow && (e.currentSlide = 0),
                e.registerBreakpoints(),
                e.setProps(),
                e.setupInfinite(),
                e.buildArrows(),
                e.updateArrows(),
                e.initArrowEvents(),
                e.buildDots(),
                e.updateDots(),
                e.initDotEvents(),
                e.cleanUpSlideEvents(),
                e.initSlideEvents(),
                e.checkResponsive(!1, !0),
                e.options.focusOnSelect === !0 && i(e.$slideTrack).children().on("click.slick", e.selectHandler),
                e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0),
                e.setPosition(),
                e.focusHandler(),
                (e.paused = !e.options.autoplay),
                e.autoPlay(),
                e.$slider.trigger("reInit", [e]);
        }),
        (e.prototype.resize = function () {
            var e = this;
            i(window).width() !== e.windowWidth &&
                (clearTimeout(e.windowDelay),
                (e.windowDelay = window.setTimeout(function () {
                    (e.windowWidth = i(window).width()), e.checkResponsive(), e.unslicked || e.setPosition();
                }, 50)));
        }),
        (e.prototype.removeSlide = e.prototype.slickRemove = function (i, e, t) {
            var s = this;
            return (
                "boolean" == typeof i ? ((e = i), (i = e === !0 ? 0 : s.slideCount - 1)) : (i = e === !0 ? --i : i),
                s.slideCount < 1 || 0 > i || i > s.slideCount - 1
                    ? !1
                    : (s.unload(),
                      t === !0 ? s.$slideTrack.children().remove() : s.$slideTrack.children(this.options.slide).eq(i).remove(),
                      (s.$slides = s.$slideTrack.children(this.options.slide)),
                      s.$slideTrack.children(this.options.slide).detach(),
                      s.$slideTrack.append(s.$slides),
                      (s.$slidesCache = s.$slides),
                      void s.reinit())
            );
        }),
        (e.prototype.setCSS = function (i) {
            var e,
                t,
                s = this,
                o = {};
            s.options.rtl === !0 && s.options.vertical === !1 && (i = -i),
                (e = "left" == s.positionProp ? Math.ceil(i) + "px" : "0px"),
                (t = "top" == s.positionProp ? Math.ceil(i) + "px" : "0px"),
                (o[s.positionProp] = i),
                s.transformsEnabled === !1
                    ? s.$slideTrack.css(o)
                    : ((o = {}), s.cssTransitions === !1 ? ((o[s.animType] = "translate(" + e + ", " + t + ")"), s.$slideTrack.css(o)) : ((o[s.animType] = "translate3d(" + e + ", " + t + ", 0px)"), s.$slideTrack.css(o)));
        }),
        (e.prototype.setDimensions = function () {
            var i = this,
                e = 0,
                t = 0,
                s = 0;
            (s = i.getValueMargin()),
                (t = i.options.slidesMargin * i.$slideTrack.children(".slick-slide").length),
                i.options.vertical === !1
                    ? i.options.centerMode === !0 && i.$list.css({ padding: "0px " + i.options.centerPadding })
                    : (i.$list.height(i.$slides.first().outerHeight(!0) * i.options.slidesToShow - s), i.options.centerMode === !0 && i.$list.css({ padding: i.options.centerPadding + " 0px" })),
                (i.listWidth = i.$list.width()),
                (i.listHeight = i.$list.height()),
                i.options.vertical === !1 && i.options.variableWidth === !1
                    ? ((e = Math.abs(s / i.options.slidesToShow)), (i.slideWidth = Math.abs(i.listWidth / i.options.slidesToShow)), i.$slideTrack.width(Math.abs(i.slideWidth * i.$slideTrack.children(".slick-slide").length) + t))
                    : i.options.variableWidth === !0
                    ? i.$slideTrack.width(5e3 * i.slideCount)
                    : ((i.slideWidth = Math.round(i.listWidth)), i.$slideTrack.height(Math.floor(i.$slides.first().outerHeight(!0) * i.$slideTrack.children(".slick-slide").length)));
            var o = i.$slides.first().outerWidth(!0) - i.$slides.first().width();
            i.options.variableWidth === !1 && i.$slideTrack.children(".slick-slide").width(i.slideWidth - o + e);
        }),
        (e.prototype.setFade = function () {
            var e,
                t = this;
            t.$slides.each(function (s, o) {
                (e = t.slideWidth * s * -1),
                    t.options.rtl === !0 ? i(o).css({ position: "relative", right: e, top: 0, zIndex: t.options.zIndex - 2, opacity: 0 }) : i(o).css({ position: "relative", left: e, top: 0, zIndex: t.options.zIndex - 2, opacity: 0 });
            }),
                t.$slides.eq(t.currentSlide).css({ zIndex: t.options.zIndex - 1, opacity: 1 });
        }),
        (e.prototype.setHeight = function () {
            var i = this;
            if (1 === i.options.slidesToShow && i.options.adaptiveHeight === !0 && i.options.vertical === !1) {
                var e = i.$slides.eq(i.currentSlide).outerHeight(!0);
                i.$list.css("height", e);
            }
        }),
        (e.prototype.setOption = e.prototype.slickSetOption = function () {
            var e,
                t,
                s,
                o,
                n,
                r = this,
                l = !1;
            if (
                ("object" === i.type(arguments[0])
                    ? ((s = arguments[0]), (l = arguments[1]), (n = "multiple"))
                    : "string" === i.type(arguments[0]) &&
                      ((s = arguments[0]), (o = arguments[1]), (l = arguments[2]), "responsive" === arguments[0] && "array" === i.type(arguments[1]) ? (n = "responsive") : "undefined" != typeof arguments[1] && (n = "single")),
                "single" === n)
            )
                r.options[s] = o;
            else if ("multiple" === n)
                i.each(s, function (i, e) {
                    r.options[i] = e;
                });
            else if ("responsive" === n)
                for (t in o)
                    if ("array" !== i.type(r.options.responsive)) r.options.responsive = [o[t]];
                    else {
                        for (e = r.options.responsive.length - 1; e >= 0; ) r.options.responsive[e].breakpoint === o[t].breakpoint && r.options.responsive.splice(e, 1), e--;
                        r.options.responsive.push(o[t]);
                    }
            l && (r.unload(), r.reinit());
        }),
        (e.prototype.setPosition = function () {
            var i = this;
            i.setDimensions(), i.setHeight(), i.options.fade === !1 ? i.setCSS(i.getLeft(i.currentSlide)) : i.setFade(), i.$slider.trigger("setPosition", [i]);
        }),
        (e.prototype.setProps = function () {
            var i = this,
                e = document.body.style;
            (i.positionProp = i.options.vertical === !0 ? "top" : "left"),
                "top" === i.positionProp ? i.$slider.addClass("slick-vertical") : i.$slider.removeClass("slick-vertical"),
                (void 0 !== e.WebkitTransition || void 0 !== e.MozTransition || void 0 !== e.msTransition) && i.options.useCSS === !0 && (i.cssTransitions = !0),
                i.options.fade && ("number" == typeof i.options.zIndex ? i.options.zIndex < 3 && (i.options.zIndex = 3) : (i.options.zIndex = i.defaults.zIndex)),
                void 0 !== e.OTransform && ((i.animType = "OTransform"), (i.transformType = "-o-transform"), (i.transitionType = "OTransition"), void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (i.animType = !1)),
                void 0 !== e.MozTransform && ((i.animType = "MozTransform"), (i.transformType = "-moz-transform"), (i.transitionType = "MozTransition"), void 0 === e.perspectiveProperty && void 0 === e.MozPerspective && (i.animType = !1)),
                void 0 !== e.webkitTransform &&
                    ((i.animType = "webkitTransform"), (i.transformType = "-webkit-transform"), (i.transitionType = "webkitTransition"), void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (i.animType = !1)),
                void 0 !== e.msTransform && ((i.animType = "msTransform"), (i.transformType = "-ms-transform"), (i.transitionType = "msTransition"), void 0 === e.msTransform && (i.animType = !1)),
                void 0 !== e.transform && i.animType !== !1 && ((i.animType = "transform"), (i.transformType = "transform"), (i.transitionType = "transition")),
                (i.transformsEnabled = i.options.useTransform && null !== i.animType && i.animType !== !1);
        }),
        (e.prototype.setSlideClasses = function (i) {
            var e,
                t,
                s,
                o,
                n,
                r = this;
            if (
                ((n = "right"),
                r.options.rtl === !0 && (n = "left"),
                r.options.vertical === !0 && (n = "bottom"),
                r.$slider
                    .children()
                    .children(".slick-track")
                    .children()
                    .css("margin-" + n, r.options.slidesMargin + "px"),
                (t = r.$slider.children().children(".slick-track").children().removeClass("slick-active slick-center slick-current").attr("aria-hidden", "true")),
                r.$slides.eq(i).addClass("slick-current"),
                r.options.centerMode === !0)
            ) {
                var l = r.options.slidesToShow % 2 === 0 ? 1 : 0;
                (e = Math.floor(r.options.slidesToShow / 2)),
                    r.options.infinite === !0 &&
                        (i >= e && i <= r.slideCount - 1 - e
                            ? r.$slides
                                  .slice(i - e + l, i + e + 1)
                                  .addClass("slick-active")
                                  .attr("aria-hidden", "false")
                            : ((s = r.options.slidesToShow + i),
                              t
                                  .slice(s - e + 1 + l, s + e + 2)
                                  .addClass("slick-active")
                                  .attr("aria-hidden", "false")),
                        0 === i ? t.eq(t.length - 1 - r.options.slidesToShow).addClass("slick-center") : i === r.slideCount - 1 && t.eq(r.options.slidesToShow).addClass("slick-center")),
                    r.$slides.eq(i).addClass("slick-center");
            } else
                i >= 0 && i <= r.slideCount - r.options.slidesToShow
                    ? r.$slides
                          .slice(i, i + r.options.slidesToShow)
                          .addClass("slick-active")
                          .attr("aria-hidden", "false")
                    : t.length <= r.options.slidesToShow
                    ? t.addClass("slick-active").attr("aria-hidden", "false")
                    : ((o = r.slideCount % r.options.slidesToShow),
                      (s = r.options.infinite === !0 ? r.options.slidesToShow + i : i),
                      r.options.slidesToShow == r.options.slidesToScroll && r.slideCount - i < r.options.slidesToShow
                          ? t
                                .slice(s - (r.options.slidesToShow - o), s + o)
                                .addClass("slick-active")
                                .attr("aria-hidden", "false")
                          : t
                                .slice(s, s + r.options.slidesToShow)
                                .addClass("slick-active")
                                .attr("aria-hidden", "false"));
            ("ondemand" === r.options.lazyLoad || "anticipated" === r.options.lazyLoad) && r.lazyLoad(),
                r.$slider.find(".slick-active:first").addClass("first-slick").siblings().removeClass("first-slick"),
                r.$slider.find(".slick-active:last").addClass("last-slick").siblings().removeClass("last-slick");
        }),
        (e.prototype.setupInfinite = function () {
            var e,
                t,
                s,
                o = this;
            if ((o.options.fade === !0 && (o.options.centerMode = !1), o.options.infinite === !0 && o.options.fade === !1 && ((t = null), o.slideCount > o.options.slidesToShow))) {
                for (s = o.options.centerMode === !0 ? o.options.slidesToShow + 1 : o.options.slidesToShow, e = o.slideCount; e > o.slideCount - s; e -= 1)
                    (t = e - 1),
                        i(o.$slides[t])
                            .clone(!0)
                            .attr("id", "")
                            .attr("data-slick-index", t - o.slideCount)
                            .prependTo(o.$slideTrack)
                            .addClass("slick-cloned");
                for (e = 0; e < s + o.slideCount; e += 1)
                    (t = e),
                        i(o.$slides[t])
                            .clone(!0)
                            .attr("id", "")
                            .attr("data-slick-index", t + o.slideCount)
                            .appendTo(o.$slideTrack)
                            .addClass("slick-cloned");
                o.$slideTrack
                    .find(".slick-cloned")
                    .find("[id]")
                    .each(function () {
                        i(this).attr("id", "");
                    });
            }
        }),
        (e.prototype.interrupt = function (i) {
            var e = this;
            i || e.autoPlay(), (e.interrupted = i);
        }),
        (e.prototype.selectHandler = function (e) {
            var t = this,
                s = i(e.target).is(".slick-slide") ? i(e.target) : i(e.target).parents(".slick-slide"),
                o = parseInt(s.attr("data-slick-index"));
            return o || (o = 0), t.slideCount <= t.options.slidesToShow ? void t.slideHandler(o, !1, !0) : void t.slideHandler(o);
        }),
        (e.prototype.slideHandler = function (i, e, t) {
            var s,
                o,
                n,
                r,
                l,
                a = null,
                d = this;
            return (
                (e = e || !1),
                (d.animating === !0 && d.options.waitForAnimate === !0) || (d.options.fade === !0 && d.currentSlide === i)
                    ? void 0
                    : (e === !1 && d.asNavFor(i),
                      (s = i),
                      (a = d.getLeft(s)),
                      (r = d.getLeft(d.currentSlide)),
                      (d.currentLeft = null === d.swipeLeft ? r : d.swipeLeft),
                      d.options.infinite === !1 && d.options.centerMode === !1 && (0 > i || i > d.getDotCount() * d.options.slidesToScroll)
                          ? void (
                                d.options.fade === !1 &&
                                ((s = d.currentSlide),
                                t !== !0
                                    ? d.animateSlide(r, function () {
                                          d.postSlide(s);
                                      })
                                    : d.postSlide(s))
                            )
                          : d.options.infinite === !1 && d.options.centerMode === !0 && (0 > i || i > d.slideCount - d.options.slidesToScroll)
                          ? void (
                                d.options.fade === !1 &&
                                ((s = d.currentSlide),
                                t !== !0
                                    ? d.animateSlide(r, function () {
                                          d.postSlide(s);
                                      })
                                    : d.postSlide(s))
                            )
                          : (d.options.autoplay && clearInterval(d.autoPlayTimer),
                            (o =
                                0 > s
                                    ? d.slideCount % d.options.slidesToScroll !== 0
                                        ? d.slideCount - (d.slideCount % d.options.slidesToScroll)
                                        : d.slideCount + s
                                    : s >= d.slideCount
                                    ? d.slideCount % d.options.slidesToScroll !== 0
                                        ? 0
                                        : s - d.slideCount
                                    : s),
                            (d.animating = !0),
                            d.$slider.trigger("beforeChange", [d, d.currentSlide, o]),
                            (n = d.currentSlide),
                            (d.currentSlide = o),
                            d.setSlideClasses(d.currentSlide),
                            d.options.asNavFor && ((l = d.getNavTarget()), (l = l.slick("getSlick")), l.slideCount <= l.options.slidesToShow && l.setSlideClasses(d.currentSlide)),
                            d.updateDots(),
                            d.updateArrows(),
                            d.options.fade === !0
                                ? (t !== !0
                                      ? (d.fadeSlideOut(n),
                                        d.fadeSlide(o, function () {
                                            d.postSlide(o);
                                        }))
                                      : d.postSlide(o),
                                  void d.animateHeight())
                                : void (t !== !0
                                      ? d.animateSlide(a, function () {
                                            d.postSlide(o);
                                        })
                                      : d.postSlide(o))))
            );
        }),
        (e.prototype.startLoad = function () {
            var i = this;
            i.options.arrows === !0 && i.slideCount > i.options.slidesToShow && (i.$prevArrow.hide(), i.$nextArrow.hide()),
                i.options.dots === !0 && i.slideCount > i.options.slidesToShow && i.$dots.hide(),
                i.$slider.addClass("slick-loading");
        }),
        (e.prototype.swipeDirection = function () {
            var i,
                e,
                t,
                s,
                o = this;
            return (
                (i = o.touchObject.startX - o.touchObject.curX),
                (e = o.touchObject.startY - o.touchObject.curY),
                (t = Math.atan2(e, i)),
                (s = Math.round((180 * t) / Math.PI)),
                0 > s && (s = 360 - Math.abs(s)),
                45 >= s && s >= 0
                    ? o.options.rtl === !1
                        ? "left"
                        : "right"
                    : 360 >= s && s >= 315
                    ? o.options.rtl === !1
                        ? "left"
                        : "right"
                    : s >= 135 && 225 >= s
                    ? o.options.rtl === !1
                        ? "right"
                        : "left"
                    : o.options.verticalSwiping === !0
                    ? s >= 35 && 135 >= s
                        ? "down"
                        : "up"
                    : "vertical"
            );
        }),
        (e.prototype.swipeEnd = function (i) {
            var e,
                t,
                s = this;
            if (((s.dragging = !1), (s.swiping = !1), s.scrolling)) return (s.scrolling = !1), !1;
            if (((s.interrupted = !1), (s.shouldClick = s.touchObject.swipeLength > 10 ? !1 : !0), void 0 === s.touchObject.curX)) return !1;
            if ((s.touchObject.edgeHit === !0 && s.$slider.trigger("edge", [s, s.swipeDirection()]), s.touchObject.swipeLength >= s.touchObject.minSwipe)) {
                switch ((t = s.swipeDirection())) {
                    case "left":
                    case "down":
                        (e = s.options.swipeToSlide ? s.checkNavigable(s.currentSlide + s.getSlideCount()) : s.currentSlide + s.getSlideCount()), (s.currentDirection = 0);
                        break;
                    case "right":
                    case "up":
                        (e = s.options.swipeToSlide ? s.checkNavigable(s.currentSlide - s.getSlideCount()) : s.currentSlide - s.getSlideCount()), (s.currentDirection = 1);
                }
                "vertical" != t && (s.slideHandler(e), (s.touchObject = {}), s.$slider.trigger("swipe", [s, t]));
            } else s.touchObject.startX !== s.touchObject.curX && (s.slideHandler(s.currentSlide), (s.touchObject = {}));
        }),
        (e.prototype.swipeHandler = function (i) {
            var e = this;
            if (!(e.options.swipe === !1 || ("ontouchend" in document && e.options.swipe === !1) || (e.options.draggable === !1 && -1 !== i.type.indexOf("mouse"))))
                switch (
                    ((e.touchObject.fingerCount = i.originalEvent && void 0 !== i.originalEvent.touches ? i.originalEvent.touches.length : 1),
                    (e.touchObject.minSwipe = e.listWidth / e.options.touchThreshold),
                    e.options.verticalSwiping === !0 && (e.touchObject.minSwipe = e.listHeight / e.options.touchThreshold),
                    i.data.action)
                ) {
                    case "start":
                        e.swipeStart(i);
                        break;
                    case "move":
                        e.swipeMove(i);
                        break;
                    case "end":
                        e.swipeEnd(i);
                }
        }),
        (e.prototype.swipeMove = function (i) {
            var e,
                t,
                s,
                o,
                n,
                r,
                l = this;
            return (
                (n = void 0 !== i.originalEvent ? i.originalEvent.touches : null),
                !l.dragging || l.scrolling || (n && 1 !== n.length)
                    ? !1
                    : ((e = l.getLeft(l.currentSlide)),
                      (l.touchObject.curX = void 0 !== n ? n[0].pageX : i.clientX),
                      (l.touchObject.curY = void 0 !== n ? n[0].pageY : i.clientY),
                      (l.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(l.touchObject.curX - l.touchObject.startX, 2)))),
                      (r = Math.round(Math.sqrt(Math.pow(l.touchObject.curY - l.touchObject.startY, 2)))),
                      !l.options.verticalSwiping && !l.swiping && r > 4
                          ? ((l.scrolling = !0), !1)
                          : (l.options.verticalSwiping === !0 && (l.touchObject.swipeLength = r),
                            (t = l.swipeDirection()),
                            void 0 !== i.originalEvent && l.touchObject.swipeLength > 4 && ((l.swiping = !0), i.preventDefault()),
                            (o = (l.options.rtl === !1 ? 1 : -1) * (l.touchObject.curX > l.touchObject.startX ? 1 : -1)),
                            l.options.verticalSwiping === !0 && (o = l.touchObject.curY > l.touchObject.startY ? 1 : -1),
                            (s = l.touchObject.swipeLength),
                            (l.touchObject.edgeHit = !1),
                            l.options.infinite === !1 &&
                                ((0 === l.currentSlide && "right" === t) || (l.currentSlide >= l.getDotCount() && "left" === t)) &&
                                ((s = l.touchObject.swipeLength * l.options.edgeFriction), (l.touchObject.edgeHit = !0)),
                            l.options.vertical === !1 ? (l.swipeLeft = e + s * o) : (l.swipeLeft = e + s * (l.$list.height() / l.listWidth) * o),
                            l.options.verticalSwiping === !0 && (l.swipeLeft = e + s * o),
                            l.options.fade === !0 || l.options.touchMove === !1 ? !1 : l.animating === !0 ? ((l.swipeLeft = null), !1) : void l.setCSS(l.swipeLeft)))
            );
        }),
        (e.prototype.swipeStart = function (i) {
            var e,
                t = this;
            return (
                (t.interrupted = !0),
                1 !== t.touchObject.fingerCount || t.slideCount <= t.options.slidesToShow
                    ? ((t.touchObject = {}), !1)
                    : (void 0 !== i.originalEvent && void 0 !== i.originalEvent.touches && (e = i.originalEvent.touches[0]),
                      (t.touchObject.startX = t.touchObject.curX = void 0 !== e ? e.pageX : i.clientX),
                      (t.touchObject.startY = t.touchObject.curY = void 0 !== e ? e.pageY : i.clientY),
                      void (t.dragging = !0))
            );
        }),
        (e.prototype.unfilterSlides = e.prototype.slickUnfilter = function () {
            var i = this;
            null !== i.$slidesCache && (i.unload(), i.$slideTrack.children(this.options.slide).detach(), i.$slidesCache.appendTo(i.$slideTrack), i.reinit());
        }),
        (e.prototype.unload = function () {
            var e = this;
            i(".slick-cloned", e.$slider).remove(),
                e.$dots && e.$dots.remove(),
                e.$prevArrow && e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.remove(),
                e.$nextArrow && e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.remove(),
                e.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden", "true").css("width", "");
        }),
        (e.prototype.unslick = function (i) {
            var e = this;
            e.$slider.trigger("unslick", [e, i]), e.destroy();
        }),
        (e.prototype.updateArrows = function () {
            var i,
                e = this;
            (i = Math.floor(e.options.slidesToShow / 2)),
                e.options.arrows === !0 &&
                    e.slideCount > e.options.slidesToShow &&
                    !e.options.infinite &&
                    (e.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"),
                    e.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"),
                    0 === e.currentSlide
                        ? (e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"), e.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"))
                        : e.currentSlide >= e.slideCount - e.options.slidesToShow && e.options.centerMode === !1
                        ? (e.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), e.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"))
                        : e.currentSlide >= e.slideCount - 1 &&
                          e.options.centerMode === !0 &&
                          (e.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), e.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")));
        }),
        (e.prototype.updateDots = function () {
            var i = this;
            null !== i.$dots &&
                (i.$dots.find("li").removeClass("slick-active").end(),
                i.$dots
                    .find("li")
                    .eq(Math.floor(i.currentSlide / i.options.slidesToScroll))
                    .addClass("slick-active"));
        }),
        (e.prototype.visibility = function () {
            var i = this;
            i.options.autoplay && (document[i.hidden] ? (i.interrupted = !0) : (i.interrupted = !1));
        }),
        (i.fn.slick = function () {
            var i,
                t,
                s = this,
                o = arguments[0],
                n = Array.prototype.slice.call(arguments, 1),
                r = s.length;
            for (i = 0; r > i; i++) if (("object" == typeof o || "undefined" == typeof o ? (s[i].slick = new e(s[i], o)) : (t = s[i].slick[o].apply(s[i].slick, n)), "undefined" != typeof t)) return t;
            return s;
        });
});
