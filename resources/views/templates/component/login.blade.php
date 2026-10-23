@php
  $hasRegisterErrors = $errors->register->any() || (old('form_type') === 'register' && $errors->any());
  $hasLoginErrors = $errors->login->any() || (old('form_type') === 'login' && $errors->any()) || (!$hasRegisterErrors && $errors->any());
@endphp

<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Click Me</div>
              </div>

              {{-- Login Form --}}
              <div class="form">
                <h3>Login to your account</h3>

                @if($errors->login->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach($errors->login->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @elseif(old('form_type') === 'login' && $errors->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('login') }}" method="post">
                  @csrf
                  <input type="hidden" name="form_type" value="login">
                  <input type="email" name="email" placeholder="Email" value="{{ old('form_type') === 'register' ? '' : old('email') }}" required="">
                  <input type="password" name="password" placeholder="Password" required="">
                  <div style="margin: 0 0 15px; text-align: left;">
                    <label style="font-weight: normal; cursor: pointer; color: #555; font-size: 13px;">
                      <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="display: inline-block; width: auto; margin-right: 5px; vertical-align: middle;"> Ingat saya
                    </label>
                  </div>
                  <input type="submit" value="Login">
                </form>
              </div>

              {{-- Register Form --}}
              <div class="form">
                <h3>Create an account</h3>

                @if($errors->register->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach($errors->register->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @elseif(old('form_type') === 'register' && $errors->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px; font-size: 13px; text-align: left;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register') }}" method="post">
                  @csrf
                  <input type="hidden" name="form_type" value="register">
                  <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('form_type') === 'register' ? old('name') : '' }}" required="">
                  <input type="email" name="email" placeholder="Email Address" value="{{ old('form_type') === 'register' ? old('email') : '' }}" required="">
                  <input type="password" name="password" placeholder="Password (min 8 karakter)" required="">
                  <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required="">
                  <input type="submit" value="Register">
                </form>
              </div>

              <div class="cta"><a href="#">Forgot your password?</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('.toggle').click(function(){
      // Switches the Icon
      $(this).children('i').toggleClass('fa-pencil');
      // Switches the forms  
      $('.form').animate({
        height: "toggle",
        'padding-top': 'toggle',
        'padding-bottom': 'toggle',
        opacity: "toggle"
      }, "slow");
    });
  });

  var modalOpened = false;
  function openAuthModalOnError() {
    if (modalOpened) return;
    if (typeof $.fn.modal === 'function') {
      @if($hasRegisterErrors)
        modalOpened = true;
        $('#myModal').modal('show');
        $('.form-module .form').eq(0).hide();
        $('.form-module .form').eq(1).show();
        $('.toggle').children('i').removeClass('fa-pencil');
      @elseif($hasLoginErrors)
        modalOpened = true;
        $('#myModal').modal('show');
        $('.form-module .form').eq(0).show();
        $('.form-module .form').eq(1).hide();
      @endif
    }
  }

  $(document).ready(openAuthModalOnError);
  $(window).on('load', openAuthModalOnError);
</script>