<?php

use Honeybadger\BulkEventDispatcher;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLaravel;

return [
    /**
     * Your project's Honeybadger API key. Get this from the project settings on your Honeybadger dashboard.
     */
    'api_key' => env('HONEYBADGER_API_KEY'),

    /**
     * Your personal authentication token. Get this from authentication tab in your User Settings page.
     */
    'personal_auth_token' => env('HONEYBADGER_PERSONAL_AUTH_TOKEN'),

    /**
     * The endpoint for the Honeybadger API.
     * If you are using the EU region, set this to 'https://eu-api.honeybadger.io'.
     */
    'endpoint' => env('HONEYBADGER_ENDPOINT', 'https://api.honeybadger.io'),

    /**
     * The endpoint for the Honeybadger App.
     * This is used to synchronize check-ins with Honeybadger.
     * If you are using the EU region, set this to 'https://eu-app.honeybadger.io'.
     */
    'app_endpoint' => env('HONEYBADGER_APP_ENDPOINT', 'https://app.honeybadger.io'),

    /**
     * The application environment.
     */
    'environment_name' => env('APP_ENV'),

    /**
     * To disable exception reporting, set this to false.
     */
    'report_data' => ! in_array(env('APP_ENV'), ['local', 'testing']),

    /**
     * When reporting an exception, we'll automatically include relevant environment variables.
     * See the Environment Whitelist (https://docs.honeybadger.io/lib/php/reference/configuration.html#environment-whitelist) for details.
     * Use this section to filter or include env variables.
     */
    'environment' => [
        /**
         * List of environment variables that should be filtered out when sending a report to Honeybadger.
         */
        'filter' => [
            // "QUERY_STRING",
        ],

        /**
         * List of environment variables that should be included when sending a report to Honeybadger.
         */
        'include' => [
            // "APP_DEBUG"
        ],
    ],

    /**
     * We include details of the request when reporting an exception. Use this section to configure this.
     */
    'request' => [
        /**
         * Fields in the request body that should be filtered out.
         * By default, we filter out any fields named similarly to "password" or "token", but you can add more.
         */
        'filter' => [
            // "credit_card_number",
        ],
    ],

    /**
     * The current version of your application. Use this to easily tie errors to specific releases or commits.
     * If you'd like to automatically use the Git commit hash as the version, set this to:
     *   'version' => trim(exec('git log --pretty="%h" -n1 HEAD')).
     */
    'version' => env('APP_VERSION'),

    /**
     * The hostname of the machine the app is running on.
     */
    'hostname' => gethostname(),

    /**
     * The root directory of your project.
     */
    'project_root' => base_path(),

    /**
     * Older PHP functions use the Error class, while modern PHP mostly uses Exception.
     * Specify if you'd like Honeybadger to report both types of errors.
     * The shutdown handler is required flushing any remaining events that were queued using Honeybadger.event().
     */
    'handlers' => [
        'exception' => true,
        'error' => true,
        'shutdown' => true,
    ],

    /**
     * Customise the Guzzle client the Honeybadger SDK uses internally.
     * See https://docs.guzzlephp.org/en/stable/request-options.html for a description of each item,.
     */
    'client' => [
        'timeout' => 15,
        'proxy' => [],
        'verify' => env('HONEYBADGER_VERIFY_SSL', true),
    ],

    /**
     * Enable reporting deprecation warnings.
     */
    'capture_deprecations' => false,

    /**
     * Exception classes that should not be reported to Honeybadger.
     */
    'excluded_exceptions' => [],

    'breadcrumbs' => [
        /**
         * Enable recording of breadcrumbs (application events).
         * Setting this to false will disable automatic breadcrumbs and the addBreadcrumb() function.
         */
        'enabled' => true,

        /**
         * Events which should automatically be recorded by the Honeybadger client as breadcrumbs.
         * Note that to track redis events, you need to call `Redis::enableEvents()` in your app.
         */
        'automatic' => HoneybadgerLaravel::DEFAULT_EVENTS,
    ],

    /**
     * Define your checkins here and synchronize them to Honeybadger with the
     * honeybadger:checkins:sync artisan command.
     * The recommended approach is to run this command as part of your deploy process.
     * Learn more about checkins at https://docs.honeybadger.io/api/reporting-check-ins/.
     */
    'checkins' => [],

    'events' => [
        /**
         * Enable sending application events to Honeybadger Insights.
         * Setting this to false will disable automatic events collection and the event() function.
         */
        'enabled' => false,

        /**
         * The number of events to queue before sending them to Honeybadger.
         */
        'bulk_threshold' => BulkEventDispatcher::BULK_THRESHOLD,

        /**
         * The number of seconds to wait before sending queued events to Honeybadger.
         */
        'dispatch_interval_seconds' => BulkEventDispatcher::DISPATCH_INTERVAL_SECONDS,

        /**
         * Events which should automatically be sent to Honeybadger Insights.
         * Note that to track redis events, you need to call `Redis::enableEvents()` in your app.
         */
        'automatic' => HoneybadgerLaravel::DEFAULT_EVENTS,
    ],
];
