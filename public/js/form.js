//set up form function
function initForm(formName, preSubmit, postSubmit) {
    var targetformName = '#' + formName;

    // bind form using 'ajaxForm'
    $(targetformName).ajaxForm({
        data: { formOrigin : window.location.pathname },
        beforeSubmit : preSubmit, // pre-submit callback
        success : postSubmit // post-submit callback
    });
}

function popupForm(url) {
    $('#popupForm').load(
        url,
        function() {
            $('#popupForm').dialog({
                title: "My Form",
                width: 500,
                show: {effect: 'explode', duration: 750 },
                hide: {effect: 'explode', duration: 750 },
                modal: true,
                autoOpen: false
            });
            $('#popupForm').dialog('open');
        }
    );
}

function onContentReady() {
    C.log('Content Replaced rerunning all ContentBindings');

    $(".flashMessages").hide();
    setTimeout(function() { $(".flashMessages").slideDown(); }, 400);

    C.log('Bind edit-Links: ' + $('a.ajaxEdit'));
    $('a.ajaxEdit').each(function() {
        $(this).click(function() {
            C.log($(this).attr('href'));
            $(".flashMessages").slideUp();
            popupForm($(this).attr('href'));
            return false;
        });
    });
}

$(function() {
    onContentReady();
});