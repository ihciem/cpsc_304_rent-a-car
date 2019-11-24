<?php
function printResult($result) { //prints results from a select statement
    $header = false;

    echo "<table>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        $numKeys = array_filter(array_keys($row), function($numKey) {return is_int($numKey);});
        $assocKeys = array_filter(array_keys($row), function($assocKey) {return is_string($assocKey);});

        // output header/column/attribute names
        if (!$header) {
            echo "<thead><tr>";
            foreach ($assocKeys as $key) {
                echo '<th>' . ($key !== null ? htmlentities($key, ENT_QUOTES) : '') . str_repeat("&nbsp;", 5) . '</th>';
            }
            echo "</tr></thead>";
            $header = true;
        }

        // output all the data rows.
        echo '<tr>';
        foreach ($numKeys as $index) {
            echo "<td>" . $row[$index] . str_repeat("&nbsp;", 5) . "</td>";
        }
        echo '</tr>';
    }
    echo "</table>";
}