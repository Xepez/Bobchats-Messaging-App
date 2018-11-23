<?php
    $dbconn = sqlite_open('../Bobchats.db');

    if ($dbconn) {
        $result = sqlite_query($dbconn,  "SELECT * FROM users");
        var_dump(sqlite_fetch_array($result, SQLITE_ASSOC));
    } else {
        print "Connection to database failed!\n";
    }
?>