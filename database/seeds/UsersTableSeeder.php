<?php

use App\User;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Core\UsersController;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = new UsersController();
        /**
         * Crear administrador
         */
        $user = $users->create_admin();
        /**
         * ----------------------------------
         */
        $this->command->info('Se creo usuario administrador: root@codegea.com');
        $user->assignRole('admin');
        $this->command->info('Se le asigno todos los permisos');
    }
}
