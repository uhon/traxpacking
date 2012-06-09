var C, GEN, INIT, FORM, UI, SVG, WHYJUSTIFY;

C = {
    // console wrapper
    debug:true, // global debug on|off
    quietDismiss:true, // may want to just drop, or alert instead
    log:function () {
        C = {
            // console wrapper
            debug: true, // global debug on|off
            quietDismiss: true, // may want to just drop, or alert instead
            log: function() {
                if (!C.debug) { return false; }

                if (typeof (console) === 'object' && typeof console.log !== "undefined") {
                    try {
                        var curdate = new Date();
                        var dateString = curdate.getHours() + curdate.getMinutes() + curdate.getSeconds();

                        arguments.unshift(dateString);
                        console.log.apply(this, arguments); // safari's console.log can't accept scope...
                    } catch (e) {
                        // so we loop instead.
                        var i, l;
                        for (i = 0, l = arguments.length; i < l; i++) {
                            console.log(arguments[i]);
                        }
                    }
                } else {
                    if (!C.quietDismiss) {
                        var result = "", j, le;
                        for (j = 0, le = arguments.length; j < le; j++) {
                            result += arguments[j] + " (" + typeof arguments[j] + ") ";
                        }

                        alert(result);
                    }
                }
                return true;
            }
        }; // end console wrapper.
    }
}; // end console wrapper.

GEN = {   // start of GEN object scope.
    /*
     * Returns an Object or Function-Pointer based on a String-Representation of ObjectHirarchy.
     * If exectute property is set, the function gets executed
     *
     * E.x.:  "GEN.getObjectBYString"
     *         turns to: window["GEN"]["getObjectBYString"]
     */
    getObjectByString: function(objectString, execute) {
        var hirarchyArray = objectString.split("."),
        curMember = window;

        $.each(hirarchyArray, function(key, value) {
            curMember = curMember[value];
        });
        if (typeof (execute) !== "undefined" && execute === true) {
            return curMember();
        } else {
            return curMember;
        }
    }
}; // end of GEN object scope.

INIT = {   // start of INIT object scope.
    onContentReady:function () {
        window.setTimeout(function () {
            UI.showFlashMessages();
        }, 400);

        C.log('Content Replaced rerunning all ContentBindings');
        FORM.bindEditLinks();
        FORM.bindDeleteForms();
        INIT.tinyTips();
    },

    tinyTips: function() {
        //$('svg .poiIcon, svg path.active').each(function() {
        $('svg .poiIcon').each(function() {
            var title = $(this).attr('title');
            if (typeof (title) !== 'undefined' && jQuery.trim($(this).attr('title')) !== '') {
                $(this).tinyTips('light', 'title', true);
            }
        });
    }
}; // end of INIT object scope.

FORM = {  // start of FORM object scope.
    bindEditLinks: function() {
        // Bind Edit-Links
        var logObj = 'none';
        if($('a.ajaxEdit').length > 0) {
            logObj = $('a.ajaxEdit');
        }
        C.log('Bind edit-Links: ', logObj);
        $('a.ajaxEdit').each(function () {
            $(this).click(function () {
                $(".flashMessages").slideUp();
                FORM.popupForm($(this));
                return false;
            });
        });
    },

    bindDeleteForms: function() {
        // Bind Edit-Links
        var logObj = 'none';
        if($('form.deleteForm').length > 0) {
            logObj = $('form.deleteForm');
        }
        C.log('Bind delete-Links: ', logObj);
        $('form.deleteForm').bind('submit', function () {
            $(".flashMessages").slideUp();

            if($('.confirm', $(this)).length > 0) {
                return confirm($('.confirm', $(this)).html());
            }
            return true;
        });
    },

    //set up form function
    initForm:function (formName, preSubmit, postSubmit) {
        var targetformName = '#' + formName;

        // bind form using 'ajaxForm'
        $(targetformName).ajaxForm({
            data:{ formOrigin:window.location.pathname },
            beforeSubmit:preSubmit, // pre-submit callback
            success:postSubmit // post-submit callback
        });
    },

    popupForm:function (linkElement) {
        C.log(linkElement);
        $('#popupForm').load(
            linkElement.attr('href'),
            function () {
                $('#popupForm').dialog({
                    title:"FormDialog",
                    width:700,
                    show:{effect:'explode', duration:750 },
                    hide:{effect:'explode', duration:750 },
                    modal:true,
                    autoOpen:false,
                    closeOnEscape: true
                });
                $('#popupForm').dialog('open');
            }
        );
    }
};  // end of FORM object scope.

UI = { // start of INIT object scope.
    showFlashMessages: function() {
        $(".flashMessages").each(function () {
            if ($('.msgItem', $(this)).length > 0) {
                if($(this).is(':visible')) {
                    $(this).fadeTo('fast', 0.1, function() {  $(this).fadeTo('fast', 1); });
                } else {
                    $(this).slideDown();
                }
            }
        });
    },
    infoMessage: function(message, removeOthers) {
        if(removeOthers) {
            $(".infoMessages .msgItem").slideUp(function() { $(".infoMessages .msgItem").remove(); } );
        }
        //noinspection JSCheckFunctionSignatures
        $(".infoMessages .msgItem").append('<li class="msgItem">' + message + '</li>');
    },
    createButton: function(buttonText, theFunction) {
        var button = $('<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">' + buttonText + '</span></button>');
        button.bind('click', theFunction);
        return button;
    },
    errorMessage: function(message, removeOthers) {
        if(removeOthers) {
            $(".errorMessages .msgItem").slideUp(function() { $(".infoMessages .msgItem").remove(); } );
        }
        //noinspection JSCheckFunctionSignatures
        $(".errorMessages .msgItem").append('<li class="msgItem">' + message + '</li>');
    },
    activateTabs: function() {
        // Do it for Tabbed Subforms
        UI.activateTabsOnPoiForm();
    },
    activateTabsOnPoiForm: function() {
        if($('#poiForm').length > 0) {
            var tabContainer = $('#poiForm .tp_subform_tabbed:first');
            $('.removeLink').each(function() {
                var removeLink = $(this).attr('href');
                $(this).closest('tr').hide();
            });
            $('ul', tabContainer).show();
            tabContainer.tabs({
            });
            // close icon: removing the tab on click
            $(".tabList span.ui-icon-close", tabContainer).click(function() {
                var index = $(".tabList li", tabContainer).index( $( this ).closest('li') ),
                    deleteLink = $('div:nth-of-type(' + (index+1) + ') a.removeLink', tabContainer).attr('href');
                C.log('tabContainer', tabContainer);
                C.log('index', index);
                C.log('nth div', $('div:nth-of-type(' + (index+1) + ')', tabContainer));
                    if(confirm('do yo really want to remove this Picture from POI?')) {
                        tabContainer.waitForIt();
                        $.get(deleteLink, function(data) {
                            C.log($('#popupForm .flashMessages'));
                            C.log($('.flashMessages', data));
                            $('#popupForm .flashMessages').remove();
                            $('#popupForm').prepend(data);
                            UI.showFlashMessages();
                            tabContainer.waitForItStop();
                            // TODO: No success/error szenario catched
                            tabContainer.tabs( "remove", index );
                            var tabCounter = 1;
                            $('.tabList li a', tabContainer).each(function() {
                                $(this).text(tabCounter);
                                tabCounter++;
                            });
                        });
                    }
            });
        }
    },
    markTabsWithErrors: function() {
        $.each($(".tp_subform_tabbed ul.errors").closest(".tp_subform_tabbed"), function() {
            C.log($(this));
            index = $(this).attr('id').substr(4, $(this).attr('id').length-4);
            var correspondingTab = $("ul li:nth-of-type(" + (index) + ")", $(this).closest('.subform_container'));
            C.log('correspondingTab', correspondingTab);
            $('a', correspondingTab).css('color', 'red');
        });


        //.attr('href');

    },
    initalSlideshowState: null,
    startSlideshow: function() {
        C.log('Starting Slideshow');
        UI.initalSlideshowState = $('#supersized-wrapper').html();
        //$("#playground").hide();
        C.log($("iframe", window.top.document));
        // TODO: Comment this out

        $('body', window.document).append('<div id="supersized-loader"></div><div id="supersized"></div>');
        $('#supersized-wrapper').show();
        $('#svgMapContainer').hide();


        //alert("startSlideshow");
        $.supersized({
            // Functionality
            slideshow               :   1,            // Slideshow on/off
            autoplay                :   0,            // Slideshow starts playing automatically
            start_slide             :   1,            // Start slide (0 is random)
            stop_loop               :   1,            // Pauses slideshow on last slide
            random                  :   0,            // Randomize slide order (Ignores start slide)
            slide_interval          :   3000,         // Length between transitions
            transition              :   1,            // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
            transition_speed        :   1300,         // Speed of transition
            new_window              :   1,            // Image links open in new window/tab
            pause_hover             :   0,            // Pause slideshow on hover
            keyboard_nav            :   1,            // Keyboard navigation on/off
            performance             :   3,            // 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
            image_protect           :   1,            // Disables image dragging and right click with Javascript

            // Size & Position
            min_width               :   0,            // Min width allowed (in pixels)
            min_height              :   0,            // Min height allowed (in pixels)
            vertical_center         :   1,            // Vertically center background
            horizontal_center       :   1,            // Horizontally center background
            fit_always              :   0,            // Image will never exceed browser width or height (Ignores min. dimensions)
            fit_portrait            :   1,            // Portrait images will not exceed browser height
            fit_landscape           :   0,            // Landscape images will not exceed browser width

            // Components
            slide_links             :   'blank',      // Individual links for each slide (Options: false, 'number', 'name', 'blank')
            thumb_links             :   1,            // Individual thumb links for each slide
            thumbnail_navigation    :   0,            // Thumbnail navigation
            // Theme Options
            progress_bar            :    0,            // Timer for each slide
            mouse_scrub             :    0,
            // Slideshow Images
            slides                  :      [

                {image : 'http://uhon.ch/sent/img/pictures/DSC00465.jpg', title : 'DSC00465', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00465.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00480.jpg', title : 'DSC00480', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00480.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00502.jpg', title : 'DSC00502', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00502.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00395.jpg', title : 'DSC00395', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00395.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00440.jpg', title : 'DSC00440', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00440.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00466.jpg', title : 'DSC00466', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00466.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00482.jpg', title : 'DSC00482', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00482.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00505.jpg', title : 'DSC00505', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00505.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00396.jpg', title : 'DSC00396', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00396.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00443.jpg', title : 'DSC00443', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00443.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00468.jpg', title : 'DSC00468', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00468.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00484.jpg', title : 'DSC00484', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00484.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00518.jpg', title : 'DSC00518', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00518.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00400.jpg', title : 'DSC00400', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00400.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00445.jpg', title : 'DSC00445', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00445.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00470.jpg', title : 'DSC00470', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00470.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00486.jpg', title : 'DSC00486', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00486.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00524.jpg', title : 'DSC00524', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00524.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00402.jpg', title : 'DSC00402', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00402.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00447.jpg', title : 'DSC00447', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00447.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00473.jpg', title : 'DSC00473', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00473.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00490.jpg', title : 'DSC00490', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00490.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00528.jpg', title : 'DSC00528', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00528.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00405.jpg', title : 'DSC00405', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00405.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00448.jpg', title : 'DSC00448', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00448.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00475.jpg', title : 'DSC00475', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00475.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00491.jpg', title : 'DSC00491', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00491.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00529.jpg', title : 'DSC00529', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00529.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00420.jpg', title : 'DSC00420', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00420.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00451.jpg', title : 'DSC00451', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00451.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00477.jpg', title : 'DSC00477', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00477.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00492.jpg', title : 'DSC00492', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00492.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00530.jpg', title : 'DSC00530', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00530.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00421.jpg', title : 'DSC00421', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00421.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00455.jpg', title : 'DSC00455', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00455.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00478.jpg', title : 'DSC00478', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00478.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00496.jpg', title : 'DSC00496', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00496.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00532.jpg', title : 'DSC00532', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00532.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00426.jpg', title : 'DSC00426', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00426.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00460.jpg', title : 'DSC00460', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00460.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00479.jpg', title : 'DSC00479', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00479.jpg', url : ''},
                {image : 'http://uhon.ch/sent/img/pictures/DSC00497.jpg', title : 'DSC00497', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00497.jpg', url : ''}
            ]
        });
        $('#fullscreen_toggle').empty().append(UI.createButton('Fullscreen', WHYJUSTIFY.toggleFullscreen));
        $('#close-supersized').empty().append(UI.createButton('X', UI.stopSlideshow));
    },
    stopSlideshow: function() {
        $('#supersized-wrapper').hide();
        $('#svgMapContainer').show();
        $("#supersized-loader").remove();
        $('#supersized-wrapper').empty();
        $('#supersized-wrapper').html(UI.initalSlideshowState);
        $('#supersized').remove();
    }
}; // end of UI object scope.


SVG = { // start of SVG object scope.
    worldMaps: { },

    createSvgWorldMap: function(functionCall, containerId) {

        if(typeof(containerId) === "undefined") {
            containerId = "svgMapContainer";
        }
        container = $('#' + containerId);

        SVG.worldMaps[containerId] = container.svg();
        SVG.worldMaps[containerId] = container.svg('get');
        $('#svgMapContainer').waitForIt();
        SVG.worldMaps[containerId].load("/img/world_map.svg", {addTo: true, changeSize: false, onLoad: functionCall });
    },

    setupSvgWorldMap: function(countryArray, containerId) {

        // SVG.worldMap.root().css('height', $(window).height());
        //C.log('------- -0 0 ' + $(window).width() + ' ' + $(window).height());
        //SVG.worldMap.configure({viewBox: '-400 -400 ' + $(window).width() + ' ' + $(window).height()}, true);

        if(typeof(containerId) === "undefined" || typeof(containerId.length) === "undefined") {
            containerId = "svgMapContainer";
        }
        var container = $('#' + containerId),
            svgElement = SVG.worldMaps[containerId];

        C.log('setupWorldMap with following array', countryArray, 'on Container', container);

        C.log('SVG Element at Setup Start', svgElement.root());


        var minX = 99999,
            minY = 99999,
            maxX = -99999,
            maxY = -99999,
            maxWidth = 0,
            maxHeight = 0,
            borderSize = 3,
            dimensions = null,
            growHeight,
            growWidth,
            countryList = countryArray;


        //countryList = VODOO
        $.each(countryList, function(key, country) {
            C.log(country.name);

            var countryElement = $('path[title="' + country.name + '"]', svgElement.root());

            countryElement.attr("fill", '#ccc');
            countryElement.attr("class", 'active');


            dimensions = countryElement.get(0).getBBox();
            if(dimensions.x - borderSize < minX) {
                minX = dimensions.x - borderSize;
            }
            if(dimensions.y - borderSize < minY) {
                minY = dimensions.y - borderSize;
            }
            if(dimensions.x + dimensions.width + borderSize > maxX) {
                maxX = dimensions.x + dimensions.width + borderSize;
            }
            if(dimensions.y + dimensions.height + borderSize > maxY) {
                maxY = dimensions.y + dimensions.height + borderSize;
            }
            maxWidth = (maxX - minX);
            maxHeight = Math.abs(maxY - minY);
        });

        if(maxWidth === 0 || maxHeight === 0) {
            svgElement.configure({viewBox: "50 -800 740 240"}, true);
            svgElement.configure({scale: 3});
        } else {

            if((maxWidth / maxHeight > 900/400)) {
                growHeight = maxWidth / 900 * 400 - maxHeight;
                maxHeight += growHeight;
                minY -= growHeight / 2;
            } else {
                growWidth = maxHeight / 400 * 900 - maxWidth;
                maxWidth += growWidth;
                minX -= (growWidth / 2);
            }

            C.log("Dimensions for SVG Element: minX:" + minX + ", minY:" + minY + ", width:" + maxWidth + ", height:" + maxHeight);
            svgElement.configure({viewBox: minX + " " + minY + " " + (maxWidth +100) + " " + maxHeight}, true);

            svgElement.configure({scale: 3});

            /*$('path.active', svgElement.root()).bind('mouseover', function(e) {
                $(this).attr('fill', 'green');
            });

            $('path.active', svgElement.root()).bind('mouseout', function(e) {
                $(this).attr('fill', '#ccc');
            });*/
        }
        C.log('SVG Element at Setup End', svgElement.root());
        INIT.tinyTips();
        $('#fullscreen_toggle').append(UI.createButton('Fullscreen', WHYJUSTIFY.toggleFullscreen));
        $('#svgMapContainer').waitForItStop();
    },

    drawPois: function(pois, containerId) {

        if(typeof(containerId) === "undefined" || typeof(containerId.length) === "undefined") {
            containerId = "svgMapContainer";
        }
        var lines = { },
            prevCoords = null,
            counter = 0,
            svgElement = SVG.worldMaps[containerId];

        C.log('draw Pois on ',  svgElement);
        C.log( $(svgElement.root()));
        $.each(pois, function(poiId, poiArray) {
            var coords = poiArray['svgCoords'].split(','),
                imageWidth = 50 / $(svgElement.root()).attr('scale') / 3,
                imageHeight = 60 / $(svgElement.root()).attr('scale') / 3,
                poiElement = $(
                    svgElement.image(
                        svgElement.root(),
                        coords[0] - imageWidth / 2,
                        coords[1] - imageHeight,
                        imageWidth,
                        imageHeight,
                        '/img/whyjustify_pin_black.png',
                        { class : "poiIcon", title : poiArray['title'] }
                    )
                );

            poiElement.bind('mouseover', function(e) {
                $(this).attr("href", '/img/whyjustify_pin_red.png');
            });

            poiElement.bind('mouseout', function(e) {
                $(this).attr("href", '/img/whyjustify_pin_black.png');
            });

            poiElement.bind('click', function(e) {
                $(window.top).attr("location", poiArray['url']);
                var dialogContent,
                    dialog,
                    button,
                    pictures;

                dialogContent = $('<div><div style="text-align:center; margin-top: 20px;"></div></div>');

                /*dialogContent.dialog({
                    modal: true,
                    title: $(this).attr('title'),
                    autoOpen: true,
                    closeOnEscape: true,
                    show: "explode",
                    hide: "explode",
                    resizable: false,
                    stack: false
                });
                C.log('country clicked' + $(this));
                $(this).show();
                $(this).attr("fill", '#ccc');*/

            });


            if(prevCoords !== null) {
                lines[counter] = [ prevCoords[0], prevCoords[1], coords[0], coords[1] ]
            }
            prevCoords = coords;
            counter++;
        });

        $.each(lines, function(key, value) {
            svgElement.line(svgElement.root(), value[0], value[1], value[2], value[3], { stroke: "#aaaaaa", strokeWidth : "0.3"});
        });


    }
}; // end of SVG object scope.

WHYJUSTIFY = { // start of WHYJUSTIFY-specific object scope.
    fullscreenRestore: null,
    toggleFullscreen: function() {
        var iframe = $("iframe", window.top.document);
        if(WHYJUSTIFY.fullscreenRestore === null) {
            WHYJUSTIFY.fullscreenRestore = { width: iframe.css('width'), height: iframe.css('height') };
            C.log("restore-css-string: ", WHYJUSTIFY.fullscreenRestore);
            iframe.css({ border: 0, position:"fixed", top:0, left:0, right:0, bottom:0, width:"100%", height:"100%" });
            $("#comments, #header, #footer, .entry-actions, #primary", window.top.document).hide();
            $("#main", window.top.document).css('padding-top: 0px');
        } else {
            $(".entry-actions", window.top.document).css('padding-top: 40px');
            $("#comments, #header, #footer, .entry-actions, #primary", window.top.document).show();
            iframe.css(WHYJUSTIFY.fullscreenRestore);
            iframe.css({position:"static", top:"auto", left:"auto"});
            WHYJUSTIFY.fullscreenRestore = null;
        }
    }
}; // end of WHYJUSTIFY-specific object scope.

$(function () {
    INIT.onContentReady();

    rpc = jQuery.Zend.jsonrpc({ url : "/ajax/rpc.php", async : true});


    /*C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.hello());
    C.log(rpc.hello());
    C.log(rpc.hello());
    C.log(rpc.hello());*/

    /*var testPremadeSmd = jQuery.Zend.jsonrpc({
        url: '/ajax/rpc.php',
        smd: {"transport":"POST","envelope":"JSON-RPC-2.0","contentType":"application\/json","SMDVersion":"2.0","target":"rpc.php","services":{"add":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"subtract":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"multiply":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"divide":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"float"},"hang":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"sleepTime","optional":false}],"returns":"boolean"}},"methods":{"add":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"subtract":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"multiply":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"divide":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"float"},"hang":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"sleepTime","optional":false}],"returns":"boolean"}}}
    });

    C.log(testPremadeSmd.random());
    C.log(testPremadeSmd.random());
    C.log(testPremadeSmd.random());
    C.log(testPremadeSmd.random());
    C.log(testPremadeSmd.hello());
    C.log(testPremadeSmd.hello());
    C.log(testPremadeSmd.hello());
    C.log(testPremadeSmd.hello());*/




});

