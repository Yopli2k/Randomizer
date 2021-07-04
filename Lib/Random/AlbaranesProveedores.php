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

use FacturaScripts\Dinamic\Model\AlbaranProveedor;
use FacturaScripts\Plugins\Randomizer\Lib\Random\BusinessDocumentTrait;
use Faker;

/**
 * Description of AlbaranesProveedores
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class AlbaranesProveedores extends NewItems
{

    use BusinessDocumentTrait;

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
            $doc = new AlbaranProveedor();
            $doc->setSubject(static::proveedor());
            $doc->codalmacen = static::codalmacen();
            $doc->codpago = static::codpago();
            $doc->codserie = static::codserie();
            $doc->dtopor1 = $faker->optional(0.1)->numberBetween(1, 90);
            $doc->dtopor2 = $faker->optional(0.1)->numberBetween(1, 90);
            $doc->fecha = static::fecha();
            $doc->hora = static::hora();
            $doc->observaciones = $faker->optional()->text();

            if ($doc->exists()) {
                continue;
            }

            if (false === $doc->save()) {
                var_dump($doc);
                break;
            }

            static::createLines($faker, $doc, $faker->numberBetween(1, 9));
            static::recalculate($doc);
        }

        return $generated;
    }
}
