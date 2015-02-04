<h5>GET /api/search</h5>
<h5>POST /api/search</h5>

<p>Endpoint to search and filter through index records</p>

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
            <td>q</td>
            <td>No</td>
            <td><em>Empty String</em></td>
            <td> Search terms </td>
        </tr>
        <tr>
            <td>filters[]</td>
            <td>No</td>
            <td><em>None</em></td>
            <td>Filter key(s) to apply specific filtering to search request.</td>
        </tr>
        <tr>
            <td>limit</td>
            <td>No</td>
            <td>20</td>
            <td>Number of items to include in result.  Limited to max of 40 results.</td>
        </tr>
        <tr>
            <td>page</td>
            <td>No</td>
            <td>1</td>
            <td>Page number of results.</td>
        </tr>
        <tr>
            <td>facets[]</td>
            <td>No</td>
            <td><em>None</em></td>
            <td>Facet fields to include facet/count information in results.</td>
        </tr>
        <tr>
            <td>facet_filters[]</td>
            <td>No</td>
            <td><em>None</em></td>
            <td>Filters to apply based on facets and terms.  <a href="#section-examples">See examples for more details</a>.</td>
        </tr>
    </tbody>
</table>

<strong>Request:</strong>
<pre>{{ URL::to('/api/search') }}?api_key=&lt;API_KEY&gt;&amp;q=science&amp;limit=5</pre>
<strong>Response:</strong>
<pre class="pre-scrollable">
{
    "_shards": {
        "failed": 0,
        "successful": 5,
        "total": 5
    },
    "hits": {
        "hits": [
            {
                "_id": "5b89d3438b69b0ff4c5ad03df9905c3e",
                "_index": "lr-v2",
                "_score": 2.167745,
                "_source": {
                    "accessMode": [],
                    "blacklisted": false,
                    "description": "Science Service is a non-profit organization founded in 1921 with the mission \"to advance public understanding and appreciation of science.\" Through various training programs, talent searches, science fairs and scholarship competitions, the service promotes students' interest and learning in science, math, and engineering. The weekly newsletter, Science News Online, covers the \"important and exciting progress of science research and technology.\"",
                    "grades": [],
                    "hasScreenshot": false,
                    "keys": [
                        "Science -- General science",
                        "internetscout",
                        "Science -- Popular works",
                        "Science",
                        "Science -- Informal education",
                        "English",
                        "Internet Scout Project",
                        "Science -- Periodicals"
                    ],
                    "mediaFeatures": [],
                    "publisher": "SRI International on behalf of The National Science Digital Library",
                    "standards": [],
                    "title": "Science Service",
                    "url": "http://www.sciserv.org/",
                    "url_domain": "www.sciserv.org",
                    "whitelisted": true
                },
                "_type": "lr_doc"
            },
            {
                "_id": "bb27a0ae6939d54d7f7229f83b2254ad",
                "_index": "lr-v2",
                "_score": 2.0606077,
                "_source": {
                    "accessMode": [],
                    "blacklisted": false,
                    "description": "TryScience contains games and virtual reality adventures, virtual field trips to museums and science centers, and experiments. There is a Starfleet Academy virtual reality game involving communications, engineering, environments, and lifeforms; an electrolysis experiment that can be conducted online or at home; and a game about an African Naked Mole-rat colony. Experiments are categorized by: Earth science, biological science, mathematics, physical science, space science, technology and engineering, chemistry, social science, and medicine and health.",
                    "grades": [
                        "elementary school",
                        "middle school"
                    ],
                    "hasScreenshot": false,
                    "keys": [
                        "dlese.org",
                        "Biology",
                        "Soil science",
                        "Middle School",
                        "Chemistry",
                        "Science",
                        "Meteorology",
                        "Physics",
                        "Atmospheric science",
                        "en",
                        "Space sciences",
                        "Elementary School",
                        "Physical sciences",
                        "DLESE Community Collection",
                        "Mathematics",
                        "Astronomy",
                        "Technology",
                        "Earth science"
                    ],
                    "mediaFeatures": [],
                    "publisher": "SRI International on behalf of The National Science Digital Library",
                    "standards": [],
                    "title": "Try Science",
                    "url": "http://www.tryscience.org/home.html",
                    "url_domain": "www.tryscience.org",
                    "whitelisted": true
                },
                "_type": "lr_doc"
            },
            {
                "_id": "9e3e8f36c9f1ff3938b908774517fedb",
                "_index": "lr-v2",
                "_score": 2.052906,
                "_source": {
                    "accessMode": [],
                    "blacklisted": false,
                    "description": "Planet Science, managed by the UK's National Endowment for Science Technology and Arts (NESTA), is a Macromedia Flash Player-enhanced website offering creative and fun approaches to teaching and learning science. Students can discover the world of science through innumerable entertaining activities, experiments, and online adventures such as PS100x recorder and sequencer where users can learn about sound waves by comparing waveforms for different sounds and, with a microphone, record and compare their own sounds. Parents can learn how to throw a fun science-style party; find a Fun Pack filled with holiday activities, games, and quizzes; and can learn how to answer the tough questions children interested in science may ask. Young adults can discover which science profession is right for them. The website even offers materials for children under eleven. With so much to offer, this website should be a destination for anyone interested in the subject.",
                    "grades": [],
                    "hasScreenshot": false,
                    "keys": [
                        "Science -- General science",
                        "internetscout",
                        "Science",
                        "English",
                        "Science -- Study and teaching",
                        "Internet Scout Project"
                    ],
                    "mediaFeatures": [],
                    "publisher": "SRI International on behalf of The National Science Digital Library",
                    "standards": [],
                    "title": "Planet Science",
                    "url": "http://www.scienceyear.com/home.html",
                    "url_domain": "www.scienceyear.com",
                    "whitelisted": true
                },
                "_type": "lr_doc"
            },
            {
                "_id": "6f00b60d011ab84d7443345b33c4e9b9",
                "_index": "lr-v2",
                "_score": 2.0516644,
                "_source": {
                    "accessMode": [],
                    "blacklisted": false,
                    "description": "Science Alert publishes articles on a range of science topics from a large selection of journals that follow open access policy. Topics include agricultural and biological sciences, physical sciences, chemistry, Earth and environmental sciences, medical sciences, and many others. The articles are peer-reviewed, published online, and are referenced in a variety of abstract services, databases, and directories. Journals can be browsed by subject or by an alphabetical listing. Other materials include a page devoted to new titles, information for authors, and subscription information.",
                    "grades": [
                        "informal education",
                        "higher education",
                        "undergraduate (upper division)",
                        "undergraduate (lower division)",
                        "vocational/professional development education",
                        "graduate/professional"
                    ],
                    "hasScreenshot": false,
                    "keys": [
                        "Informal Education",
                        "Materials science",
                        "Higher Education",
                        "Social science",
                        "General Science and STEM Gateways and Resources",
                        "Undergraduate (Upper Division)",
                        "Undergraduate (Lower Division)",
                        "Ecology, Forestry and Agriculture",
                        "Biological science",
                        "ncs-NSDL-COLLECTION-000-003-111-907",
                        "Health/Medicine",
                        "Computer science",
                        "Vocational/Professional Development Education",
                        "Physical science",
                        "Life Science",
                        "General science",
                        "Chemistry",
                        "Agriculture",
                        "Engineering",
                        "Graduate/Professional",
                        "Geoscience"
                    ],
                    "mediaFeatures": [],
                    "publisher": "SRI International on behalf of The National Science Digital Library",
                    "standards": [],
                    "title": "Science Alert",
                    "url": "http://scialert.net/index.php",
                    "url_domain": "scialert.net",
                    "whitelisted": true
                },
                "_type": "lr_doc"
            },
            {
                "_id": "8b84f9c4a4d988b202006032f4b04b2a",
                "_index": "lr-v2",
                "_score": 2.0319693,
                "_source": {
                    "accessMode": [],
                    "blacklisted": false,
                    "description": "Science Playwiths consists of a set of small experiments using simple everyday science, selected for quick, easy use by K-6 teachers. Topics include Earth science, physics, electricity and magnetism, fluid flow, sound, light, and others. There is also a set of more complex experiments, methods and enquiries; an Australian science and technology timeline; a set of ideas for science projects and some help; and \"The Ugly Islands\", a simulation exercise that offers problems for people to play with.",
                    "grades": [
                        "elementary school",
                        "middle school"
                    ],
                    "hasScreenshot": false,
                    "keys": [
                        "dlese.org",
                        "Biology",
                        "Soil science",
                        "Middle School",
                        "Geology",
                        "Science",
                        "en-US",
                        "Elementary School",
                        "Space sciences",
                        "Physical sciences",
                        "DLESE Community Collection",
                        "Mathematics",
                        "Astronomy",
                        "Physics",
                        "Earth science"
                    ],
                    "mediaFeatures": [],
                    "publisher": "SRI International on behalf of The National Science Digital Library",
                    "standards": [
                        "s10113aa",
                        "s100de90",
                        "s102dbaa",
                        "s101fd8a",
                        "s100b227",
                        "s1014c32",
                        "s1007522",
                        "s101f7fa"
                    ],
                    "title": "Science Playwiths",
                    "url": "http://members.ozemail.com.au/~macinnis/scifun/index.htm",
                    "url_domain": "members.ozemail.com.au",
                    "whitelisted": true
                },
                "_type": "lr_doc"
            }
        ],
        "max_score": 2.167745,
        "total": 45790
    },
    "time": 0.29221701622,
    "timed_out": false,
    "took": 288
}
</pre>


