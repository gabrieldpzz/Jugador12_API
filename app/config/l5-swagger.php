<?php

return [

    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Jugador12 API',
            ],

            'routes' => [
                // Ruta de la UI de Swagger
                'api' => 'api/documentation',
            ],

            'paths' => [
                // Usar rutas absolutas para assets en la UI
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                // Carpeta de assets de Swagger UI (publicados por vendor:publish)
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                // Nombres de archivos generados
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',

                // Formato que usa la UI
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                // Directorios que se escanean para anotaciones @OA\*
                'annotations' => [
                    base_path('app'),
                    base_path('routes'),
                ],
            ],
        ],
    ],

    'defaults' => [

        'routes' => [
            // Ruta para servir el JSON/YAML ya generado
            'docs' => 'docs',

            // Callback OAuth2 (no lo usarás, pero se deja por compatibilidad)
            'oauth2_callback' => 'api/oauth2-callback',

            // Middlewares de las rutas de la UI/docs/assets
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            'group_options' => [],
        ],

        'paths' => [
            // Carpeta donde se guardan los JSON/YAML generados
            'docs' => storage_path('api-docs'),

            // Carpeta de vistas publicadas (si publicaste las views de L5-Swagger)
            'views' => base_path('resources/views/vendor/l5-swagger'),

            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',

            // Base path opcional (normalmente null)
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            // Directorios a excluir (deprecated – usa scanOptions.exclude)
            'excludes' => [],

            // Directorios a escanear (redundante pero recomendado mantenerlo también aquí)
            'annotations' => [
                base_path('app'),
                base_path('routes'),
            ],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [
                // Config extra para processors si lo necesitas
            ],
            'analyser' => null,
            'analysis' => null,
            'processors' => [
                // p.ej: custom processors
            ],
            'pattern' => null,

            // Directorios a excluir del scan
            'exclude' => [],

            // Versión del spec OpenAPI a generar (3.0 por defecto)
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        // **Deja vacío** para NO mostrar el botón "Authorize"
        'securityDefinitions' => [
            'securitySchemes' => [],
            'security' => [],
        ],

        // Regenerar siempre (mejor false en prod)
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

        // Generar copia YAML
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        // Proxy (si usas LB que reescribe host)
        'proxy' => false,

        // Configs plugin (normalmente null)
        'additional_config_url' => null,

        // Orden de operaciones (alpha/method/null)
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        // Deshabilita validación externa
        'validator_url' => null,

        // Parámetros de la UI
        'ui' => [
            // Abre tags por defecto: 'none' | 'list' | 'full'
            'docExpansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'list'),

            // Oculta el panel "Schemas" (=-1)
            'defaultModelsExpandDepth' => env('L5_SWAGGER_UI_DEFAULT_MODELS_EXPAND_DEPTH', -1),

            'deepLinking' => env('L5_SWAGGER_UI_DEEP_LINKING', true),
            'displayRequestDuration' => env('L5_SWAGGER_UI_DISPLAY_REQUEST_DURATION', true),

            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        // Constantes utilizables en anotaciones (si quieres)
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost'),
        ],
    ],
];
