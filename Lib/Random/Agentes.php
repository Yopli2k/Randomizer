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

use FacturaScripts\Dinamic\Model\Agente;
use Faker;

/**
 * Description of Agentes
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Agentes extends NewItems
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
            $agente = new Agente();
            $agente->cargo = $faker->optional()->jobTitle;
            $agente->cifnif = static::cifnif();
            $agente->codagente = static::codeOrNull(10);
            $agente->email = $faker->optional(0.8)->email;
            $agente->fechaalta = $faker->date();
            $agente->fechabaja = $faker->optional(0.1)->date();
            $agente->nombre = $faker->name();
            $agente->observaciones = $faker->optional(0.3)->paragraph();
            $agente->personafisica = $faker->boolean();
            $agente->telefono1 = $faker->optional()->phoneNumber;
            $agente->telefono2 = $faker->optional()->phoneNumber;

            if ($agente->exists()) {
                continue;
            }

            if (false === $agente->save()) {
                break;
            }

            /// TODO: mover a una nueva función
            $contact = $agente->getContact();
            $contact->apartado = $faker->optional(0.1)->postcode;
            $contact->ciudad = $faker->optional(0.7)->city;
            $contact->codpais = static::codpais();
            $contact->codpostal = $faker->optional()->postcode;
            $contact->direccion = $faker->optional()->address;
            $contact->provincia = $faker->optional()->state;
            $contact->save();
        }

        return $generated;
    }
}
