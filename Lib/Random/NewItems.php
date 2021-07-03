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
use FacturaScripts\Dinamic\Model\Agente;
use FacturaScripts\Dinamic\Model\Almacen;
use FacturaScripts\Dinamic\Model\Cliente;
use FacturaScripts\Dinamic\Model\Fabricante;
use FacturaScripts\Dinamic\Model\Familia;
use FacturaScripts\Dinamic\Model\FormaPago;
use FacturaScripts\Dinamic\Model\GrupoClientes;
use FacturaScripts\Dinamic\Model\Impuesto;
use FacturaScripts\Dinamic\Model\Pais;
use FacturaScripts\Dinamic\Model\Proveedor;
use FacturaScripts\Dinamic\Model\Serie;

/**
 * Description of NewItems
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
abstract class NewItems
{

    /**
     *
     * @var Agente[]
     */
    private static $agents = null;

    /**
     *
     * @var Pais[]
     */
    private static $countries = null;

    /**
     *
     * @var Cliente[]
     */
    private static $customers = null;

    /**
     *
     * @var GrupoClientes[]
     */
    private static $customerGroups = null;

    /**
     *
     * @var Familia[]
     */
    private static $families = null;

    /**
     *
     * @var Fabricante[]
     */
    private static $manufacturers = null;

    /**
     *
     * @var FormaPago[]
     */
    private static $payments = null;

    /**
     *
     * @var Serie[]
     */
    private static $series = null;

    /**
     *
     * @var Proveedor[]
     */
    private static $suppliers = null;

    /**
     *
     * @var Impuesto[]
     */
    private static $taxes = null;

    /**
     *
     * @var Almacen[]
     */
    private static $warehouses = null;

    /**
     *
     * @param int $number
     *
     * @return int
     */
    abstract public static function create(int $number = 50): int;

    /**
     *
     * @return float
     */
    protected static function cantidad()
    {
        $option = \mt_rand(0, 19);
        switch ($option) {
            default:
                return \mt_rand(0, 9);

            case 0:
                return \mt_rand(10, 99);

            case 1:
                return \mt_rand(100, 9999);

            case 2:
                return \mt_rand(-49, 0);

            case 3:
            case 4:
                return \mt_rand(100, 99999) / 1000;
        }
    }

    /**
     *
     * @return string
     */
    protected static function cifnif(): string
    {
        $number = \mb_substr(\str_shuffle('0123456789'), 0, \mt_rand(8, 9));
        $letter = \mb_substr(\str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
        $letter2 = \mb_substr(\str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);

        $separators = ['', ' ', '-', '.', ','];
        \shuffle($separators);

        switch (\mt_rand(0, 5)) {
            case 0:
                return '';

            case 1:
                return $number;

            case 2:
                return $letter . $separators[0] . $number;

            case 3:
                return $letter . $separators[0] . $number . $separators[0] . $letter2;

            default:
                return $number . $separators[0] . $letter;
        }
    }

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
     * @param int    $maxlen
     * @param string $use
     *
     * @return string
     */
    protected static function code(int $maxlen, string $use = '-_.'): string
    {
        $size = \mt_rand(1, $maxlen);
        $code = \mb_substr(\str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz0123456789' . $use), 0, $size);
        switch (\mt_rand(0, 5)) {
            case 0:
                return \mb_substr(\str_shuffle('0123456789012345678901234567890123456789' . $use), 0, $size);

            case 1:
                return \strtoupper($code);

            default:
                return $code;
        }

        return \mt_rand(0, 1) ? $code : \strtoupper($code);
    }

    /**
     *
     * @param int    $maxlen
     * @param string $use
     *
     * @return string
     */
    protected static function codeOrNull(int $maxlen, string $use = '-_.')
    {
        switch (\mt_rand(0, 5)) {
            case 0:
            case 1:
                return static::code($maxlen, $use);

            default:
                return null;
        }
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
     * @return string
     */
    protected static function codimpuesto()
    {
        if (null === static::$taxes) {
            $tax = new Impuesto();
            static::$taxes = $tax->all();
        }

        \shuffle(static::$taxes);
        return \mt_rand(0, 2) === 0 ? static::$taxes[0]->codimpuesto : AppSettings::get('default', 'codimpuesto');
    }

    /**
     *
     * @return string
     */
    protected static function codpais()
    {
        if (null === static::$countries) {
            $country = new Pais();
            static::$countries = $country->all();
        }

        \shuffle(static::$countries);
        return \mt_rand(0, 3) === 0 ? static::$countries[0]->codpais : AppSettings::get('default', 'codpais');
    }

    /**
     *
     * @return string
     */
    protected static function codpago()
    {
        if (null === static::$payments) {
            $payment = new FormaPago();
            static::$payments = $payment->all();
        }

        \shuffle(static::$payments);
        return \mt_rand(0, 1) === 0 ? static::$payments[0]->codpago : AppSettings::get('default', 'codpago');
    }

    /**
     *
     * @return string
     */
    protected static function codserie()
    {
        if (null === static::$series) {
            $serie = new Serie();
            static::$series = $serie->all();
        }

        \shuffle(static::$series);
        return \mt_rand(0, 1) === 0 ? static::$series[0]->codserie : AppSettings::get('default', 'codserie');
    }

    /**
     *
     * @return string
     */
    protected static function fecha(): string
    {
        $days = \mt_rand(0, 1999);
        return \date(Agente::DATE_STYLE, \strtotime('-' . $days . ' days'));
    }

    /**
     *
     * @return string
     */
    protected static function fechaHora(): string
    {
        $days = \mt_rand(0, 1999);
        return \date(Agente::DATETIME_STYLE, \strtotime('-' . $days . ' days'));
    }

    /**
     *
     * @return string
     */
    protected static function hora(): string
    {
        $minutes = \mt_rand(0, 1429);
        return \date(Agente::HOUR_STYLE, \strtotime('-' . $minutes . ' minutes'));
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
}
