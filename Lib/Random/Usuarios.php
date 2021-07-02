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
     * @param int $number
     *
     * @return int
     */
    public static function create(int $number = 50): int
    {
        $faker = Faker\Factory::create('es_ES');

        for ($generated = 0; $generated < $number; $generated++) {
            $user = new User();
            $user->admin = $faker->boolean(5);
            $user->codagente = static::codagente();
            $user->codalmacen = static::codalmacen();
            $user->creationdate = $faker->date();
            $user->email = $user->nick = $faker->email;
            $user->enabled = $faker->boolean(90);
            $user->lastactivity = $faker->date();
            $user->lastip = $faker->optional()->ipv4;
            $user->newPassword = $user->newPassword2 = $faker->password();
            
            /// TODO: asignar un rol, si no es administrador

            if ($user->exists()) {
                continue;
            }

            if (false === $user->save()) {
                break;
            }
        }

        return $generated;
    }
}
