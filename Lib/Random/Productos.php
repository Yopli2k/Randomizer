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
 * @author Jose Antonio Cuello <yopli2000@gmail.com>
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
        $maxCost = \mt_rand(0, 4) > 0 ? $faker->numberBetween(1, 49) : $faker->numberBetween(1, 499);
        $maxPrice = \mt_rand(0, 4) > 0 ? $faker->numberBetween(1, 99) : $faker->numberBetween(1, 1999);

        static::dataBase()->beginTransaction();
        for ($generated = 0; $generated < $number; $generated++) {
            $product = static::getProduct($faker);
            if ($product->exists()) {
                continue;
            }

            if (false === $product->save()) {
                break;
            }

            foreach ($product->getVariants() as $variant) {
                static::setVariantData($faker, $variant, $maxCost, $maxPrice);
                $variant->save();
            }

            $max = \mt_rand(-3, 9);
            $withAttr = \mt_rand(0, 3) === 0;
            while ($max > 0) {
                $variant = static::getNewVariant($faker, $product->idproducto, $maxCost, $maxPrice);
                static::setVariantData($faker, $variant, $maxCost, $maxPrice);
                if ($withAttr) {
                    static::setVariantAttributes($variant);
                }
                $variant->save();
                $max--;
            }

            $product->loadFromCode($product->idproducto);
            $product->actualizado = $faker->dateTime();
            $product->save();
        }

        static::dataBase()->commit();
        return $generated;
    }

    /**
     *
     * @param Faker\Generator $faker
     *
     * @return Producto
     */
    private static function getProduct(&$faker)
    {
        $product = new Producto();
        $product->bloqueado = $faker->boolean(10);
        $product->codfabricante = static::codfabricante();
        $product->codfamilia = static::codfamilia();
        $product->codimpuesto = static::codimpuesto();
        $product->descripcion = $faker->paragraph;
        $product->fechaalta = $faker->date();
        $product->nostock = $faker->boolean(20);
        $product->observaciones = $faker->optional()->text(500);
        $product->publico = $faker->boolean();
        $product->referencia = static::code(20);
        $product->secompra = $faker->boolean(90);
        $product->sevende = $faker->boolean(90);
        $product->ventasinstock = $faker->boolean(30);
        return $product;
    }

    /**
     *
     * @param Faker\Generator $faker
     * @param int             $idproduct
     *
     * @return Variante
     */
    private static function getNewVariant(&$faker, $idproduct)
    {
        $newVar = new Variante();
        $newVar->idproducto = $idproduct;
        $newVar->referencia = $faker->isbn13;
        return $newVar;
    }

    /**
     * Assigns attribute values to a product variant.
     * Variants are assigned until a null is found or until a value is repeated
     * for an already defined attribute.
     *
     * @param Variante $variant
     */
    private static function setVariantAttributes(&$variant)
    {
        $attributes = [];
        for ($index = 1; $index < 5; $index++) {
            $value = static::atributo();
            if (null == $value || in_array($value->codatributo, $attributes)) {
                break;
            }
            $attributes[] = $value->codatributo;
            $variant->{'idatributovalor' . $index} = $value->id;
        }
    }

    /**
     *
     * @param Faker\Generator $faker
     * @param Variante        $variant
     * @param float           $maxCost
     * @param float           $maxPrice
     */
    private static function setVariantData(&$faker, &$variant, $maxCost, $maxPrice)
    {
        $variant->codbarras = $faker->optional(0.5)->ean13();
        $variant->coste = $faker->randomFloat(2, 0.1, $maxCost);
        $variant->margen = $faker->optional(0.2)->numberBetween(10, 100);
        $variant->precio = $faker->randomFloat(2, 0.1, $maxPrice);
    }
}
