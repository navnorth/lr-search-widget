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
                        <h2 id="lr-results-filter__title" class="lr-results-filter__title" title="Filter search results">Filter by
                          <i title="Expand search filters" id="lr-results-expand" class="fa fa-caret-down"></i>
                        </h2>
                        <div id="lr-results-facets" class="lr-results-filter__facets">
                          <div class="lr-results-filter__keywords embed-keys-selector"></div>
                          <div class="lr-results-filter__websites embed-domain-pie"></div>
                          <div class="lr-results-filter__grades embed-grades-selector"></div>
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
              <h2 class="lr-section__title">
                Loading Subjects <i class="fa fa-spinner fa-spin"></i>
              </h2>
            </div>
          </div>
          <div id="lr-section-standards" class="lr-tab-pane">
            <div id="lr-standards" class="lr-standards">
              <h2 class="lr-section__title">
                Loading Standards <i class="fa fa-spinner fa-spin"></i>
              </h2>
            </div>
          </div>
          <footer class="lr-footer">
            <div class="lr-footer__content">Educational data powered by Learning Registry.</div>
            <figure class="lr-footer__logo"><a href="http://learningregistry.org/" target="_blank"><img alt="LR logo" title="Go to LearningRegistry.org" src="{{ URL::to('img/learning_registry_logo_01.png') }}" /></a></figure>
          </footer>
      </div>
    </div>
</div>
