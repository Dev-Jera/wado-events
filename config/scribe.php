<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;

return [

    'title' => 'WADO Events Tickets — API & Endpoint Reference',

    'description' => 'Complete reference for all WADO Events Tickets endpoints: authentication, event browsing, ticket checkout, gate scanning, walk-in sales, payment webhooks, and admin operations.',

    'intro_text' => <<<'INTRO'
        This documentation covers every HTTP endpoint in the WADO Events Tickets platform.

        **Authentication:** Most endpoints require an active browser session (cookie-based). Log in at `/login` first, then use the Try It Out buttons — your session carries over automatically.

        **Webhook endpoints** (e.g. MarzPay) are called by external payment providers, not by the browser.

        **Admin endpoints** require the `super_admin` or `admin` role.

        **Gate portal / scan endpoints** require the `gate_agent` role assigned to the relevant event.
    INTRO,

    'base_url' => config('app.url'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['*'],
                'domains'  => ['*'],
            ],
            'include' => [],
            'exclude' => [
                // Filament admin panel (has its own UI)
                'dashboard/*',
                // Dev tools
                'telescope/*',
                'horizon/*',
                // Internal/debug routes
                'outbound-ip',
                // PDF/email preview pages (admin HTML views)
                'admin/email-preview/*',
                'admin/pdf-preview/*',
                // Reverb WebSocket internal routes
                'laravel-websockets*',
                'broadcasting/*',
            ],
        ],
    ],

    'type'  => 'laravel',
    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes'       => true,
        'docs_url'         => '/docs',
        'assets_directory' => null,
        // Restrict docs to authenticated admins in production
        'middleware'       => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => null,
        // Uses browser session cookie — log in at /login first
        'use_csrf'  => true,
        'csrf_url'  => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled' => true,
        'default' => true,
        'in'      => AuthIn::HEADER->value,
        'name'    => 'Cookie',
        'use_value'   => null,
        'placeholder' => '{YOUR_SESSION_COOKIE}',
        'extra_info'  => 'This API uses session-based authentication. Log in at <a href="/login">/login</a> first — your browser session cookie is sent automatically when using Try It Out.',
    ],

    'example_languages' => [
        'bash',
        'javascript',
        'php',
    ],

    'postman' => [
        'enabled'   => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
    ],

    'openapi' => [
        'enabled'    => true,
        'version'    => '3.0.3',
        'overrides'  => [
            'info.version' => '1.0.0',
        ],
        'generators' => [],
    ],

    'groups' => [
        'default' => 'General',
        'order'   => [
            'Authentication',
            'Events',
            'Checkout',
            'Tickets',
            'Gate Portal',
            'Ticket Verification',
            'Admin — Payments',
            'Webhooks',
            'General',
        ],
    ],

    'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y} ({git:short})',

    'examples' => [
        'faker_seed'    => 1234,
        'models_source' => ['factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ]),
        ],
        'urlParameters'  => [...Defaults::URL_PARAMETERS_STRATEGIES],
        'queryParameters' => [...Defaults::QUERY_PARAMETERS_STRATEGIES],
        'bodyParameters' => [...Defaults::BODY_PARAMETERS_STRATEGIES],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: [
                    'app.debug' => false,
                ]
            )
        ),
        'responseFields' => [...Defaults::RESPONSE_FIELDS_STRATEGIES],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],
];
