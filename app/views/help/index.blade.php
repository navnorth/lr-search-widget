<h1>API Help</h1>

    <p class="lead">Defined below are the API request supported by the search system.  This documentation is not complete, but covers the most used requests.</p>

<?php
    $sections = array(
        'all' => 'All API Requests',
        'search' => 'Search Requests',
        'examples' => 'Examples',
    );
?>

@foreach($sections as $s => $name)
    <hr />
    <h2 id="section-{{$s}}">{{ $name }}</h2>
    @include('help.api.'.$s)
@endforeach

