<?php

namespace App\Providers;

use App\Education;
use App\Experience;
use App\Project;
use App\Volunteer;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Education::class  => 'App\Policies\VolunteerEducationPolicy',
        Experience::class => 'App\Policies\VolunteerExperiencePolicy',
        Project::class    => 'App\Policies\ProjectPolicy',
        Volunteer::class  => 'App\Policies\UserProfilePolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
