@if(auth()->user() && session('honeybadger_last_error'))
    <style>
        #honeybadger_feedback { padding-left: 2em; padding-right: 2em; min-width: 80%;  color: #9CA3AF; }
        #honeybadger_feedback_form *, #honeybadger_feedback_form *:before, #honeybadger_feedback_form *:after { -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; }
        #honeybadger_feedback_form h2 { font-size: 110%; line-height: 1.5em; }
        #honeybadger_feedback_form label { font-weight: bold; }
        #honeybadger_feedback_name, #honeybadger_feedback_email, #honeybadger_feedback_comment { width: 100%; padding: 0.5em; }
        #honeybadger_feedback_comment { height: 10em; }
        #honeybadger_feedback_submit { color: initial; }
        #honeybadger_feedback_form .honeybadger-feedback-phone { display: none; }
        #honeybadger_feedback_link { text-decoration: underline; }
    </style>

    <script>
        function honeybadgerFeedbackResponse(data) {
            if (data['result'] == 'OK') {
                var form = document.getElementById('honeybadger_feedback_form');
                var success = document.getElementById('honeybadger_feedback_success');

                form.style.display = 'none';
                success.style.display = 'block';
            } else {
                var message;

                if (data['error']) {
                    message = data['error'];
                } else {
                    message = 'An unknown error occurred. Please try again.';
                }

                alert(message);
            }
        }

        function sendHoneybadgerFeedback() {
            try {
                var script = document.createElement('script');
                var form = document.getElementById('honeybadger_feedback_form');
                script.src = '{{ $action }}?format=js&token={{ session('honeybadger_last_error') }}&name=' + encodeURIComponent(form.name.value) + '&email=' + encodeURIComponent(form.email.value) + '&comment=' + encodeURIComponent(form.comment.value);
                form.appendChild(script);
                return false;
            } catch(e) {
                if (window.console) {
                    console.log('Error caught while processing Honeybadger feedback form: ' + e);
                    console.log('Submitting form normally...');
                }
                return true;
            }
        }
    </script>

    <div id="honeybadger_feedback">
    <div id="honeybadger_feedback_success" style="display:none;">
        <p><strong>{{ __('honeybadger::feedback.thanks') }}</strong></p>
    </div>

    <form action="{{ $action }}" method="POST" id="honeybadger_feedback_form" onsubmit="return sendHoneybadgerFeedback();">
        <input type="hidden" name="token" id="honeybadger_feedback_token" value="{{ session('honeybadger_last_error') }}">

        <h2>{{ __('honeybadger::feedback.heading') }}</h2>
        <p>{{ __('honeybadger::feedback.explanation') }}</p>

        <p class="honeybadger-feedback-name">
            <label for="honeybadger_feedback_name">{{ __('honeybadger::feedback.labels.name') }}</label><br>
            <input type="text" name="name" id="honeybadger_feedback_name" size="60">
        </p>

        <p class="honeybadger-feedback-phone">
            <label for="honeybadger_feedback_phone">{{ __('honeybadger::feedback.labels.phone') }}</label><br>
            <input type="text" name="phone" id="honeybadger_feedback_phone" size="60">
        </p>

        <p class="honeybadger-feedback-email">
            <label for="honeybadger_feedback_email">{{ __('honeybadger::feedback.labels.email') }}</label><br>
            <input type="email" name="email" id="honeybadger_feedback_email" value="{{ auth()->user()->email ?? '' }}">
        </p>

        <p class="honeybadger-feedback-comment">
            <label for="honeybadger_feedback_comment">{{ __('honeybadger::feedback.labels.comment') }}</label><br>
            <textarea name="comment" id="honeybadger_feedback_comment" cols="50" rows="6" required></textarea>
        </p>

        <p class="honeybadger-feedback-submit">
            <input type="submit" id="honeybadger_feedback_submit" value="{{ __('honeybadger::feedback.submit') }}">
        </p>
    </form>

    <p><a id="honeybadger_feedback_link" href="https://www.honeybadger.io/" target="_blank" title="Exception, uptime, and performance monitoring for PHP.">Powered by Honeybadger</a></p>
    </div>
@endif