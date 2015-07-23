<?php

/*
 * @param $header : $name => $width
*/
function generate_table($header, $rows) {

    echo '<table>';

    echo '<thead><tr>';
    foreach ($header as $name => $width) echo '<th width="' . $width . '">' . $name . '</th>';
    echo '</tr></thead>';

    echo '<tbody>';
    foreach ($rows as $row) {
        echo "<tr>";
        foreach (get_object_vars($row) as $cell) {
            echo "<td>" . $cell . "</td>";
        }
        echo "</tr>";
    }
    echo '</tbody></table>';
}
