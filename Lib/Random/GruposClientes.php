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

use FacturaScripts\Dinamic\Model\GrupoClientes;
use FacturaScripts\Dinamic\Model\Tarifa;
use Faker;

/**
 * Description of GruposClientes
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class GruposClientes extends NewItems
{

    /**
     *
     * @var Tarifa[]
     */
    private $rates = [];

    /**
     *
     * @param int $number
     *
     * @return int
     */
    public static function create(int $number = 50): int
    {
        $faker = Faker\Factory::create('es_ES');
        $this->rates = $this->loadRates();

        for ($generated = 0; $generated < $number; $generated++) {
            $grupo = new GrupoClientes();
            $grupo->codgrupo = static::codeOrNull(6);
            if ($grupo->exists()) {
                continue;
            }

            $grupo->nombre = $faker->word();
            $this->setRate($grupo);

            if (false === $grupo->save()) {
                break;
            }
        }

        return $generated;
    }

    private function loadRates()
    {
        $rateModel = new Tarifa();
        return $rateModel->all();
    }

    /**
     *
     * @param GrupoClientes $group
     */
    private function setRate(&$group)
    {
        if (empty($this->rates)) {
            return;
        }

        shuffle($this->rates);
        $group->codtarifa = $this->rates[0]->codtarifa;
    }
}
