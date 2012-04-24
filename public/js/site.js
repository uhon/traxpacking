var C, INIT, FORM, UI, SVG;

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
        $('#popupForm').load(
            linkElement.attr('href'),
            function () {
                $('#popupForm').dialog({
                    title:"My Form",
                    width:500,
                    show:{effect:'explode', duration:750 },
                    hide:{effect:'explode', duration:750 },
                    modal:true,
                    autoOpen:false
                });
                $('#popupForm').dialog('open');
            }
        );
    }
};  // end of FORM object scope.

INIT = {   // start of INIT object scope.
    onContentReady:function () {
        window.setTimeout(function () {
            UI.showFlashMessages();
        }, 400);

        C.log('Content Replaced rerunning all ContentBindings');
        FORM.bindEditLinks();
        FORM.bindDeleteForms();
    }
}; // end of INIT object scope.

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
            $('#removePictureLink').each(function() {
                var removeLink = $(this).attr('href');
                $(this).closest('tr').hide();
            });
            $('ul', tabContainer).show();
            tabContainer.tabs({
            });
            // close icon: removing the tab on click
            $(".tabList span.ui-icon-close", tabContainer).click(function() {
                var index = $(".tabList li", tabContainer).index( $( this ).closest('li') ),
                    deleteLink = $('div:nth-of-type(' + (index+1) + ') a#removePictureLink', tabContainer).attr('href');
                    if(confirm('do yo really want to remove this Picture from POI?')) {
                        tabContainer.waitForIt();
                        $.get(deleteLink, function(data) {
                            tabContainer.waitForItStop();
                            // TODO: No success/error szenario catched
                            tabContainer.tabs( "remove", index );
                        });
                    }
            });
        }
    }
}; // end of UI object scope.


SVG = { // start of SVG object scope.
    worldMap: null,
    createSvgWorldMap: function() {
        SVG.worldMap = $('#svgMapContainer').svg();
        SVG.worldMap = $('#svgMapContainer').svg('get');
        SVG.worldMap.load("/img/world_map.svg", {addTo: true, changeSize: false, onLoad: SVG.setupSvgWorldMap });
    },

    setupSvgWorldMap: function() {
        //svg.configure({viewBox: '-0 0 600 400'}, true)
        var minX = 99999,
            minY = 99999,
            maxX = -99999,
            maxY = -99999,
            maxWidth = 0,
            maxHeight = 0,
            borderSize = 25,
            dimensions = null,
            growHeight,
            growWidth,
            countryList = {
                0 : {name : 'Switzerland', pictures : '5'},
                1 : {name : 'Austria', pictures : '9'},
                2 : {name : 'Hungary', pictures : '5'},
                3 : {name : 'Romania', pictures : '5'},
                
                4 : {name : 'Bulgaria', pictures : '5'},
                5 : {name : 'Turkey', pictures : '5'},
                6 : {name : 'Georgia', pictures : '5'},
                7 : {name : 'Azerbaijan', pictures : '5'},
                8 : {name : 'Turkmenistan', pictures : '5'},
                9 : {name : 'Uzbekistan', pictures : '5'},
                10 : {name : 'Kyrgyzstan', pictures : '5'},
                11 : {name : 'China', pictures : '5'},
                12 : {name : 'Vietnam', pictures : '5'},
                13 : {name : 'Laos', pictures : '5'},
                14 : {name : 'Thailand', pictures : '5'},
                15 : {name : 'Cambodia', pictures : '5'},
                16 : {name : 'Myanmar (Burma)', pictures : '5'},
                17 : {name : 'India', pictures : '9'}
                /*18 : {name : 'Australia', pictures : '9'},
                19: {name : 'Chile', pictures : '5'},*/
            };


        //countryList = VODOO
        $.each(countryList, function(key, country) {
            if(country.pictures > 0) {
                C.log(country.name);

                var element = $('path[title="' + country.name + '"]', SVG.worldMap.root());

                element.attr("fill", '#ccc');
                element.attr("class", 'active');

                dimensions = element.get(0).getBBox();
            C.log(dimensions);
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

            }
        });

        if(maxWidth === 0 || maxHeight === 0) {
            SVG.worldMap.configure({viewBox: "50 -800 740 240"}, true);
            SVG.worldMap.configure({scale: 3});
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

            C.log("minX:" + minX + ", minY:" + minY + ", width:" + maxWidth + ", height:" + maxHeight);
            SVG.worldMap.configure({viewBox: minX + " " + minY + " " + maxWidth + " " + maxHeight}, true);

            SVG.worldMap.configure({scale: 3});

            $('path.active', SVG.worldMap.root()).bind('mouseover', function(e) {
                $(this).attr('fill', 'green');
            });
            $('path.active', SVG.worldMap.root()).bind('mouseout', function(e) {
                $(this).attr('fill', '#ccc');
            });
        }
    }
}; // end of SVG object scope.

$(function () {
    INIT.onContentReady();

    rpc = jQuery.Zend.jsonrpc({ url : "/ajax/rpc.php", async : true});

    C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.random());
    C.log(rpc.hello());
    C.log(rpc.hello());
    C.log(rpc.hello());
    C.log(rpc.hello());

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

