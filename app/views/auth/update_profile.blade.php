<fieldset>
    <legend>Update Profile</legend>

    @include('auth.forms.update_profile', array('user' => Session::get('user')))
</fieldset>
