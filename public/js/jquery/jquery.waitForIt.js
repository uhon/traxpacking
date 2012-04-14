(function ($) {
    $.fn.waitForIt = function () {
        var waitIcon,
            overlay,
            scaleFactor,
            targetElement;

        targetElement = $(window);
        if ($(this).length !== 0) {
            targetElement = $(this);
        }


        overlay = $('<div class="waitIconOverlay"></div>');
        overlay.maxZIndex();
        targetElement.prepend(overlay);
        waitIcon = $('<div class="waitIcon"><img src="/img/waitForIt.gif" alt="wait for ajax"></div>');
        waitIcon.maxZIndex();
        targetElement.prepend(waitIcon);



        overlay.width(targetElement.width());
        overlay.height(targetElement.height());

        scaleFactor = 0.25;
        waitIcon.width(Math.round(targetElement.width() * scaleFactor));
        waitIcon.css("left", Math.round(targetElement.width() / 2 - waitIcon.width() / 2));
        waitIcon.css("top", Math.round(targetElement.height() / 2 - waitIcon.height() / 2));

        overlay.css("opacity", "0.7").show();
        waitIcon.css("opacity", "1").show();

        //waitIcon.siblings().fadeTo("fast", 0.1);
        //C.log('siblings during init', waitIcon.siblings());

        return waitIcon;

    };

    $.fn.waitForItStop = function () {
        $(".waitIcon", $(this)).remove();
        $(".waitIconOverlay", $(this)).remove();
    };

})(jQuery);