<?php
/**
 * This file is part of Randomizer plugin for FacturaScripts
 * Copyright (C) 2016-2018 Carlos Garcia Gomez <carlos@facturascripts.com>
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
namespace FacturaScripts\Plugins\Randomizer\Lib\RandomDataGenerator;

use FacturaScripts\Dinamic\Model;

/**
 * Generate random accounts.
 * It may be better to incorporate the accounting plan of your country.
 *
 * @author Rafael San José      <info@rsanjoseo.com>
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 */
class Cuentas extends AbstractRandomAccounting
{

    /**
     * Generate random data.
     *
     * @param int $num
     *
     * @return int
     */
    public function generate($num = 50)
    {
        $cuenta = $this->model();

        // start transaction
        $this->dataBase->beginTransaction();

        // main save process
        try {
            for ($i = 0; $i < $num; ++$i) {
                $codigo = mt_rand(1000, 9990);
                $madre = floor($codigo / 10);

                $ejercicio = $this->getOneItem($this->ejercicios)->codejercicio;
                foreach ([$madre, $codigo] as $value) {
                    $cuenta->clear();
                    $cuenta->codejercicio = $ejercicio;
                    $cuenta->codcuenta = $value;
                    $cuenta->descripcion = $this->descripcion();
                    if (!$cuenta->save()) {
                        break;
                    }
                }
            }
            // confirm data
            $this->dataBase->commit();
        } catch (\Exception $e) {
            $this->miniLog->alert($e->getMessage());
        } finally {
            if ($this->dataBase->inTransaction()) {
                $this->dataBase->rollback();
            }
        }

        return $i;
    }

    /**
     * 
     * @return Model\Cuenta
     */
    protected function model()
    {
        return new Model\Cuenta();
    }
}
