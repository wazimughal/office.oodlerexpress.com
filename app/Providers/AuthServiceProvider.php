<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;
use App\Models\adminpanel\Users;
use App\Models\User;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    // protected $policies = [
    //      'App\Models\Organizations' => 'App\Policies\adminpanel\organizationsPolicy',
    // ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        
        $this->registerPolicies();
            
            Gate::define('isAdmin', function($user) {
                return $user->group_id ==config('constants.groups.admin');
            });

            Gate::define('isHOD', function($user) {
                
                return $user->group_id ==config('constants.groups.hod');
            });

            Gate::define('isStaff', function($user) {
                return $user->group_id ==config('constants.groups.staff');
            });
            Gate::define('isSubscriber', function($user) {
                return $user->group_id ==config('constants.groups.subscriber');
            });

            // Admin OR HOD of Department
            Gate::define('adminORhod', function($user) {
                if($user->group_id == config('constants.groups.admin') || $user->group_id ==config('constants.groups.hod'))
                return true;
            });
        //
    }
}
