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

use FacturaScripts\Dinamic\Model\Proveedor;
use Faker;

/**
 * Description of Proveedores
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Proveedores extends NewItems
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
            $proveedor = new Proveedor();
            $proveedor->acreedor = $faker->boolean();
            $proveedor->cifnif = static::cifnif();
            $proveedor->codpago = static::codpago();
            $proveedor->codretencion = static::codretencion();
            $proveedor->codserie = static::codserie();
            $proveedor->email = $faker->optional()->email;
            $proveedor->fax = $faker->optional(0.1)->phoneNumber;
            $proveedor->fechaalta = $faker->date();
            $proveedor->fechabaja = $faker->optional(0.1)->date();
            $proveedor->nombre = $faker->name();
            $proveedor->observaciones = $faker->optional()->paragraph();
            $proveedor->personafisica = $faker->boolean();
            $proveedor->razonsocial = $faker->optional()->company;
            $proveedor->regimeniva = static::regimenIVA();
            $proveedor->telefono1 = $faker->optional()->phoneNumber;
            $proveedor->telefono2 = $faker->optional()->phoneNumber;
            $proveedor->web = $faker->optional()->url;

            if ($proveedor->exists()) {
                continue;
            }

            if (false === $proveedor->save()) {
                break;
            }

            /// TODO: crear direcciones (contactos)
            /// TODO: crear cuentas bancarias
        }

        return $generated;
    }
}
