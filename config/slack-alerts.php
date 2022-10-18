<?php

return [
    /*
     * The webhook URLs that we'll use to send a message to Slack.
     */
    'webhook_urls' => [
        'default' => env('SLACK_DEFAULT_ALERT_WEBHOOK'),
        'web-backend' => env('SLACK_BACKEND_ALERT_WEBHOOK'),
        'web-frontend' => env('SLACK_FRONTEND_ALERT_WEBHOOK'),
        'product-mgt' => env('SLACK_PRODUCT_MANAGEMENT_ALERT_WEBHOOK'),
        'tooling-docs-technical-writing' => env('SLACK_GENERAL_ALERT_WEBHOOK')
    ],

    /*
     * This job will send the message to Slack. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\SlackAlerts\Jobs\SendToSlackChannelJob::class,
];
