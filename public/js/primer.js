(function($) {

    $.log = function() {
        if(window.console && window.console.log)
        {
            window.console.log(arguments.length > 1 ? arguments : arguments[0]);
        }

        return arguments[0];
    }



})(jQuery);
