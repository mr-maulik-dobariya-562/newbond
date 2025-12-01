{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
@csrf

<!-- Email Address -->
<div>
  <x-input-label for="email" :value="__('Email')" />
  <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
  <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Password -->
<div class="mt-4">
  <x-input-label for="password" :value="__('Password')" />

  <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />

  <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<!-- Remember Me -->
<div class="block mt-4">
  <label for="remember_me" class="inline-flex items-center">
    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
  </label>
</div>

<div class="flex items-center justify-end mt-4">
  @if (Route::has('password.request'))
  <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
    {{ __('Forgot your password?') }}
  </a>
  @endif

  <x-primary-button class="ms-3">
    {{ __('Log in') }}
  </x-primary-button>
</div>
</form>
</x-guest-layout> --}}


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Sign in - {{ config('app.name') }}</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets') }}/static/logo.png">
  <link href="{{ asset('assets') }}/dist/css/tabler.min.css?1692870487" rel="stylesheet" />
  <link href="{{ asset('assets') }}/dist/css/tabler-flags.min.css?1692870487" rel="stylesheet" />
  <link href="{{ asset('assets') }}/dist/css/tabler-payments.min.css?1692870487" rel="stylesheet" />
  <link href="{{ asset('assets') }}/dist/css/tabler-vendors.min.css?1692870487" rel="stylesheet" />
  <link href="{{ asset('assets') }}/dist/css/demo.min.css?1692870487" rel="stylesheet" />
  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }
  </style>
</head>

<body class=" d-flex flex-column">
  <script src="{{ asset('assets') }}/dist/js/demo-theme.min.js?1692870487"></script>
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="." class="navbar-brand navbar-brand-autodark">
          <img src="{{ asset('assets') }}/static/logo.png" width="110" height="32" alt="Tabler" class="navbar-brand-image">
        </a>
      </div>
      <div class="card card-md">
        <div class="card-body">
          <h2 class="h2 text-center mb-4">Login to your account</h2>
          <form action="{{ route('login.store') }}" method="POST" novalidate>
            @csrf
            <div class="mb-3">
              <label class="form-label">Email / Mobile </label>
              <input type="text" class="form-control @error('email_or_username') is-invalid @enderror" value="{{ old('email_or_username') }}" name="email_or_username" placeholder="Enter Email or Mobile" autofocus>
              <x-input-error :messages="$errors->get('email_or_username')" class="mt-2" />
            </div>
            <div class="mb-2">
              <label class="form-label">
                Password
              </label>
              <div class="input-group input-group-flat">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Your password" autocomplete="off">
                <span class="input-group-text">
                  <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                      <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                  </a>
                </span>
              </div>
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mb-2">
              <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" />
                <span class="form-check-label">Remember me</span>
              </label>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('assets') }}/dist/js/tabler.min.js?1692870487" defer></script>
  <script src="{{ asset('assets') }}/dist/js/demo.min.js?1692870487" defer></script>
</body>

</html>