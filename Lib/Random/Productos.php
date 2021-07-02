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

use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Dinamic\Model\Variante;
use Faker;

/**
 * Description of Productos
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Productos extends NewItems
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
        $maxCost = $faker->numberBetween(1, 499);
        $maxPrice = $faker->numberBetween(1, 1999);

        for ($generated = 0; $generated < $number; $generated++) {
            $producto = new Producto();
            $producto->bloqueado = $faker->boolean(10);
            $producto->codfabricante = static::codfabricante();
            $producto->codfamilia = static::codfamilia();
            $producto->codimpuesto = static::codimpuesto();
            $producto->descripcion = $faker->paragraph;
            $producto->fechaalta = $faker->date();
            $producto->nostock = $faker->boolean(20);
            $producto->observaciones = $faker->optional()->text(500);
            $producto->publico = $faker->boolean();
            $producto->referencia = static::code(20);
            $producto->secompra = $faker->boolean(90);
            $producto->sevende = $faker->boolean(90);
            $producto->ventasinstock = $faker->boolean(30);

            if ($producto->exists()) {
                continue;
            }

            if (false === $producto->save()) {
                break;
            }

            foreach ($producto->getVariants() as $vari) {
                $vari->codbarras = $faker->optional(0.5)->ean13();
                $vari->coste = $faker->randomFloat(2, 0.1, $maxCost);
                $vari->margen = $faker->optional(0.2)->numberBetween(10, 100);
                $vari->precio = $faker->randomFloat(2, 0.1, $maxPrice);
                $vari->save();
            }

            $max = \mt_rand(-3, 9);
            while ($max > 0) {
                $newVar = new Variante();
                $newVar->codbarras = $faker->optional(0.5)->ean13();
                $newVar->coste = $faker->randomFloat(2, 0.1, $maxCost);
                $newVar->idproducto = $producto->idproducto;
                $newVar->margen = $faker->optional(0.2)->numberBetween(10, 100);
                $newVar->precio = $faker->randomFloat(2, 0.1, $maxPrice);
                $newVar->referencia = $faker->isbn13;
                $newVar->save();
                
                /// TODO: seleccionar atributos, si los hay

                $max--;
            }

            $producto->loadFromCode($producto->idproducto);
            $producto->actualizado = $faker->dateTime();
            $producto->save();
        }

        return $generated;
    }
}
