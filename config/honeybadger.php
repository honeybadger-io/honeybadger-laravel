<?php

return [
    /**
     * Your project's Honeybadger API key. Get this by visiting the project settings from your dashboard at https://app.honeybadger.io.
     */
    'api_key' => env('HONEYBADGER_API_KEY'),

    /**
     * When reporting an exception, we'll automatically include relevant environment variables. Use this section to configure this.
     */
    'environment' => [
        /**
         * List of environment variables that should be filtered out when sending a report to Honeybadger.
         * By default, we filter out any passwords or access keys.
         */
        'filter' => [],

        /**
         * List of environment variables that should be included when sending a report to Honeybadger.
         */
        'include' => [],
    ],

    /**
     * We'll include request details when reporting an exception. Use this section to configure this.
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
     * The current version of your application. Use this to easily tie errors to specific releases and commits.
     * If you'd like to automatically use the Git commit hash as the version, set this to:
     * `trim(exec('git log --pretty="%h" -n1 HEAD'))`
     */
    'version' => env('APP_VERSION'),

    /**
     * The current hostname the app is running on.
     */
    'hostname' => gethostname(),

    /**
     * The root directory of the project.
     */
    'project_root' => base_path(),

    /**
     * The application environment.
     */
    'environment_name' => env('APP_ENV'),

    /**
     * Older PHP functions use the Error class, while modern PHP mostly uses Exception.
     * Specify if you'd like Honeybadger to report both types of errors.
     */
    'handlers' => [
        'exception' => true,
        'error' => true,
    ],

    /**
     * Customise the Guzzle client the Honeybadger SDK uses internally.
     * See https://docs.guzzlephp.org/en/stable/request-options.html for a description of each item,.
     */
    'client' => [
        'timeout' => 0,
        'proxy' => [],
        'verify' => env('HONEYBADGER_VERIFY_SSL', true),
    ],

    /**
     * Exception classes that should not be reported to Honeybadger.
     */
    'excluded_exceptions' => [],
];
