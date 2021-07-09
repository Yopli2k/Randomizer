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

use FacturaScripts\Dinamic\Lib\RegimenIVA;
use FacturaScripts\Dinamic\Model\Agente;
use FacturaScripts\Dinamic\Model\Cliente;
use FacturaScripts\Dinamic\Model\GrupoClientes;
use FacturaScripts\Dinamic\Model\Proveedor;

/**
 * Set of methods for obtaining data derived from comercials contacts
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
trait ComercialContactTrait
{

    /**
     *
     * @var Agente[]
     */
    protected static $agents = null;

    /**
     *
     * @var Cliente[]
     */
    protected static $customers = null;

    /**
     *
     * @var GrupoClientes[]
     */
    protected static $customerGroups = null;

    /**
     *
     * @var Proveedor[]
     */
    protected static $suppliers = null;

    /**
     *
     * @return Cliente
     */
    protected static function cliente()
    {
        if (null === static::$customers) {
            $customer = new Cliente();
            static::$customers = $customer->all();
        }

        \shuffle(static::$customers);
        return empty(static::$customers) ? new Cliente() : static::$customers[0];
    }

    /**
     *
     * @return string
     */
    protected static function codagente()
    {
        if (null === static::$agents) {
            $agent = new Agente();
            static::$agents = $agent->all();
        }

        \shuffle(static::$agents);
        return empty(static::$agents) || \mt_rand(0, 3) === 0 ? null : static::$agents[0]->codagente;
    }

    /**
     *
     * @return string
     */
    protected static function codgrupo()
    {
        if (null === static::$customerGroups) {
            $customerGroup = new GrupoClientes();
            static::$customerGroups = $customerGroup->all();
        }

        \shuffle(static::$customerGroups);
        return empty(static::$customerGroups) || \mt_rand(0, 2) === 0 ? null : static::$customerGroups[0]->codgrupo;
    }

    /**
     *
     * @return Proveedor
     */
    protected static function proveedor()
    {
        if (null === static::$suppliers) {
            $supplier = new Proveedor();
            static::$suppliers = $supplier->all();
        }

        \shuffle(static::$suppliers);
        return empty(static::$suppliers) ? new Proveedor() : static::$suppliers[0];
    }

    /**
     *
     * @return string
     */
    protected static function regimenIVA()
    {
        $values = RegimenIVA::all();
        \shuffle($values);
        return array_key_first($values);
    }
}
