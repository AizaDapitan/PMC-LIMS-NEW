<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private $group = array(
        // Role
        array(
            'interface' => 'App\Repositories\Interfaces\RoleRepositoryInterface',
            'repository' => 'App\Repositories\RoleRepository',
            'service' => 'App\Services\RoleService',
            'model' => [
                'App\Models\Role',
            ],
        ),
        // Dept User
        array(
            'interface' => 'App\Repositories\Interfaces\DeptUserRepositoryInterface',
            'repository' => 'App\Repositories\DeptUserRepository',
            'service' => 'App\Services\DeptUserService',
            'model' => [
                'App\Models\DeptUser',
            ],
        ),

        // Dept Officer
        array(
            'interface' => 'App\Repositories\Interfaces\DeptOfficerRepositoryInterface',
            'repository' => 'App\Repositories\DeptOfficerRepository',
            'service' => 'App\Services\DeptOfficerService',
            'model' => [
                'App\Models\DeptOfficer',
            ],
        ),      
         // users
         array(
            'interface' => 'App\Repositories\Interfaces\UserRepositoryInterface',
            'repository' => 'App\Repositories\UserRepository',
            'service' => 'App\Services\UserService',
            'model' => [
                'App\Models\User'
            ],
        ),
        // Access Rights
        array(
            'interface' => 'App\Repositories\Interfaces\AccessRightRepositoryInterface',
            'repository' => 'App\Repositories\AccessRightRepository',
            'service' => 'App\Services\AccessRightService',
            'model' => [
                'App\Models\UsersPermissions',
                'App\Models\RolesPermissions'
            ],
        ),    
        // Audit
        array(
            'interface' => 'App\Repositories\Interfaces\AuditRepositoryInterface',
            'repository' => 'App\Repositories\AuditRepository',
            'service' => 'App\Services\AuditService',
            'model' => [
                '\OwenIt\Auditing\Models\Audit',
            ],
        ),
         // Report
         array(
            'interface' => 'App\Repositories\Interfaces\ReportRepositoryInterface',
            'repository' => 'App\Repositories\ReportRepository',
            'service' => 'App\Services\ReportService',
            'model' => [
                '\OwenIt\Auditing\Models\Audit',
            ],
        ),     
    );
    public function register()
    {
        foreach ($this->group as $key => $item) {
            $this->app->bind($item['interface'], function ($app) use ($item) {
                if (is_array($item['model'])) {
                    $models = [];
                    foreach ($item['model'] as $model) {
                        $models[] = new $model();
                    }
                    return new $item['repository'](...$models);
                } else {
                    return new $item['repository'](new $item['model']());
                }
            });
            $this->app->bind($item['service'], function ($app) use ($item) {
                return new $item['service'](
                    $app->make($item['interface'])
                );
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
