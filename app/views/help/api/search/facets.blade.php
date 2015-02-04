<h5>GET /api/search</h5>
<h5>POST /api/search</h5>

<p>Endpoint to return facet data</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Required</th>
            <th>Default</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>facet</td>
            <td><strong>Yes</strong></td>
            <td>n/a</td>
            <td>Name of facet to return data on</td>
        </tr>
        <tr>
            <td>q</td>
            <td>No</td>
            <td><em>Empty</em></td>
            <td>Term to filter results.</td>
        </tr>
        <tr>
            <td>limit</td>
            <td>No</td>
            <td>15</td>
            <td>Number of items to include in result.  Limited to max of 40 results.</td>
        </tr>
    </tbody>
</table>

<strong>Request:</strong>
<pre>{{ URL::to('/api/search/facets') }}?api_key=&lt;API_KEY&gt;&amp;facet=url_domain&amp;q=nasa&amp;limit=5</pre>
<strong>Response:</strong>
<pre class="pre-scrollable">
{
    "_type": "terms",
    "missing": 0,
    "other": 655,
    "terms": [
        {
            "count": 3574,
            "term": "photojournal.jpl.nasa.gov"
        },
        {
            "count": 1118,
            "term": "svs.gsfc.nasa.gov"
        },
        {
            "count": 67,
            "term": "www.nasa.gov"
        },
        {
            "count": 41,
            "term": "science.nasa.gov"
        },
        {
            "count": 27,
            "term": "imagine.gsfc.nasa.gov"
        }
    ],
    "total": 5482
}
</pre>


