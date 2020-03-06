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

use FacturaScripts\Dinamic\Model;
use FacturaScripts\Dinamic\Lib\BusinessDocumentTools;

/**
 * Abstract class that contains the methods that generate random documents
 * for clients and suppliers, such as orders, delivery notes and invoices. 
 *
 * @author Rafael San José      <info@rsanjoseo.com>
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 */
abstract class AbstractRandomDocuments extends AbstractRandomPeople
{

    /**
     * List of warehouses.
     *
     * @var Model\Almacen[]
     */
    protected $almacenes;

    /**
     * List of currencies.
     *
     * @var Model\Divisa[]
     */
    protected $divisas;

    /**
     *
     * @var BusinessDocumentTools
     */
    protected $docTools;

    /**
     * List of payment methods.
     *
     * @var Model\FormaPago[]
     */
    protected $formasPago;

    /**
     * List of series.
     *
     * @var Model\Serie[]
     */
    protected $series = [];

    /**
     * AbstractRandomDocuments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->docTools = new BusinessDocumentTools();

        $this->shuffle($this->almacenes, new Model\Almacen());
        $this->shuffle($this->divisas, new Model\Divisa());
        $this->shuffle($this->formasPago, new Model\FormaPago());

        /// exclude serie for rectified invoices
        $codserierec = $this->toolBox()->appSettings()->get('default', 'codserierec');
        $serieModel = new Model\Serie();
        foreach ($serieModel->all() as $serie) {
            if ($serie->codserie != $codserierec) {
                $this->series[] = $serie;
            }
        }
    }

    /**
     * Generates a random document
     *
     * @param Model\Base\BusinessDocument $doc
     */
    protected function randomizeDocument(&$doc)
    {
        $doc->codagente = mt_rand(0, 4) && !empty($this->agentes) ? $this->agentes[0]->codagente : null;
        $doc->codalmacen = (mt_rand(0, 4) == 0) ? $this->almacenes[0]->codalmacen : $doc->codalmacen;

        $doc->coddivisa = (mt_rand(0, 4) == 0) ? $this->divisas[0]->coddivisa : $doc->coddivisa;
        foreach ($this->divisas as $div) {
            if ($div->coddivisa == $doc->coddivisa) {
                $doc->tasaconv = $div->tasaconv;
                break;
            }
        }

        $doc->codpago = $this->formasPago[0]->codpago;
        $doc->codserie = mt_rand(0, 4) == 0 ? $this->series[0]->codserie : $doc->codserie;
        if (mt_rand(0, 4) == 0) {
            $doc->observaciones = $this->observaciones();
        }

        if (isset($doc->numero2) && mt_rand(0, 4) == 0) {
            $doc->numero2 = mt_rand(10, 99999);
        } elseif (isset($doc->numproveedor) && mt_rand(0, 4) == 0) {
            $doc->numproveedor = mt_rand(10, 99999);
        }

        $doc->setDate($this->fecha(), $this->hora());
    }

    /**
     * Generates random document lines
     *
     * @param Model\Base\BusinessDocument $doc
     */
    protected function randomLineas(&$doc)
    {
        $productos = $this->randomProductos();

        /// 1 out of 5 times we use negative quantities
        $modcantidad = (mt_rand(0, 9) == 0) ? -1 : 1;

        $numlineas = (int) $this->cantidad(0, 10, 200);
        while ($numlineas > 0) {
            if (isset($productos[$numlineas]) && $productos[$numlineas]->sevende) {
                $lin = $doc->getNewProductLine($productos[$numlineas]->referencia);
            } else {
                $lin = $doc->getNewLine();
                $lin->descripcion = $this->descripcion();
                $lin->pvpunitario = $this->precio(1, 49, 699);
            }

            $lin->cantidad = $modcantidad * $this->cantidad(1, 3, 19);
            if (mt_rand(0, 4) == 0) {
                $lin->dtopor = $this->cantidad(0, 33, 100);
            }

            if (mt_rand(0, 49) == 0) {
                $lin->suplido = true;
            }

            $lin->save();
            --$numlineas;
        }

        /// recalculate
        $this->docTools->recalculate($doc);
        $doc->save();
    }
}
