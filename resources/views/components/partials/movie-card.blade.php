<div class="col-md-2 w3l-movie-gride-agile">
  <a href="{{ route('movies.show', $film->id) }}" class="hvr-shutter-out-horizontal">
    <img src="{{ asset($film->poster) }}" title="album-name" alt=" " />
    <div class="w3l-action-icon">
      <i class="fa fa-play-circle" aria-hidden="true"></i>
    </div>
  </a>
  <div class="mid-1">
    <div class="w3l-movie-text">
      <h6><a href="{{ route('movies.show', $film->id) }}">{{ $film->title }}</a></h6>							
    </div>
    <div class="mid-2">
      <p>{{ $film->year }}</p>
      <div class="block-stars">
        <ul class="w3l-ratings">
          <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
          <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
          <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
          <li><a href="#"><i class="fa fa-star-half-o" aria-hidden="true"></i></a></li>
          <li><a href="#"><i class="fa fa-star-o" aria-hidden="true"></i></a></li>
        </ul>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
  @if ($film->year == now()->year)
    <div class="ribben two">
      <p>NEW</p>
    </div>
  @endif
</div>
