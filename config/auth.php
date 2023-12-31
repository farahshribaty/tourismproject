<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        // 'api' => [
        //     'driver' => 'passport',
        //     'provider' => 'users',            
        // ],

        //for user
        'user'=>[
            'driver'=>'session',
            'provider'=>'users',
            //'hash'=>false,
        ],
        'user-api'=>[
            'driver'=>'passport',
            'provider'=>'users',
        ],

        //for admins
        'admin'=>[
            'driver'=>'session',
            'provider'=>'admins',
        ],
        'admin-api'=>[
            'driver'=>'passport',
            'provider'=>'admins',
        ],

        //for doctor
//        'doctor' => [
//            'driver' => 'session',
//            'provider' => 'doctors',
//        ],
//        'doctor-api' => [
//            'driver' => 'passport',
//            'provider' => 'doctors',
//        ],

        //for hotel
        'hotel_admin'=>[
            'driver'=>'session',
            'provider'=>'hotel_admins',
        ],
        'hotel_admin-api'=>[
            'driver'=>'passport',
            'provider'=>'hotel_admins',
        ],

        //for airline
        'airline_admin'=>[
            'driver'=>'session',
            'provider'=>'airline_admins',
        ],
        'airline_admin-api'=>[
            'driver'=>'passport',
            'provider'=>'airline_admins',
        ],

        //for trip_company
        'trip_admin'=>[
            'driver'=>'session',
            'provider'=>'trip_admins',
        ],
        'trip_admin-api'=>[
            'driver'=>'passport',
            'provider'=>'trip_admins',
        ],

        //for attraction
        'attraction_admin'=>[
            'driver'=>'session',
            'provider'=>'attraction_admins',
        ],
        'attraction_admin-api'=>[
            'driver'=>'passport',
            'provider'=>'attraction_admins',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
//        'api'=>[
//            'driver'=>'passport',
//            'provider'=>'users',
//        ],

        //for admin
        'admins'=>[
            'driver'=>'eloquent',
            'model'=>App\Models\Admin::class,
        ],

        //for doctor
//        'doctors' => [
//            'driver' => 'eloquent',
//            'model' => App\Models\Doctor::class,
//        ],

        //for hotel
        'hotel_admins'=>[
            'driver'=>'eloquent',
            'model'=>App\Models\HotelAdmin::class,
        ],

        //for airline
        'airline_admins'=>[
            'driver'=>'eloquent',
            'model'=>App\Models\AirlineAdmin::class,
        ],

        //for trip_company
        'trip_admins'=>[
            'driver'=>'eloquent',
            'model'=>App\Models\TripAdmin::class,
        ],

        //for attraction
        'attraction_admins'=>[
            'driver'=>'eloquent',
            'model'=>App\Models\AttractionAdmin::class,
        ],

        // ____the end____


    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
