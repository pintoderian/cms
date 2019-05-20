<?php

//use UsersTableSeeder;
use App\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use App\Modules\Banpro\Database\Seeds\FincaSeeder;

class DatabaseSeeder extends Seeder
{
    private $seeders = [
        UsersTableSeeder::class,
        PermissionSeeder::class,
    ];
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed the default permissions
        $permissions = Permission::defaultPermissions();
        foreach ($permissions as $perms) {
            Permission::firstOrCreate(['name' => $perms]);
        }
        $this->command->info('Permisos predeterminados agregados.');
        // Confirm roles needed
        if ($this->command->confirm('Crear roles para el usuario, ¿predeterminado es admin y user? [y | N]', true)) {
            // Ask for roles from input
            $input_roles = $this->command->ask('Ingresar roles separados por coma.', 'admin,user');
            // Explode roles
            $roles_array = explode(',', $input_roles);
            // add roles
            foreach($roles_array as $role) {
                $role = Role::firstOrCreate(['name' => trim($role)]);
                if( $role->name == 'admin' ) {
                    // assign all permissions
                    $role->syncPermissions(Permission::all());
                    $this->command->info('Admin concedió todos los permisos');
                } else {
                    // for others by default only read access
                    $role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
                }
            }
            $this->command->info('Roles ' . $input_roles . ' agregado exitosamente');
        } else {
            Role::firstOrCreate(['name' => 'user']);
            $this->command->info('Se agregó solo el rol de usuario predeterminado.');
        }
        /**
         * Barra de progreso de los seeders
         */
        $seeders = $this->seeders;
        $this->command->info('Ejecutando Seeders de Laravel ...');
        $this->command->getOutput()->progressStart(count($seeders));
        foreach ($seeders as $seeder) {
            $this->call($seeder, true);
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();
    }
}
