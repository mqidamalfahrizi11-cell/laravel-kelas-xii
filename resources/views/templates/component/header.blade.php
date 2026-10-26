<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="{{ route('movies.search') }}" method="GET">
        <div class="sg-input-wrapper">
          <input type="text" id="search-input" name="q" placeholder="Search" value="{{ request('q') }}" required="" autocomplete="off">
          <div id="suggest-box"></div>
        </div>
        <input type="submit" value="Go">
        <div class="clearfix"></div>
      </form>
    </div>
    <div class="w3l_sign_in_register" style="width: auto;">
      <ul>
        @guest
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
        @endguest
        @auth
          @if(Auth::user()->isAdmin())
            <li style="margin-right: 10px; vertical-align: middle;">
              <a href="{{ route('admin.dashboard') }}" style="background: #212121; color: #fff; padding: 6px 15px; font-weight: bold; text-transform: uppercase;">
                <i class="fa fa-dashboard" style="padding-right: 5px;"></i> Dashboard
              </a>
            </li>
          @endif
          <li style="color: #212121; font-weight: bold; margin-right: 10px; vertical-align: middle;">
            <i class="fa fa-user" style="padding-right: 5px;"></i> {{ Auth::user()->name }}
          </li>
          <li style="vertical-align: middle;">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" style="color: #fff; background: #FF8D1B; border: none; padding: 6px 20px; font-weight: bold; text-transform: uppercase; cursor: pointer;">
                Logout
              </button>
            </form>
          </li>
        @endauth
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>

<style>
  .w3_search form {
    position: relative;
    width: 100%;
  }
  .w3_search .sg-input-wrapper {
    position: relative;
    float: left;
    width: 78%;
  }
  .w3_search .sg-input-wrapper input[type="text"] {
    width: 100% !important;
    float: left !important;
    box-sizing: border-box !important;
    margin: 0 !important;
  }
  .w3_search input[type="submit"] {
    width: 22% !important;
    float: left !important;
    box-sizing: border-box !important;
    margin: 0 !important;
  }
  #suggest-box {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    min-width: 280px;
    max-width: calc(100vw - 30px);
    background: #fff !important;
    border: 1px solid #ddd !important;
    border-radius: 0 0 8px 8px !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, .15) !important;
    z-index: 9999 !important;
    max-height: 360px !important;
    overflow-y: auto !important;
    text-align: left !important;
    display: none;
    margin-top: 2px !important;
    box-sizing: border-box !important;
    padding: 0 !important;
  }
  #suggest-box .sg-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 12px !important;
    cursor: pointer !important;
    border-bottom: 1px solid #f0f0f0 !important;
    color: #222 !important;
    text-decoration: none !important;
    background: #fff;
    box-sizing: border-box !important;
    transition: background 0.15s ease;
  }
  #suggest-box .sg-item:last-child {
    border-bottom: none !important;
  }
  #suggest-box .sg-item:hover,
  #suggest-box .sg-item.active {
    background: #fff3e6 !important;
  }
  #suggest-box .sg-item.active .sg-title {
    color: #FF8D1B !important;
  }
  #suggest-box .sg-item img {
    width: 40px !important;
    height: 56px !important;
    max-width: 40px !important;
    max-height: 56px !important;
    object-fit: cover !important;
    flex-shrink: 0 !important;
    border-radius: 3px !important;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #eee !important;
  }
  #suggest-box .sg-info {
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    min-width: 0 !important;
    text-align: left !important;
  }
  #suggest-box .sg-title {
    font-weight: 600 !important;
    font-size: 14px !important;
    line-height: 1.2 !important;
    color: #222 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin: 0 !important;
  }
  #suggest-box .sg-year {
    font-size: 12px !important;
    color: #888 !important;
    margin-top: 4px !important;
    line-height: 1 !important;
  }
  #suggest-box .sg-empty {
    padding: 10px 12px !important;
    color: #888 !important;
    font-size: 13px !important;
    text-align: center !important;
    cursor: default !important;
    user-select: none !important;
  }
</style>

<script>
$(document).ready(function() {
  var debounceTimer = null;
  var currentXhr = null;
  var $input = $('#search-input');
  var $box = $('#suggest-box');
  var selectedIndex = -1;
  var currentItems = [];

  function abortRequest() {
    if (currentXhr && currentXhr.readyState !== 4) {
      currentXhr.abort();
      currentXhr = null;
    }
  }

  function hideDropdown() {
    $box.hide().empty();
    selectedIndex = -1;
    currentItems = [];
  }

  function setHighlight(index) {
    var $items = $box.find('.sg-item');
    if ($items.length === 0) return;

    if (index >= $items.length) {
      index = 0;
    } else if (index < 0) {
      index = $items.length - 1;
    }

    selectedIndex = index;
    $items.removeClass('active');
    var $activeItem = $items.eq(index).addClass('active');

    if ($activeItem.length) {
      var boxTop = $box.scrollTop();
      var boxHeight = $box.height();
      var itemTop = $activeItem.position().top + boxTop;
      var itemHeight = $activeItem.outerHeight();

      if (itemTop < boxTop) {
        $box.scrollTop(itemTop);
      } else if (itemTop + itemHeight > boxTop + boxHeight) {
        $box.scrollTop(itemTop + itemHeight - boxHeight);
      }
    }
  }

  function renderSuggestions(items) {
    $box.empty();
    selectedIndex = -1;
    currentItems = items || [];

    if (!items || items.length === 0) {
      var $empty = $('<div>').addClass('sg-empty').text('Film tidak ditemukan');
      $box.append($empty).show();
      return;
    }

    $.each(items, function(index, item) {
      var $item = $('<div>').addClass('sg-item').attr('data-index', index);

      var $img = $('<img>')
        .attr('src', item.poster || '')
        .attr('alt', item.title || '')
        .attr('width', '40')
        .attr('height', '56');

      var $info = $('<div>').addClass('sg-info');
      var $title = $('<div>').addClass('sg-title').text(item.title || '');
      var $year = $('<div>').addClass('sg-year').text(item.year || '');

      $info.append($title, $year);
      $item.append($img, $info);

      $item.on('mousedown', function(e) {
        e.preventDefault();
        window.location.href = item.url;
      });

      $item.on('mouseenter', function() {
        setHighlight(index);
      });

      $box.append($item);
    });

    $box.show();
  }

  function fetchSuggestions(query) {
    abortRequest();

    var trimmed = $.trim(query);
    if (trimmed.length < 2) {
      hideDropdown();
      return;
    }

    currentXhr = $.getJSON('{{ route("movies.suggest") }}', { q: trimmed })
      .done(function(data) {
        renderSuggestions(data);
      })
      .fail(function(jqxhr, textStatus) {
        if (textStatus !== 'abort') {
          hideDropdown();
        }
      });
  }

  $input.on('input', function() {
    clearTimeout(debounceTimer);
    var val = $(this).val();
    debounceTimer = setTimeout(function() {
      fetchSuggestions(val);
    }, 250);
  });

  $input.on('keydown', function(e) {
    var $items = $box.find('.sg-item');
    var isOpen = $box.is(':visible') && $items.length > 0;

    if (e.which === 40) { // Panah bawah
      if (isOpen) {
        e.preventDefault();
        setHighlight(selectedIndex + 1);
      }
    } else if (e.which === 38) { // Panah atas
      if (isOpen) {
        e.preventDefault();
        setHighlight(selectedIndex - 1);
      }
    } else if (e.which === 13) { // Enter
      if (isOpen && selectedIndex >= 0 && selectedIndex < currentItems.length) {
        e.preventDefault();
        window.location.href = currentItems[selectedIndex].url;
      }
    } else if (e.which === 27) { // Escape
      hideDropdown();
    }
  });

  $input.on('focus', function() {
    var val = $.trim($(this).val());
    if (val.length >= 2 && currentItems.length > 0) {
      $box.show();
    } else if (val.length >= 2) {
      fetchSuggestions(val);
    }
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('.sg-input-wrapper').length) {
      $box.hide();
    }
  });
});
</script>