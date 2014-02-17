jQuery(function() {

    $('.login').click(function(e) {
        e.preventDefault();

        navigator.id.request();
    });


    $('.logout').click(function(e) {
        e.preventDefault();

        navigator.id.logout();
    });

});
