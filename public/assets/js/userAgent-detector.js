var userAgentDetector = function () {
    if(window.matchMedia("(max-width: 767px)").matches){
        // The viewport is less than 768 pixels wide (Mobile)
        $('.banner-media-layout-01').addClass('banner-media-layout-01__mobile')
        $('.banner-media-layout-01').removeClass('banner-media-layout-01__web')
    } else{
        // The viewport is at least 768 pixels wide (Web)
        $('.banner-media-layout-01').removeClass('banner-media-layout-01__mobile')
        $('.banner-media-layout-01').addClass('banner-media-layout-01__web')
    }
}

$(window).on('load', function () {
    userAgentDetector();
}).on('resize', function() {
    userAgentDetector();
});

