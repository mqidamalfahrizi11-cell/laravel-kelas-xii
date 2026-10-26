@extends('templates.index')

@section('content')
<div class="general-agileits-w3l">
  <div class="w3l-medile-movies-grids">
    <!-- /movie-browse-agile -->
      <div class="movie-browse-agile">
          <!--/browse-agile-w3ls -->
          <div class="browse-agile-w3ls general-w3ls">
            <div class="tittle-head">
              @if (isset($keyword))
                <h4 class="latest-text">Hasil pencarian: {{ $keyword }}</h4>
              @elseif ($genreFilm !== null)
                <h4 class="latest-text">Movies Of Genre {{ $genreFilm->name }}</h4>
              @else
                <h4 class="latest-text">List Of Movies</h4>
              @endif
              <div class="container">
                <div class="agileits-single-top">
                  <ol class="breadcrumb">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('movies') }}">Movies</a></li>
                    @if (isset($keyword))
                      <li class="active">Hasil pencarian: {{ $keyword }}</li>
                    @elseif ($genreFilm !== null)
                      <li class="active">Genre {{ $genreFilm->name }}</li>
                    @else
                      <li class="active">All Movies</li>
                    @endif
                  </ol>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="browse-inner">
              @if ($films->count() == 0)
                <div class="col-md-12 text-center" style="padding: 50px 0;">
                  <h4 style="color: #777;">Film tidak ditemukan</h4>
                </div>
              @else
                @foreach ($films as $film)
                  @include('components.partials.movie-card', ['film' => $film])
                @endforeach
              @endif
              <div class="clearfix"> </div>
            </div>
              </div>
          </div>
      <!--//browse-agile-w3ls -->
          <div class="blog-pagenat-wthree">
            @if ($films->hasPages())
                <nav>
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if ($films->onFirstPage())
                            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                <span class="frist" aria-hidden="true">&lsaquo; Prev</span>
                            </li>
                        @else
                            <li>
                                <a class="frist" href="{{ $films->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo; Prev</a>
                            </li>
                        @endif
                        {{-- <Number Page --}}
                        @for ($numberPage = 1; $numberPage <= $films->lastPage(); $numberPage++)
                          @if ($numberPage == $films->currentPage())
                            <li class="disabled" aria-disabled="true" >
                              <span class="frist" aria-hidden="true">{{ $numberPage }}</span>
                            </li>
                          @else
                            <li><a href="{{ $films->url($numberPage) }}" class="frist">{{ $numberPage }}</a></li>
                          @endif
                        @endfor
                        {{-- Number Page/> --}}
                        {{-- Next Page Link --}}
                        @if ($films->hasMorePages())
                            <li>
                                <a class="frist" href="{{ $films->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">Next &rsaquo;</a>
                            </li>
                        @else
                            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                <span class="frist" aria-hidden="true">Next &rsaquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            @endif
          </div>

          @isset($recommendations)
            <div class="browse-agile-w3ls general-w3ls" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
              <div class="tittle-head">
                <h4 class="latest-text">
                  @if (!empty($notFound))
                    Film tidak ditemukan, mungkin kamu suka:
                  @else
                    Rekomendasi Film
                  @endif
                </h4>
              </div>
              <div class="container">
                <div class="browse-inner">
                  @foreach ($recommendations as $film)
                    @include('components.partials.movie-card', ['film' => $film])
                  @endforeach
                  <div class="clearfix"> </div>
                </div>
              </div>
            </div>
          @endisset
        </div>
    <!-- //movie-browse-agile -->
    </div>	
  </div>
<!-- //w3l-medile-movies-grids -->
</div>
@endsection