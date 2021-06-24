@if(session('honeybadger_last_error'))
    <div class="{{ $class }}">
        {{ $text }} {{ session('honeybadger_last_error') }}
    </div>
@endif