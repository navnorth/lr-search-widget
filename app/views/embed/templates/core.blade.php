<?php
    use Widget as W;
    $s = $widget->widget_settings;

    $demo = isset($demo) ? $demo : false;

    $facets = !!$s[W::SETTINGS_SHOW_FACETS];
    $modal = !!$s[W::SETTINGS_SHOW_RESOURCE_MODAL];
    $flagging = !!$s[W::SETTINGS_ENABLE_FLAGGING];
?>
<link type="text/css" rel="stylesheet" href="{{ URL::to('/css/embed.css') }}?_={{ time() }}" />
<link type="text/css" rel="stylesheet" href="{{ URL::to('/vendor/perfect-scrollbar/min/perfect-scrollbar-0.4.8.min.css') }}" />
<link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/0.9.9/magnific-popup.css" />
<link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css" />

<div class="lr-embed lr-embed-{{$widget->widget_key}}
    {{ $facets ? '' : 'no-facets' }}
    {{ $modal ? '' : 'no-modal' }}
    {{ $flagging ? '' : 'no-flagging' }}
    ">

    <div class="lr-embed-wrapper">
      <header class="lr-branding embed-heading" role="banner">
      </header>
      <nav id="lr-nav" class="lr-nav" role="navigation">
          <ul class="lr-nav__list">
              <li class="lr-nav__item">
                  <a title="Search the Learning Registry" class="lr-nav__link lr-nav-link__search" href="#lr-section-search">Search</a>
              </li>
              <li class="lr-nav__item">
                  <a title="Browse by Subject" class="lr-nav__link lr-nav-link__subjects" href="#lr-section-subjects">Browse by Subject</a>
              </li>
              <li class="lr-nav__item">
                  <a title="Browse by Standard" class="lr-nav__link lr-nav-link__standards" href="#lr-section-standards">Browse by Standard</a>
              </li>
          </ul>
      </nav>

      <div id="lr-content" role="main">
          <div id="lr-section-search" class="lr-section lr-tab-pane">
              <!-- <h2 class="lr-section__title">Find Cool Stuff</h2>
              <p class="lr-section__intro">
                The world's learning resources at your fingertips.
              </p> -->
            <div class="lr-search-form lr-form embed-search-bar" role="search"></div>
            <div class="lr-results">
              <div class="lr-pager embed-search-pagination"></div>
              <div class="embed-search-facets"></div>
              @if($facets || $demo)
                  <div class="embed-facets">
                      <div class="lr-results-filter">
                        <h2 id="lr-results-filter__title" class="lr-results-filter__title" title="Filter search results">Filter by<i title="Expand search filters" id="lr-results-expand" class="fa fa-caret-down"></i></h2>
                        <div id="lr-results-facets" class="lr-results-filter__facets">
                          <i  title="Collapse search filters" id="lr-results-collapse" class="fa fa-times"></i>
                          <div class="lr-results-filter__keywords embed-keys-selector"></div>
                          <div class="lr-results-filter__websites embed-domain-pie"></div>
                          <div class="lr-results-filter__publishers embed-publishers-selector"></div>
                          <div class="lr-results-filter__mediaFeatures embed-mediaFeatures-selector"></div>
                        </div>
                      </div>
                  </div>
              @endif
              <div class="embed-search-results" id="lr-results-list">
                <div class="embed-search-loading"></div>
                <div></div>
              </div>
            </div>
          </div>

          <div id="lr-section-subjects" class="lr-tab-pane">
            <div id="lr-subjects" class="lr-subjects">
              <ul>
                <li>
                  Arts & Music (2,028)
                  <ul>
                    <li>Artists (125)</li>
                    <li>Music (975)
                      <ul>
                        <li>Blues, Gospel, Folk (12)</li>
                        <li>Jazz (79)</li>
                        <li>Sheet Music (10)</li>
                        <li>Other Music (18)
                          <ul>
                            <li>Test level</li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                    <li>Theatre & Film (8)</li>
                    <li>Visual Arts (797)
                      <ul>
                        <li>Architecture (211)</li>
                        <li>Drawing & Prints (22)</li>
                        <li>Painting (156)</li>
                        <li>Photography (138)</li>
                        <li>Sculpture (30)</li>
                        <li>Other Visual Arts (25)</li>
                      </ul>
                    </li>
                    <li>Other Arts & Music (25)</li>
                  </ul>
                </li>
                <li>
                  Health & Phys Ed (156)
                  <ul>
                    <li>Phys ed. exercise (6)</li>
                    <li>Substance abuse (12)</li>
                    <li>Other Health (53)</li>
                  </ul>
                </li>
                <li>
                  Language Arts (478)
                  <ul>
                    <li>Literature & Writers (204)
                      <ul>
                        <li>American Literature (30)</li>
                        <li>Poetry (88)
                          <ul>
                            <li>Test level</li>
                          </ul>
                        </li>
                        <li>Other Literature (32)</li>
                      </ul>
                    </li>
                    <li>Reading (398)</li>
                    <li>Other Language Arts (21)</li>
                  </ul>
                </li>
                <li>
                  Math (1,276)
                  <ul>
                    <li>Algebra (845)</li>
                    <li>Data Analysis (156)</li>
                    <li>Geometry (905)</li>
                    <li>Measurement (695)</li>
                    <li>Number & Operations (14)</li>
                    <li>Other Math (50)</li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
          <div id="lr-section-standards" class="lr-tab-pane">
            <div id="lr-standards" class="lr-standards">
              <ul>
                <li>Mathematics
                  <ul>
                    <li>Grade K
                      <ul>
                        <li>Counting and Cardinality
                          <ul>
                            <li>Know number names and the count sequence
                              <div class="lr-standards__info">
                                Standard 1. Count to 100 by ones and by tens.
                                Standard 2.Count forward beginning from a given number within the known sequence (instead of having to begin at 1).
                                Standard 3.Write numbers from 0 to 20. Represent a number of objects with a written numeral 0-20 (with 0 representing a count of no objects).
                              </div>
                            </li>
                            <li>Count to tell the number of objects
                              <div class="lr-standards__info">
                                Standard 4.Understand the relationship between numbers and quantities; connect counting to cardinality.Introduction
                                Standard 5.Count to answer “how many?” questions about as many as 20 things arranged in a line, a rectangular array, or a circle, or as many as 10 things in a scattered configuration; given a number from 1–20, count out that many objects.
                              </div>
                            </li>
                            <li>Compare numbers
                              <div class="lr-standards__info">
                                Standard 6.Identify whether the number of objects in one group is greater than, less than, or equal to the number of objects in another group, e.g., by using matching and counting strategies.
                                Standard 7.Compare two numbers between 1 and 10 presented as written numerals.
                              </div>
                            </li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
          <footer class="lr-footer">
            <div class="lr-footer__content">Educational data powered by Learning Registry.</div>
            <figure class="lr-footer__logo"><a href="http://learningregistry.org/" target="_blank"><img alt="LR logo" title="Go to LearningRegistry.org" src="{{ URL::to('img/learning_registry_logo_01.png') }}" /></a></figure>
          </footer>
      </div>
    </div>
</div>
