<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();

        Passport::tokensCan([
            'user'=>'User',
            'admin'=>'Admin',
            //'doctor' => 'Doctor',
            'hotel_admin'=>'HotelAdmin',
            'airline_admin'=>'AirlineAdmin',
            'trip_admin'=>'TripAdmin',
            'attraction_admin'=>'AttractionAdmin',
        ]);

        Passport::tokensExpireIn(Carbon::now()->addDays(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));
    }
}
