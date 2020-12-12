<?php
/**
 * This file is part of Randomizer plugin for FacturaScripts
 * Copyright (C) 2016-2020 Carlos Garcia Gomez <carlos@facturascripts.com>
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

use FacturaScripts\Dinamic\Model\AlbaranCliente;

/**
 * Generates delivery notes to customers with random data.
 *
 * @author Rafael San José      <info@rsanjoseo.com>
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 */
class AlbaranesCliente extends AbstractRandomDocuments
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
        $clientes = $this->randomClientes();
        if (empty($clientes)) {
            return 0;
        }

        $generated = 0;

        // start transaction
        $this->dataBase->beginTransaction();

        // main save process
        try {
            while ($generated < $num) {
                $cliente = $this->getOneItem($clientes);

                $alb = $this->model();
                $alb->setSubject($cliente);
                $this->randomizeDocument($alb);

                switch (\mt_rand(0, 2)) {
                    case 0:
                        $alb->numero2 = $this->toolBox()->utils()->randomString();
                        break;

                    case 1:
                        $alb->numero2 = \mt_rand(1, 99999999);
                        break;
                }

                if (false === $alb->save()) {
                    break;
                }

                $this->randomLineas($alb);
                ++$generated;
            }

            // confirm data
            $this->dataBase->commit();
        } catch (\Exception $e) {
            $this->toolBox()->log()->error($e->getMessage());
        } finally {
            if ($this->dataBase->inTransaction()) {
                $this->dataBase->rollback();
            }
        }

        return $generated;
    }

    /**
     * 
     * @return AlbaranCliente
     */
    protected function model()
    {
        return new AlbaranCliente();
    }
}
