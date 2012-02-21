var C, INIT, FORM, UI;

C = {
    // console wrapper
    debug:true, // global debug on|off
    quietDismiss:true, // may want to just drop, or alert instead
    log:function () {
        "use strict";
        var curdate,
            dateString,
            i,
            l,
            result,
            j,
            le;

        if (!C.debug) {
            return false;
        }

        if (typeof (console) === 'object' && typeof console.log !== "undefined") {
            try {
                curdate = new Date();
                dateString = curdate.getHours() + curdate.getMinutes() + curdate.getSeconds();

                arguments.unshift(dateString);
                console.log.apply(this, arguments); // safari's console.log can't accept scope...
            } catch (e) {
                // so we loop instead.
                for (i = 0, l = arguments.length; i < l; i) {
                    console.log(arguments[i]);
                    i = i + 1;
                }
            }
        } else {
            if (!C.quietDismiss) {
                result = "";
                for (j = 0, le = arguments.length; j < le; j) {
                    result += arguments[j] + " (" + typeof arguments[j] + ") ";
                    j = j + 1;
                }


                alert(result);
            }
        }
        return true;
    }
}; // end console wrapper.


FORM = {  // start of FORM object scope.
    bindEditLinks: function() {
        // Bind Edit-Links
        C.log('Bind edit-Links: ' + $('a.ajaxEdit'));
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
        C.log('Bind delete-Links: ' + $('form.deleteForm'));
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

$(function () {
    INIT.onContentReady();
});