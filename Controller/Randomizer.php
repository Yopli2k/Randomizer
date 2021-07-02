<?php
/**
 * This file is part of Randomizer plugin for FacturaScripts
 * Copyright (C) 2017-2021 Carlos Garcia Gomez <carlos@facturascripts.com>
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
namespace FacturaScripts\Plugins\Randomizer\Controller;

use FacturaScripts\Core\Base;
use FacturaScripts\Core\Model\User;
use FacturaScripts\Plugins\Randomizer\Lib\Random;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to generate random data
 *
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 * @author Rafael San José      <info@rsanjoseo.com>
 */
class Randomizer extends Base\Controller
{

    /**
     * Contains the total quantity for each model.
     *
     * @var array
     */
    public $totalCounter = [];

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['menu'] = 'admin';
        $pageData['title'] = 'generate-test-data';
        $pageData['icon'] = 'fas fa-flask';
        return $pageData;
    }

    /**
     * Runs the controller's private logic.
     *
     * @param Response                   $response
     * @param User                       $user
     * @param Base\ControllerPermissions $permissions
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);

        $option = $this->request->get('gen', '');
        if ($option !== '') {
            $this->execAction($option);
            $this->redirect($this->url() . '?gen=' . $option, 5);
        }

        $this->getTotals();
    }

    /**
     * Executes selected action.
     *
     * @param string $option
     */
    private function execAction($option)
    {
        switch ($option) {
            case 'agentes':
                return $this->generateAction('generated-agents', Random\Agentes::create());

            case 'albaranescli':
                return $this->generateAction('generated-customer-delivery-notes', Random\AlbaranesClientes::create());

            case 'albaranesprov':
                return $this->generateAction('generated-supplier-delivery-notes', Random\AlbaranesProveedores::create());

            case 'clientes':
                return $this->generateAction('generated-customers', Random\Clientes::create());

            case 'contactos':
                return $this->generateAction('generated-contacts', Random\Contactos::create());

            case 'fabricantes':
                return $this->generateAction('generated-manufacturers', Random\Fabricantes::create());

            case 'familias':
                return $this->generateAction('generated-families', Random\Familias::create());

            case 'grupos':
                return $this->generateAction('generated-customer-groups', Random\GruposClientes::create());

            case 'pedidoscli':
                return $this->generateAction('generated-customer-orders', Random\PedidosClientes::create());

            case 'pedidosprov':
                return $this->generateAction('generated-supplier-orders', Random\PedidosProveedores::create());

            case 'presupuestoscli':
                return $this->generateAction('generated-customer-estimations', Random\PresupuestosClientes::create());

            case 'presupuestosprov':
                return $this->generateAction('generated-supplier-estimations', Random\PresupuestoProveedores::create());

            case 'productos':
                return $this->generateAction('generated-products', Random\Productos::create());

            case 'proveedores':
                return $this->generateAction('generated-supplier', Random\Proveedores::create());

            case 'proyectos':
                return $this->generateAction('generated-projects', Random\Proyectos::create());

            case 'servicios':
                return $this->generateAction('generated-services', Random\Servicios::create());

            case 'users':
                return $this->generateAction('generated-users', Random\Usuarios::create());
        }
        
        /// TODO: crear atributos y valores, comisiones, tarifas, transportistas, empresas y almacenes
    }

    /**
     * 
     * @param string $label
     * @param int    $number
     */
    private function generateAction(string $label, int $number)
    {
        $this->toolBox()->i18nLog()->notice($label, ['%quantity%' => $number]);
        $this->toolBox()->i18nLog()->notice('randomizer-generating-more-items');
    }

    /**
     * Set totalCounter key for each model.
     */
    private function getTotals()
    {
        $models = [
            'agentes' => 'FacturaScripts\\Dinamic\\Model\\Agente',
            'albaranescli' => 'FacturaScripts\\Dinamic\\Model\\AlbaranCliente',
            'albaranesprov' => 'FacturaScripts\\Dinamic\\Model\\AlbaranProveedor',
            'asientos' => 'FacturaScripts\\Dinamic\\Model\\Asiento',
            'clientes' => 'FacturaScripts\\Dinamic\\Model\\Cliente',
            'contactos' => 'FacturaScripts\\Dinamic\\Model\\Contacto',
            'cuentas' => 'FacturaScripts\\Dinamic\\Model\\Cuenta',
            'fabricantes' => 'FacturaScripts\\Dinamic\\Model\\Fabricante',
            'familias' => 'FacturaScripts\\Dinamic\\Model\\Familia',
            'grupos' => 'FacturaScripts\\Dinamic\\Model\\GrupoClientes',
            'pedidoscli' => 'FacturaScripts\\Dinamic\\Model\\PedidoCliente',
            'pedidosprov' => 'FacturaScripts\\Dinamic\\Model\\PedidoProveedor',
            'presupuestoscli' => 'FacturaScripts\\Dinamic\\Model\\PresupuestoCliente',
            'presupuestosprov' => 'FacturaScripts\\Dinamic\\Model\\PresupuestoProveedor',
            'productos' => 'FacturaScripts\\Dinamic\\Model\\Producto',
            'proveedores' => 'FacturaScripts\\Dinamic\\Model\\Proveedor',
            'subcuentas' => 'FacturaScripts\\Dinamic\\Model\\Subcuenta',
            'users' => 'FacturaScripts\\Dinamic\\Model\\User'
        ];

        foreach ($models as $tag => $modelName) {
            if (false === \class_exists($modelName)) {
                $this->totalCounter[$tag] = 0;
                continue;
            }

            $model = new $modelName();
            $this->totalCounter[$tag] = $model->count();
        }
    }
}
