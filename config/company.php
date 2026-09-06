<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Profile & Multi-Company Configuration
    |--------------------------------------------------------------------------
    |
    | These values are loaded from the .env file to support multiple company
    | brandings, titles, descriptions, headers, footers, logos, and carpet
    | numbering conventions without modifying application code.
    |
    */

    'name' => env('COMPANY_NAME', 'شرکت صنعتی برادران قاسمی'),
    'description' => env('COMPANY_DESCRIPTION', 'تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان'),
    'logo_path' => env('COMPANY_LOGO_PATH', 'images/logos/qasimi_logo.png'),
    'header_path' => env('COMPANY_HEADER_PATH', 'images/logos/qasimi_header.png'),
    'footer_path' => env('COMPANY_FOOTER_PATH', 'images/logos/qasimi_footer.png'),
    
    /*
    |--------------------------------------------------------------------------
    | Carpet Numbering Settings
    |--------------------------------------------------------------------------
    */
    'carpet_no_prefix' => env('CARPET_NO_PREFIX', 'QB'),
    'carpet_no_start' => env('CARPET_NO_START', 'QB1000'),
];
