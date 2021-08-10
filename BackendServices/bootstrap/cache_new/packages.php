<?php return array (
  'facade/ignition' => 
  array (
    'providers' => 
    array (
      0 => 'Facade\\Ignition\\IgnitionServiceProvider',
    ),
    'aliases' => 
    array (
      'Flare' => 'Facade\\Ignition\\Facades\\Flare',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'joggapp/laravel-google-translate' => 
  array (
    'providers' => 
    array (
      0 => 'JoggApp\\GoogleTranslate\\GoogleTranslateServiceProvider',
    ),
    'aliases' => 
    array (
      'GoogleTranslate' => 'JoggApp\\GoogleTranslate\\GoogleTranslateFacade',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'linecorp/line-bot-sdk' => 
  array (
    'providers' => 
    array (
      0 => 'LINE\\Laravel\\LINEBotServiceProvider',
    ),
    'aliases' => 
    array (
      'LINEBot' => 'LINE\\Laravel\\Facade\\LINEBot',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'reliese/laravel' => 
  array (
    'providers' => 
    array (
      0 => 'Reliese\\Coders\\CodersServiceProvider',
    ),
  ),
  'tanmuhittin/laravel-google-translate' => 
  array (
    'providers' => 
    array (
      0 => 'Tanmuhittin\\LaravelGoogleTranslate\\LaravelGoogleTranslateServiceProvider',
    ),
  ),
  'tymon/jwt-auth' => 
  array (
    'aliases' => 
    array (
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'Tymon\\JWTAuth\\Facades\\JWTFactory',
    ),
    'providers' => 
    array (
      0 => 'Tymon\\JWTAuth\\Providers\\LaravelServiceProvider',
    ),
  ),
);