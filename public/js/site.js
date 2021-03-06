var C, GEN, INIT, FORM, UI, PHOTO, SVG, WHYJUSTIFY;

C = {
    // console wrapper
    debug: true, // global debug on|off
    quietDismiss: true, // may want to just drop, or alert instead
    log: function () {
        C = {
            // console wrapper
            debug: true, // global debug on|off
            quietDismiss: true, // may want to just drop, or alert instead
            log: function () {
                if (!C.debug) { return false; }

                if (typeof (console) === 'object' && console.log !== undefined) {
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
    getObjectByString: function (objectString, execute) {
        var hirarchyArray = objectString.split("."),
        curMember = window;

        $.each(hirarchyArray, function (key, value) {
            curMember = curMember[value];
        });
        if (execute !== undefined && execute === true) {
            return curMember();
        } else {
            return curMember;
        }
    }
}; // end of GEN object scope.

INIT = {   // start of INIT object scope.
    onContentReady: function () {
        window.setTimeout(function () {
            UI.showFlashMessages();
        }, 400);

        C.log('Content Replaced rerunning all ContentBindings');
        FORM.bindEditLinks();
        FORM.bindDeleteForms();
        INIT.tinyTips();
    },

    tinyTips: function () {
        //$('svg .poiIcon, svg path.active').each(function () {
        $('svg image').each(function () {
            var title = $(this).attr('title');
            if (title !== undefined && jQuery.trim($(this).attr('title')) !== '') {
                $(this).tinyTips('light', 'title', true);
            }
        });
    }
}; // end of INIT object scope.

FORM = {  // start of FORM object scope.
    bindEditLinks: function () {
        // Bind Edit-Links
        var logObj = 'none';
        if ($('a.ajaxEdit').length > 0) {
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

    bindDeleteForms: function () {
        // Bind Delete-Links
        var logObj = 'none';
        if ($('form.deleteForm').length > 0) {
            logObj = $('form.deleteForm');
        }
        C.log('Bind delete-Links: ', logObj);
        $('form.deleteForm').bind('submit', function () {
            $(".flashMessages").slideUp();

            if ($('.confirm', $(this)).length > 0) {
                return confirm($('.confirm', $(this)).html());
            }
            return true;
        });
    },
    
    // click all initialisation links in a table
    initAjaxified: function (curIndex) {
        var element = $($("table:first a").get(curIndex));
        if (element.length !== 0) {
            $.get(element.attr("href"), function () {
                FORM.initAjaxified(curIndex+1);
            });
        }
        return 0;
    },
    
    //set up form function
    initForm: function (formName, preSubmit, postSubmit) {
        var targetformName = '#' + formName;

        // bind form using 'ajaxForm'
        $(targetformName).ajaxForm({
            data: { formOrigin: window.location.pathname },
            beforeSubmit: preSubmit, // pre-submit callback
            success: postSubmit // post-submit callback
        });
    },

    popupForm: function (linkElement) {
        $('#popupForm').load(
            linkElement.attr('href'),
            function () {
                $('#popupForm').dialog({
                    title: "FormDialog",
                    width: 700,
                    show: {effect: 'explode', duration: 750 },
                    hide: {effect: 'explode', duration: 750 },
                    modal: true,
                    autoOpen: false,
                    closeOnEscape: true
                });
                $('#popupForm').dialog('open');
            }
        );
    },

    togglePoiOrigin: function () {
        if ($('#category').val() === "5") {
            $("#svgPrevCoordinates").closest('tr').show();
            $("#toggleOrigin").attr('checked', 'checked');
        } else {
            if ($("#toggleOrigin").attr("checked") === "checked") {
                $("#svgPrevCoordinates").closest('tr').show();
                $("#toggleOrigin").attr('checked', 'checked');
            } else {
                $("#svgPrevCoordinates").closest('tr').hide();
                $("#svgPrevCoordinates").val('');
                $("#toggleOrigin").removeAttr('checked');
            }
        }
    },

    initPoiOriginToggle: function () {
        if (
            $("#svgPrevCoordinates").val() === ""
            && $('#category').val() !== "5"
            && $("#toggleOrigin").attr('checked') !== "checked"
        ) {
            $("#toggleOrigin").removeAttr('checked');
            $("#svgPrevCoordinates").closest('tr').hide();
        } else if ($("#svgPrevCoordinates").val() !== "") {
            $("#toggleOrigin").attr('checked', "checked");
        }
    }
};  // end of FORM object scope.

UI = { // start of INIT object scope.
    showFlashMessages: function () {
        $(".flashMessages").each(function () {
            if ($('.msgItem', $(this)).length > 0) {
                if ($(this).is(':visible')) {
                    $(this).fadeTo('fast', 0.1, function () {  $(this).fadeTo('fast', 1); });
                } else {
                    $(this).slideDown();
                }
            }
        });
    },
    infoMessage: function (message, removeOthers) {
        if (removeOthers) {
            $(".infoMessages .msgItem").slideUp(function () { $(".infoMessages .msgItem").remove(); });
        }
        //noinspection JSCheckFunctionSignatures
        $(".infoMessages .msgItem").append('<li class="msgItem">' + message + '</li>');
    },
    createButton: function (buttonText, theFunction) {
        var button = $('<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">' + buttonText + '</span></button>');
        button.bind('click', theFunction);
        return button;
    },
    errorMessage: function (message, removeOthers) {
        if (removeOthers) {
            $(".errorMessages .msgItem").slideUp(function () { $(".infoMessages .msgItem").remove(); });
        }
        //noinspection JSCheckFunctionSignatures
        $(".errorMessages .msgItem").append('<li class="msgItem">' + message + '</li>');
    },
    activateTabs: function () {
        // Do it for Tabbed Subforms
        UI.activateTabsOnPoiForm();
    },
    activateTabsOnPoiForm: function () {
        if ($('#poiForm').length > 0) {
            var tabContainer = $('#poiForm .tp_subform_tabbed:first');
            $('.removeLink').each(function () {
                var removeLink = $(this).attr('href');
                $(this).closest('tr').hide();
            });
            $('ul', tabContainer).show();
            tabContainer.tabs({
            });
            // close icon: removing the tab on click
            $(".tabList span.ui-icon-close", tabContainer).click(function () {
                var index = $(".tabList li", tabContainer).index($(this).closest('li')),
                    deleteLink = $('div:nth-of-type(' + (index+1) + ') a.removeLink', tabContainer).attr('href');
                    if (confirm('do you really want to remove this Picture from POI?')) {
                        tabContainer.waitForIt();
                        $.get(deleteLink, function (data) {
                            $('#popupForm .flashMessages').remove();
                            $('#popupForm').prepend(data);
                            UI.showFlashMessages();
                            tabContainer.waitForItStop();
                            // TODO: No success/error szenario catched
                            tabContainer.tabs("remove", index);
                            // all other indexes after the deleted have to be adjusted accordingly
                            var indexCounter = index + 2;
                            C.log('adjust indexes of following subform-elements');
                            while(true) {
                                var changelings = $('[name^="pictures[' + indexCounter + ']"]', $('.tp_subform_tabbed'));

                                if(changelings.length > 0) {
                                    changelings.each(function() {
                                        var newIndex = indexCounter - 1;
                                        $(this).attr('name', "pictures[" + newIndex + $(this).attr('name').substr($(this).attr('name').indexOf(']')));
                                    });
                                } else {
                                    break;
                                }
                                indexCounter++;
                            }



                            //while((var element))
                            var tabCounter = 1;
                            $('.tabList li a', tabContainer).each(function () {
                                $(this).text(tabCounter);
                                tabCounter++;
                            });
                        });
                    }
            });
        }
    },
    markTabsWithErrors: function () {
        $.each($(".tp_subform_tabbed ul.errors").closest(".tp_subform_tabbed"), function () {
            var index = $(this).attr('id').substr(4, $(this).attr('id').length-4);
            var correspondingTab = $("ul li:nth-of-type(" + (index) + ")", $(this).closest('.subform_container'));
            $('a', correspondingTab).css('color', 'red');
        });


        //.attr('href');

    },
    initalSlideshowState: null,
    startSlideshow: function () {
        C.log('Starting Slideshow');
        UI.initalSlideshowState = $('#supersized-wrapper').html();
        //$("#playground").hide();

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

                {image : 'http://uhon.ch/sent/img/pictures/DSC00465.jpg', title : 'DSC00465', thumb : 'http://uhon.ch/sent/img/thumbs/DSC00465.jpg', url : ''}
            ]
        });
        $('#fullscreen_toggle').empty().append(UI.createButton('Fullscreen', WHYJUSTIFY.toggleFullscreen));
        $('#close-supersized').empty().append(UI.createButton('X', UI.stopSlideshow));
    },
    stopSlideshow: function () {
        $('#supersized-wrapper').hide();
        $('#svgMapContainer').show();
        $("#supersized-loader").remove();
        $('#supersized-wrapper').empty();
        $('#supersized-wrapper').html(UI.initalSlideshowState);
        $('#supersized').remove();
    }
}; // end of UI object scope.

PHOTO = { // start of PHOTO object scope
    preparePhotoMap: function () {
        // initial pictures displayed (either last or forced by POI)
        var forcedPoi = new RegExp('[\\?&]' + 'poi' + '=([^&#]*)').exec($(window.parent).attr("location"));
        $('#svgMapContainer').after('<div style="clear:both"></div><div id="photoStream"></div>');
        if (forcedPoi !== null) {
            SVG.forcedCurrentPoi = parseInt(forcedPoi.pop(), 10);
            PHOTO.showPhotoByPoi(SVG.forcedCurrentPoi);
        } else {
            PHOTO.showPhotoByPoi();
        }


    },

    showPhotoByPoi: function (poiId) {
        if (poiId === undefined) {
            poiId = "";
        }
        $('#photoStream').empty();
        $('#photoStream').waitForItLoadPictures('/default/index/photo/poi/' + poiId, function () {
            $("#tpScrollHorizontal").thumbnailScroller({
                scrollerType: "clickButtons",
                scrollerOrientation: "horizontal",
                scrollEasing: "easeOutCirc",
                scrollEasingAmount: 800,
                acceleration: 4,
                scrollSpeed: 800,
                noScrollCenterSpace: 10
            });

            $("#tpScrollHorizontal a.lightview").bind("click", function () {
                WHYJUSTIFY.toggleFullscreen();
                $(window.top).scrollTo({top: 1000});
            });
        });
    }
}; // end of PHOTO object scope

SVG = { // start of SVG object scope.
    worldMaps: { },
    worldMapsScaleFactor: { },
    redrawPoiTimeout: null,
    forcedCurrentPoi: null,
    createSvgWorldMap: function (functionCall, containerId) {

        if (containerId === undefined) {
            containerId = "svgMapContainer";
        }
        var container = $('#' + containerId);

        SVG.worldMaps[containerId] = container.svg();
        SVG.worldMaps[containerId] = container.svg('get');
        $('#svgMapContainer').waitForIt();
        SVG.worldMaps[containerId].load("/img/world_map.svg", {addTo: true, changeSize: true, onLoad: functionCall });
    },

    setupSvgWorldMap: function (countryArray, containerId) {

        if (containerId === undefined || containerId.length === undefined) {
            containerId = "svgMapContainer";
        }
        var container = $('#' + containerId),
            svgElement = SVG.worldMaps[containerId],
            containerHeight = $(svgElement.root()).parent().height(),
            containerWidth = $(svgElement.root()).parent().width();

        C.log('setupWorldMap with following array', countryArray, 'on Container', container);

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
        C.log("process countries");
        $.each(countryList, function (key, country) {
            C.log(country.name);

            var countryElement = $('path[title="' + country.name + '"]', svgElement.root());

            countryElement.attr("fill", '#ccc');
            countryElement.attr("class", 'active');

            // Countries change color while hovering
            /*
            countryElement.bind('mouseover', function (e) {
                $(this).attr("fill", 'green');
            });

            countryElement.bind('mouseout',  function (e) {
                countryElement.attr("fill", '#ccc');
            });
            */


            dimensions = countryElement.get(0).getBBox();
            if (dimensions.x - borderSize < minX) {
                minX = dimensions.x - borderSize;
            }
            if (dimensions.y - borderSize < minY) {
                minY = dimensions.y - borderSize;
            }
            if (dimensions.x + dimensions.width + borderSize > maxX) {
                maxX = dimensions.x + dimensions.width + borderSize;
            }
            if (dimensions.y + dimensions.height + borderSize > maxY) {
                maxY = dimensions.y + dimensions.height + borderSize;
            }
            maxWidth = (maxX - minX);
            maxHeight = Math.abs(maxY - minY);
        });

        if (maxWidth === 0 || maxHeight === 0) {
            svgElement.configure({viewBox: "50 -800 740 240"}, true);
            SVG.worldMapsScaleFactor[containerId] = 1405 / 600;
        } else {

            if ((maxWidth / maxHeight > containerWidth/containerHeight)) {
                growHeight = maxWidth / containerWidth * containerHeight - maxHeight;
                maxHeight += growHeight;
                minY -= growHeight / 2;
            } else {
                growWidth = maxHeight / containerHeight * containerWidth - maxWidth;
                maxWidth += growWidth;
                minX -= (growWidth / 2);
            }

            //SVG.worldMapsScaleFactor[containerId] = (1405 / $(svgElement.root()).width()) * (1405 * (maxWidth / 1405)) / 2500;

            //C.log("Dimensions for SVG Element: minX:" + minX + ", minY:" + minY + ", width:" + maxWidth + ", height:" + maxHeight);
            //svgElement.configure({viewBox: minX + " " + minY + " " + (maxWidth) + " " + maxHeight}, true);
            var factor = 1405 / maxWidth * containerWidth / 1405;
            SVG.worldMapsScaleFactor[containerId] = factor;
            $('#viewport', $(svgElement.root())).attr('transform', 'matrix(' + factor + ',0,0,' + factor + ','+ minX * factor * -1 + ',' + factor * -1 * (minY) + ')');
            C.log("Dimensions for SVG Element: scale-Factor:" + factor + ", minX:" + minX + ", minY:" + minY + " minX*factor:" + minX * factor * -1 + ", minY*factor:" + minY * factor * -1);

            //svgElement.configure({scale: 3});

            /*$('path.active', svgElement.root()).bind('mouseover', function (e) {
                $(this).attr('fill', 'green');
            });

            $('path.active', svgElement.root()).bind('mouseout', function (e) {
                $(this).attr('fill', '#ccc');
            });*/
        }

        $('#fullscreen_toggle').append(UI.createButton('Fullscreen', WHYJUSTIFY.toggleFullscreen));
        $('#svgMapContainer').waitForItStop();
    },

    /*
        @var pois Array of Pois
        @var type 'default' or 'photo' // different behaviour while clicking on pois
        @var containerId to draw Pois on (if multiple SVGs are in use)
     */
    drawPois: function (pois, type, containerId) {
        if (containerId === undefined) {
            containerId = "svgMapContainer";
        }
        var lines = { },
            prevCoords = null,
            counter = 0,
            svgElement = SVG.worldMaps[containerId],
            currentFactor = $('#viewport', $(svgElement.root())).attr('transform').substring(7).split(",").shift();

        C.log('draw Pois on ', svgElement, pois, type, containerId);


        $.each(pois, function (i, poiArray) {
            // reset current Highlighted POI
            if (SVG.forcedCurrentPoi !== null && poiArray.current !== undefined && poiArray.current === true) {
                poiArray.current = false;
            }

            var coords = poiArray.svgCoords.split(','),
                imageWidth = 50 / (currentFactor * 2),
                imageHeight = 60 / (currentFactor * 2),
                pinPosition = new Array(2),
                poiElement,
                pinType,
                imageIcon,
                isActive = false,
                isCurrent = false,
                activeClass = ""
            ;

            if (poiArray.svgPrevCoords !== "" && poiArray.svgPrevCoords !== null) {
                prevCoords = poiArray.svgPrevCoords.split(',');
            }

            if (prevCoords !== null) {
                lines[counter] = [ prevCoords[0], prevCoords[1], coords[0], coords[1], poiArray.lineColor ];
            }



            if ((poiArray.url !== null && poiArray.url.indexOf("/photo") !== -1) || type === "photo") {
                pinType = "camera";
            } else {
                pinType = "pin";
            }

            //imageIcon = "/img/" + pinType + "_grey.png";
            imageIcon = null;

            if (
                (poiArray.current !== undefined && poiArray.current === true)
                || SVG.forcedCurrentPoi === poiArray.id
            ) {
                isCurrent = true;
                imageIcon = "/img/" + pinType + "_red.png";
            } else if (poiArray.url !== null && poiArray.url.length > 0) {
                isActive = true;
                activeClass = " activePoi";
                //imageIcon = "/img/whyjustify_pin_black.png";
                imageIcon = "/img/" + pinType + "_black.png";
            }

            // no clickable poi or the poi is in the middle of the line; so we draw some nice dots
            if (imageIcon === null || poiArray.category === "bicycle") {
                var attribs = { "class" : "landmark" };
                if (poiArray.category !== "bicycle") {
                    attribs.title = poiArray.title;
                }

                svgElement.image(
                    $('#viewport', $(svgElement.root())),
                    coords[0] - 10 / (currentFactor * 2),
                    coords[1] - 10 / (currentFactor * 2),
                    20 / (currentFactor * 2),
                    20 / (currentFactor * 2),
                    "/img/grey_dot.png",
                    attribs
                );
            }

            // there is a clickable poi to draw
            if (imageIcon !== null) {
                pinPosition = new Array(2);
                pinPosition[0] = coords[0];
                pinPosition[1] = coords[1];
                if (poiArray.category === "bicycle") {
                    pinPosition[0] = parseFloat(coords[0]) - (parseFloat(coords[0]) - parseFloat(prevCoords[0])) / 2;
                    pinPosition[1] = parseFloat(coords[1]) - (parseFloat(coords[1]) - parseFloat(prevCoords[1])) / 2;
                }

                poiElement = $(
                    svgElement.image(
                        $('#viewport', $(svgElement.root())),
                        pinPosition[0] - imageWidth / 2,
                        pinPosition[1] - imageHeight,
                        imageWidth,
                        imageHeight,
                        imageIcon,
                        { "class" : "poiIcon" + activeClass, title : poiArray.title }
                    )
                );

                if (isActive && !isCurrent) {
                    poiElement.bind('mouseover', function (e) {
                        $(this).attr("href", '/img/' + pinType + '_red.png');
                    });

                    poiElement.bind('mouseout',  function (e) {
                        $(this).attr("href", '/img/' + pinType + '_black.png');
                    });


                    poiElement.bind('click', function (e) {
                        if (type === "default") {
                            if (poiArray.url !== null) {
                                $(window.top).attr("location", poiArray.url);
                            }
                        } else if (type === "photo") {
                            PHOTO.showPhotoByPoi(poiArray.id);
                            //$(window.top).scrollTop($("#svg_map_frame", $(window.top)).offset().top + 400);
                            $(window.top).scrollTop(500);
                            //$(window.top).scrollTop(1000);
                            $('.poiIcon, .poiLine', $('svg')).remove();
                            C.log('removed old pois and lines, redraw!');
                            SVG.forcedCurrentPoi = poiArray.id;
                            SVG.drawPois(pois, type, containerId);
                        }
                    });



                }
            }

            prevCoords = coords;
            counter++;
        });

        $.each(lines, function (key, value) {
            svgElement.line($('#viewport'), value[0], value[1], value[2], value[3], { "class": "poiLine", stroke: "#" + value[4], strokeWidth : 1.5 / currentFactor});
        });

        INIT.tinyTips();
        $('svg').svgPan('viewport', true, true, false, 0.05, function () {
            clearTimeout(SVG.redrawPoiTimeout);
            SVG.redrawPoiTimeout = setTimeout(function () {
                $('.poiIcon, .poiLine, .landmark', $('svg')).remove();
                C.log('removed old pois and lines, redraw!');
                SVG.drawPois(pois, type, containerId);
            }, 300);

        });
    }
}; // end of SVG object scope.

WHYJUSTIFY = { // start of WHYJUSTIFY-specific object scope.
    fullscreenRestore: null,
    toggleFullscreen: function () {
        C.log("toggle Fullscreen");
        var iframe = $("iframe[name='" + window.name + "']", window.top.document),
            logo = $('#whyjustify_logo', window.top.document);
        if (WHYJUSTIFY.fullscreenRestore === null) {
            WHYJUSTIFY.fullscreenRestore = {
                width: iframe.css('width'),
                height: iframe.css('height'),
                iframeMargin : iframe.css('margin-top'),
                playgroundPadding : $('#playground').css('padding-top'),
                logoOffset : { top: logo.css('top'), right: logo.css('right') },
                scrollPosition: $(window.parent).scrollTop()
                //mapHeight : 0
            };
            /*if ($(".worldMapSmall").length > 0) {
                WHYJUSTIFY.fullscreenRestore.mapHeight = $(".worldMapSmall").height();
            } else if ($(".worldMapLarge").length > 0) {
                WHYJUSTIFY.fullscreenRestore.mapHeight =  $(".worldMapLarge").height();
            }*/

            iframe.css({ border: 0, position: "fixed", top: 0, marginTop: 0, left: 0, right: 0, bottom: 0, width: "100%", height: "100%" });
            $('#playground').css('padding', 0);
            logo.css({top: "10px", right: "10px"});
            $("#comments, #header, #footer, .entry-actions, #primary", window.top.document).hide();
            $("#main", window.top.document).css('padding-top: 0px');
            // set plain background
            $("body").css({
                backgroundImage: "url(\"/img/background.jpg\")",
                backgroundColor: "#474C52",
                backgroundAttachment: "fixed",
                backgroundPosition: "center center",
                backgroundRepeat: "repeat"
            });

            // hide unwanted elements
            $("#tpScroll").hide();
            // increase
            $(".worldMapSmall, .worldMapSmall svg, .worldMapLarge, .worldMapLarge svg").animate({ width : iframe.width(), height : iframe.height() }, 500);
            $(window.parent).scrollTop(0);
        } else {
            $(".entry-actions", window.top.document).css('padding-top: 40px');
            $("#comments, #header, #footer, .entry-actions, #primary", window.top.document).show();
            iframe.css('width', WHYJUSTIFY.fullscreenRestore.width);
            iframe.css('height', WHYJUSTIFY.fullscreenRestore.height);
            iframe.css('margin-top', WHYJUSTIFY.fullscreenRestore.iframeMargin);
            iframe.css({position: "static", top: "auto", left: "auto"});
            $('#playground').css('padding-top', WHYJUSTIFY.fullscreenRestore.playgroundPadding);
            logo.css(WHYJUSTIFY.fullscreenRestore.logoOffset);

            $("body").removeAttr('style');
            $(".worldMapSmall, .worldMapSmall svg, .worldMapLarge, .worldMapLarge svg").removeAttr('style');

            // show wanted elements
            $("#tpScroll").show();
            $(window.parent).scrollTop(WHYJUSTIFY.fullscreenRestore.scrollPosition);
            WHYJUSTIFY.fullscreenRestore = null;
        }
    }
}; // end of WHYJUSTIFY-specific object scope.

$(function () {
    INIT.onContentReady();

    rpc = jQuery.Zend.jsonrpc({ url : "/ajax/rpc.php", async : true});


    /*var testPremadeSmd = jQuery.Zend.jsonrpc({
        url: '/ajax/rpc.php',
        smd: {"transport":"POST","envelope":"JSON-RPC-2.0","contentType":"application\/json","SMDVersion":"2.0","target":"rpc.php","services":{"add":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"subtract":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"multiply":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"divide":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"float"},"hang":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"sleepTime","optional":false}],"returns":"boolean"}},"methods":{"add":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"subtract":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"multiply":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"integer"},"divide":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"x","optional":false},{"type":"integer","name":"y","optional":false}],"returns":"float"},"hang":{"envelope":"JSON-RPC-2.0","transport":"POST","parameters":[{"type":"integer","name":"sleepTime","optional":false}],"returns":"boolean"}}}
    });*/
});

