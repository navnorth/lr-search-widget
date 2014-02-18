<?php

    if(isset($user))
    {
        Former::populate($user);
    }

    echo Former::open_horizontal()
        ->method('post')
        ->action('/auth/update-profile');

    echo Former::lg_input('firstname', 'First Name')->maxlength(50);
    echo Former::lg_input('lastname', 'Last Name')->maxlength(50);
    echo Former::lg_input('organization', 'Organization')->maxlength(50);
    echo Former::lg_input('url', 'Website')->maxlength(200);

    echo Former::actions(Former::lg_primary_submit('Update Profile'));

    echo Former::close();

