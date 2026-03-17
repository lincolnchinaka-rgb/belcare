<?php

return [
    'enable_pwa' => true,
    'install-toast-show' => true,
    
    'manifest' => [
        'name' => 'BEL-CARE MedAccess Navigator',
        'short_name' => 'BEL-CARE',
        'start_url' => '/',
        'theme_color' => '#2563eb',
        'background_color' => '#ffffff',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'description' => 'Bulawayo Emergency Healthcare Locator',
        'icons' => [
            [
                'src' => 'logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ],
        ],
    ],
    
    'livewire-app' => false,
];