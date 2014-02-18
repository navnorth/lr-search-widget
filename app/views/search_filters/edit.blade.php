<fieldset>
    <legend>Edit Search Filter: {{ $filter->name }}</legend>


    @include('search_filters.forms.filter', array('searchFilter' => $filter))
</fieldset>
