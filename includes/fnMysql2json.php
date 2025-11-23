<?php

    function cbMysql2json($sql, $col = 0)
    {
        /*  Takes input of a SQL query and runs it on the         */
        /*  default SQL connection.  Returns a string formatted   */
        /*  as ["val1", "val2", "val3"] where each value is the   */
        /*  value from a different row in the query result.       */

        $Results = array();
        $R = mysql_query($sql);
        while ($row = mysql_fetch_array($R)){
            $Results[] = $row[$col];
        }
        return json_encode($Results);
    }

?>