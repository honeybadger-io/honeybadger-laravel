@if(session('honeybadger_last_error'))
    <div class="{{ $class }}">
        <small>{{ $text }} {{ session('honeybadger_last_error') }}</small>
    </div>
@endif