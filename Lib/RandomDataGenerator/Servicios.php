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

use FacturaScripts\Dinamic\Model\Servicio;

/**
 * Generates delivery notes to customers with random data.
 *
 * @author Rafael San José      <info@rsanjoseo.com>
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 * @author Luis Miguel Pérez    <luismi@pcrednet.com>
 */
class Servicios extends AbstractRandomDocuments
{

    /**
     * Generate random data.
     *
     * @param int $num
     *
     * @return int
     */
    public function generate($num = 40)
    {
        $clientes = $this->randomClientes();
        if (empty($clientes)) {
            return 0;
        }

        $generated = 0;
        $serv = $this->model();

        // start transaction
        $this->dataBase->beginTransaction();

        // main save process
        try {
            while ($generated < $num) {
                $serv->clear();

                $cliente = $this->getOneItem($clientes);
                $this->randomizeDocument($serv, $cliente);
                $serv->material = $this->material();
                $serv->material_estado = $this->material_estado();
                $serv->accesorios = $this->accesorios();
                $serv->descripcion = $this->descripcion();
                if (!$serv->save()) {
                    break;
                }

                $this->randomLineas($serv);
                ++$generated;
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
     * @return AlbaranCliente
     */
    protected function model()
    {
        return new \FacturaScripts\Plugins\Services\Model\Servicio();
    }
    
     
    /**
     * Returns random articles
     *
     * @return string
     */
    public function material()
    {
        $material = [
            'Ordenador', 'Lavadora', 'Televisor.',
            'Suegros', 'Portátil', 'tablet',
            'Movil'
        ];

        /// Add a lot of Blas as an option
        $bla = 'Bla';
        while (mt_rand(0, 29) > 0) {
            $bla .= ', bla';
        }
        $material[] = $bla . '.';

        return $this->getOneItem($material);
    }
    
    /**
     * Returns random product-status
     *
     * @return string
     */
    public function material_estado()
    {
        $material_estado = [
            'roto', 'sucio', 'limpio.',
            'con rayas', 'feo', 'cristal roto',
            'mal estado'
        ];

        /// Add a lot of Blas as an option
        $bla = 'Bla';
        while (mt_rand(0, 29) > 0) {
            $bla .= ', bla';
        }
        $material_estado[] = $bla . '.';

        return $this->getOneItem($material_estado);
    }
    
    /**
     * Returns random accesories
     *
     * @return string
     */
    public function accesorios()
    {
        $accesorios = [
            'bolso', 'cargador', 'raton.',
            'ropa sucia', 'cascos', 'maletin rosa',
            'silla verde'
        ];

        /// Add a lot of Blas as an option
        $bla = 'Bla';
        while (mt_rand(0, 29) > 0) {
            $bla .= ', bla';
        }
        $accesorios[] = $bla . '.';

        return $this->getOneItem($accesorios);
    }
    
    /**
     * Returns random problem-description
     *
     * @return string
     */
    public function descripcion()
    {
        $descripcion = [
            'no enciende', 'no lava la ropa bien', 'no se ven canales porno',
            'se queman las tostadas', 'hace ruido cuando encendie', 'no lo sabe el cliente',
            'me lo ha tirado y se ha ido'
        ];

        /// Add a lot of Blas as an option
        $bla = 'Bla';
        while (mt_rand(0, 29) > 0) {
            $bla .= ', bla';
        }
        $descripcion[] = $bla . '.';

        return $this->getOneItem($descripcion);
    }
}
