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

use FacturaScripts\Dinamic\Lib\BusinessDocumentTools;
use FacturaScripts\Dinamic\Model\PresupuestoCliente;
use Faker;

/**
 * Description of PresupuestosClientes
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class PresupuestosClientes extends NewItems
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
            $doc = new PresupuestoCliente();
            $doc->setSubject(static::cliente());
            $doc->codagente = static::codagente();
            $doc->codalmacen = static::codalmacen();
            $doc->codpago = static::codpago();
            $doc->codserie = static::codserie();
            $doc->dtopor1 = $faker->optional(0.1)->numberBetween(1, 90);
            $doc->dtopor2 = $faker->optional(0.1)->numberBetween(1, 90);
            $doc->fecha = static::fecha();
            $doc->hora = static::hora();
            $doc->numero2 = $faker->optional()->isbn10;
            $doc->observaciones = $faker->optional()->text();

            if ($doc->exists()) {
                continue;
            }

            if (false === $doc->save()) {
                break;
            }

            $freelines = \mt_rand(1, 200);
            while ($freelines > 0) {
                $newline = $doc->getNewLine();
                $newline->cantidad = static::cantidad();
                $newline->codimpuesto = static::codimpuesto();
                $newline->descripcion = $faker->text();
                $newline->dtopor = $faker->optional(0.2, 0)->numberBetween(0, 99);
                $newline->pvpunitario = $faker->randomFloat(2, 0, 1999);
                $newline->save();

                $freelines--;
            }

            $tool = new BusinessDocumentTools();
            $tool->recalculate($doc);
            $doc->save();
        }

        return $generated;
    }
}
