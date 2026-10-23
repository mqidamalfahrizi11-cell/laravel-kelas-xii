<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
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