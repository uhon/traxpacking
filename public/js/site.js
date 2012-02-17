var C = {
    // console wrapper
    debug: true, // global debug on|off
    quietDismiss: true, // may want to just drop, or alert instead
    log: function() {
        "use strict";
        var curdate,
            dateString,
            i,
            l,
            result,
            j,
            le;

        if (!C.debug) { return false; }

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
                    i = i+1;
                }
            }
        } else {
            if (!C.quietDismiss) {
                result = "";
                for (j = 0, le = arguments.length; j < le; j) {
                    result += arguments[j] + " (" + typeof arguments[j] + ") ";
                    j = j+1;
                }


                alert(result);
            }
        }
        return true;
    }
}; // end console wrapper.

$(function() {
    "use strict";
});