<?php
/**
 * This file is part of Randomizer plugin for FacturaScripts
 * Copyright (C) 2021 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\Randomizer\Lib\Random;

use FacturaScripts\Dinamic\Model\User;
use FacturaScripts\Dinamic\Model\Role;
use Faker;

/**
 * Description of Usuarios
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Usuarios extends NewItems
{

    /**
     *
     * @var Role[]
     */
    private $roles = [];

    /**
     *
     * @param int $number
     *
     * @return int
     */
    public static function create(int $number = 50): int
    {
        $faker = Faker\Factory::create('es_ES');
        $this->roles = $this->loadRoles();

        for ($generated = 0; $generated < $number; $generated++) {
            $user = new User();
            $user->nick = $faker->email;
            if ($user->exists()) {
                continue;
            }

            $user->admin = $faker->boolean(5);
            $user->codagente = static::codagente();
            $user->codalmacen = static::codalmacen();
            $user->creationdate = $faker->date();
            $user->email = $user->nick;
            $user->enabled = $faker->boolean(90);
            $user->lastactivity = $faker->date();
            $user->lastip = $faker->optional()->ipv4;
            $user->newPassword = $user->newPassword2 = $faker->password();

            if (false == $user->admin) {
                $this->setRol($user);
            }

            if (false === $user->save()) {
                break;
            }
        }

        return $generated;
    }

    /**
     *
     * @return Role[]
     */
    private function loadRoles()
    {
        $roleModel = new Role();
        return $roleModel->all();
    }

    private function setRol(&$user)
    {
        if (empty($this->roles)) {
            return;
        }

        shuffle($this->roles);

        $roleUser = new RoleUser();
        $roleUser->codrole = $this->roles[0]->codrole;
        $roleUser->nick = $user->nick;
        $roleUser->save();
    }
}
