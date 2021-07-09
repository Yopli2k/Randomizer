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

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Model\Almacen;
use FacturaScripts\Dinamic\Model\AtributoValor;
use FacturaScripts\Dinamic\Model\Fabricante;
use FacturaScripts\Dinamic\Model\Familia;
use FacturaScripts\Dinamic\Model\Variante;

/**
 * Set of methods for obtaining data derived from products
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 * @author Jose Antonio Cuello  <yopli2000@gmail.com>
 */
trait ProductosTrait
{

    /**
     *
     * @var AtributoValor[]
     */
    protected static $attributes = null;

    /**
     *
     * @var Familia[]
     */
    protected static $families = null;

    /**
     *
     * @var Fabricante[]
     */
    protected static $manufacturers = null;

    /**
     *
     * @var Variante[]
     */
    protected static $references = null;

    /**
     *
     * @var Almacen[]
     */
    protected static $warehouses = null;

    /**
     *
     * @return AtributoValor
     */
    protected static function atributo()
    {
        if (null === static::$attributes) {
            $attribute = new AtributoValor();
            static::$attributes = $attribute->all();
        }

        \shuffle(static::$attributes);
        return empty(static::$attributes) || \mt_rand(0, 2) === 0 ? null : static::$attributes[0];
    }

    /**
     *
     * @return string
     */
    protected static function codalmacen()
    {
        if (null === static::$warehouses) {
            $warehouse = new Almacen();
            static::$warehouses = $warehouse->all();
        }

        \shuffle(static::$warehouses);
        return \mt_rand(0, 2) === 0 ? static::$warehouses[0]->codalmacen : AppSettings::get('default', 'codalmacen');
    }

    /**
     *
     * @return string
     */
    protected static function codfabricante()
    {
        if (null === static::$manufacturers) {
            $manufacturer = new Fabricante();
            static::$manufacturers = $manufacturer->all();
        }

        \shuffle(static::$manufacturers);
        return empty(static::$manufacturers) || \mt_rand(0, 3) === 0 ? null : static::$manufacturers[0]->codfabricante;
    }

    /**
     *
     * @return string
     */
    protected static function codfamilia()
    {
        if (null === static::$families) {
            $family = new Familia();
            static::$families = $family->all();
        }

        \shuffle(static::$families);
        return empty(static::$families) || \mt_rand(0, 3) === 0 ? null : static::$families[0]->codfamilia;
    }

    /**
     *
     * @return string
     */
    protected static function referencia()
    {
        if (null === static::$references) {
            $reference = new Variante();
            static::$references = $reference->all();
        }

        \shuffle(static::$references);
        return empty(static::$references) || \mt_rand(0, 2) === 0 ? null : static::$references[0]->referencia;
    }
}
