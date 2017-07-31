<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
    ],

    'guards' => [
        'api' => ['driver' => 'api'],
		'provider'=>'users'
    ],

	'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
            'table' => 'users',
        ],
	],

];
