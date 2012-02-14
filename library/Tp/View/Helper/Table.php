<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_View_Helper_Table extends Zend_View_Helper_Abstract
{
    public function table($data = array(), array $dataHeaders = null, $class = "valueTable", $noneText = "No Data available!") {
        if(empty($data)) {
            $output = $noneText;
        } else {
            $output = "<table class=\"{$class}\">";

            // Print Headers if available
            if($dataHeaders !== null) {
                $output .= '<tr class="head">';
                foreach($dataHeaders as $th) {
                    $output .= '<th>' . $th . '</th>';
                }
                $output .= '</tr>';
            }

            // Print Data
            $counter = 0;
            foreach($data as $row) {
                $evenOdd="even";
                if($counter % 2 === 0) {
                    $evenOdd="odd";
                }
                $output .= '<tr class="' . $evenOdd . '">';
                foreach($row as $td) {
                    $output .= '<td>' . $td . '</td>';
                }
                $output .= '</tr>';
                $counter++;
            }

            $output .= "</table>";
        }
        return $output;
    }
}
