window.prepareAuth = function(loggedInUser) {

    navigator.id.watch({
        loggedInUser: loggedInUser,
        onlogin: function(assertion) {
            $.post('/auth/persona', { assertion: assertion })
                .done(function() {
                    window.location.reload();
                })
                .fail(function() {
                    navigator.id.logout();
                })
        },
        onlogout: function() {
            $.post('/auth/logout')
                .done(function() {
                    window.location = '/';
                });
        }
    });


    // Configure .login and .logout links to use Persona
    jQuery(function($) {

        $('.login').click(function(e) {
            e.preventDefault();

            navigator.id.request();
        });


        // $('.logout').click(function(e) {
        //     e.preventDefault();
        //
        //     navigator.id.logout();
        // });
    })
};
