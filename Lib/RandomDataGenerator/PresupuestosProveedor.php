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

use FacturaScripts\Dinamic\Model\PresupuestoProveedor;

/**
 *  Generates delivery notes to suppliers with random data.
 *
 * @author Francesc Pineda Segarra  <francesc.pineda.segarra@gmail.com>
 * @author Rafael San José Tovar    <rsanjoseo@gmail.com>
 * @author Carlos García Gómez      <carlos@facturascripts.com>
 */
class PresupuestosProveedor extends AbstractRandomDocuments
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
        $proveedores = $this->randomProveedores();
        if (empty($proveedores)) {
            return 0;
        }

        $generated = 0;
        $pre = $this->model();

        // start transaction
        $this->dataBase->beginTransaction();

        // main save process
        try {
            while ($generated < $num) {
                $pre->clear();

                $proveedor = $this->getOneItem($proveedores);
                $this->randomizeDocument($pre, false, $proveedor);
                if ($pre->save()) {
                    $this->randomLineas($pre);
                    ++$generated;
                } else {
                    break;
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

        return $generated;
    }

    /**
     * 
     * @return PresupuestoProveedor
     */
    protected function model()
    {
        return new PresupuestoProveedor();
    }
}
