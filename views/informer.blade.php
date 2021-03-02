<div class="{{ $classes }}">
  {{ config('honeybadger.user_informer.message', 'Honeybadger Error') }} {{ session('honeybadger_last_error') }}
</div>