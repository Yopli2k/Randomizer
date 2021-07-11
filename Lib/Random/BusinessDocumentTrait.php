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

use FacturaScripts\Dinamic\Lib\BusinessDocumentTools;
use Faker;

/**
 * Set of methods common to the different Business Documents.
 *
 * @author Jose Antonio Cuello  <yopli2000@gmail.com>
 */
trait BusinessDocumentTrait
{

    /**
     * Add a number of lines to the indicated document.
     *
     * @param Faker\Factory
     * @param BusinessDocument $document
     * @param int $numLines
     */
    protected static function createLines(&$faker, &$document, int $numLines = 1)
    {
        for ($line = 0; $line < $numLines; $line++) {
            $newLine = static::getNewLine($faker, $document);
            $newLine->cantidad = $faker->numberBetween(1, 99);
            $newLine->dtopor = $faker->optional(0.1, 0)->numberBetween(1, 90);
            if (false === $newLine->save()) {
                break;
            }
        }
    }

    /**
     *
     * @param BusinessDocument $document
     */
    protected static function recalculate(&$document)
    {
        $tool = new BusinessDocumentTools();
        $tool->recalculate($document);
        $document->save();
    }

    /**
     * @param Faker\Factory
     * @param BusinessDocument $document
     */
    private static function getNewLine(&$faker, &$document)
    {
        $reference = static::referencia();
        if (empty($reference)) {
            $newLine = $document->getNewLine();
            $newLine->descripcion = $faker->text();
            return $newLine;
        }

        return $document->getNewProductLine($reference);
    }
}
