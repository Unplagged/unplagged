<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of Barcode
 *
 * @author benjamin
 */
class Unplagged_Barcode {

    private $width;
    private $height;
    private $initWidth;
    private $barHeight;
    private $showLabels;

    /**
     * - 0: Seite nicht vorhanden oder kein Plagiat
     * - 1: nicht einberechnte Seiten
     * - 2: Seite enthählt Plagiat
     * - 3: mehr als 50% der Seite plagiert
     * - 4: mehr als 75% der Seute plagiert
     */
    private $colors = array('0' => '#ffffff', '1' => '#bfd9f8', '2' => '#000000', '3' => '#920005', '4' => '#f80012');
    private $pages = array();
    private $result;
    private $x = 0;
    private $y = 0;
    private $widthUnit;

    public function __construct($width = 100, $height = 200, $barHeight = 100, $showLabels = true, $widthUnit = '%', $data) {
     /*   $data = array();
        for ($i = 1; $i < 200; $i++) {
            $disabled = ($i < 10 || $i > 180) ? true : false;
            $data[] = array('pageNumber' => $i, 'plagPercentage' => rand(0, 100), 'disabled' => $disabled);
        }
        */
        $this->widthUnit = $widthUnit;
        $this->width = $width;
        $this->height = $height;
        $this->showLabels = $showLabels;
        $this->barHeight = $barHeight;

        $prevPageNumber = 1;

        foreach ($data as $page) {
            $color = null;

            // whenever there is a gap between the last and the current page, add the pages as missing pages
            if ($prevPageNumber + 1 != $page['pageNumber']) {
                for ($i = $prevPageNumber + 1; $i < $page['pageNumber']; $i++) {
                    $this->pages[$i] = 0;
                }
            }

            // page should not be in the report
            if ($page['disabled'] == 'true') {
                $color = 1;
                // page has more than 75% of plagiarism
            } elseif ($page['plagPercentage'] > 75) {
                $color = 4;
                // page has more than 50% of plagiarism
            } elseif ($page['plagPercentage'] > 50) {
                $color = 3;
                // page has plagiarism
            } elseif ($page['plagPercentage'] > 0) {
                $color = 2;
            } else {
                $color = 0;
            }
            $this->pages[$page['pageNumber']] = $color;

            $prevPageNumber = $page['pageNumber'];
        }

        $this->initWidth = count($this->pages) > 0 ? $this->width * 1.0 / count($this->pages) : 0;
    }

    public function render() {
        $this->result = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="width: ' . $this->width . $this->widthUnit . '; height: ' . $this->height . 'px;">' . "\n";
        if (!empty($this->pages)) {
            if ($this->showLabels) {
                $this->y += 15;

                $this->result .= '<line x1="0" y1="' . $this->y . '" x2="' . $this->width . $this->widthUnit . '" y2="' . $this->y . '" style="stroke: #000000"/>';
                $this->y += 5;
            }

            $this->generateBars();

            if ($this->showLabels) {
                $this->y += $this->barHeight + 5;
                $this->result .= '<line x1="0" y1="' . $this->y . '" x2="' . $this->width . $this->widthUnit . '" y2="' . $this->y . '" style="stroke: #000000"/>';

                $this->y += 20;
                $this->generateAxis();
            }
        }
        $this->result .= '</svg>';

        return $this->result;
    }

    private function generateBars() {
        $x = 0;
        $barWidth = 0;
        $next = null;
        while (false !== $next) {
            $color = current($this->pages);
            $next = next($this->pages);

            $barWidth += $this->initWidth;
            if ($color != $next) {
                $this->result .= '<rect x="' . $x . $this->widthUnit . '" y="' . $this->y . '" width="' . $barWidth . $this->widthUnit . '" height="' . $this->barHeight . '" style="fill:' . $this->colors[$color] . ';"/>' . "\n";

                $x += $barWidth;
                $barWidth = 0;
            }
        }
    }

    private function generateAxis() {
        $labelStepsize = 10;

        while (true) {
            $count = sizeof($this->pages);
            $labelCount = floor($count / $labelStepsize);

            // we assume a label needs 45px and 500px is the width that needs to be displayable without crossovers
            if (45 * $labelCount > 500) {
                $labelStepsize += 10;
                continue;
            }
            break;
        }

        $label = $labelStepsize;
        $x = 0;
        while ($x < ($this->width - ($this->initWidth * $labelStepsize))) {
            $x += ($this->initWidth * $labelStepsize);
            $this->result .= '<text x="' . $x . $this->widthUnit . '" y="' . $this->y . '" font-family="Arial" font-size="14" text-anchor="middle">' . $label . '</text>';
            $this->result .= '<line x1="' . $x . $this->widthUnit . '" y1="' . ($this->y - 20) . '" x2="' . $x . $this->widthUnit . '" y2="' . ($this->y - 15) . '" stroke-width="1" stroke="#000000"></line>';

            $label += $labelStepsize;
        }
    }

}

?>
