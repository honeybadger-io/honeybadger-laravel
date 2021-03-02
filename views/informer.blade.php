@if(config('honeybadger.user_informer.enabled') && session('honeybadger_last_error'))
    <div class="{{ $classes }}">
        {{ config('honeybadger.user_informer.message', 'Honeybadger Error') }} {{ session('honeybadger_last_error') }}
    </div>
@endif