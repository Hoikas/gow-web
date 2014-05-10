<?php
require './ds-config.ini.php';
$csv = array();
$ucsv = array(); // doh!

$pq = pg_connect("host={$dbhost} port={$dbport} dbname={$dbname} user={$dbuser} password={$dbpass}");

// Grab all of the currently active players.
$aQuery = pg_query('SELECT "PlayerIdx" FROM auth."Players" ORDER BY "PlayerIdx"')
          or die(pg_errormessage());

// This makes our *still active* player assoc
while ($row = pg_fetch_row($aQuery)) {
    // Grab the CreateTime for the Player node...
    $tQuery = pg_query('SELECT "CreateTime" FROM vault."Nodes" WHERE idx='.$row[0])
              or die(pg_errormessage());
    $tResult = pg_fetch_row($tQuery)[0];
    $time = date('m/Y', $tResult);
    pg_free_result($tQuery);

    // add to assoc
    if (array_key_exists($time, $csv)) {
        $csv[$time] += 1;
    } else {
        $csv[$time] = 1;
    }
}

// Now, let's find unique accounts.
$uQuery = pg_query('SELECT * FROM (SELECT DISTINCT ON ("Uuid_1") "CreateTime" FROM vault."Nodes" WHERE "NodeType"=2) hack ORDER BY "CreateTime"')
          or die(pg_errormessage());
while ($row = pg_fetch_row($uQuery)) {
    $time = date('m/Y', $row[0]);

    // add to assoc
    if (array_key_exists($time, $ucsv)) {
        $ucsv[$time] += 1;
    } else {
        $ucsv[$time] = 1;
    }
}

// shut down postgres
pg_free_result($aQuery);
pg_free_result($uQuery);
pg_close($pq);

// blah...
echo('new avatars/month<br />');
$keys = array_keys($csv);
foreach ($keys as $month) {
    echo("{$month},{$csv[$month]}<br />");
}
echo('<br /><br />');

echo('total over months<br />');
$total = 0;
foreach ($keys as $month) {
    $total += $csv[$month];
    echo("{$month},{$total}<br />");
}
echo('<br /><br />');

// blah...
echo('new unique users/month<br />');
$keys = array_keys($ucsv);
foreach ($keys as $month) {
    echo("{$month},{$ucsv[$month]}<br />");
}
echo('<br /><br />');

echo('total unique over months<br />');
$total = 0;
foreach ($keys as $month) {
    $total += $ucsv[$month];
    echo("{$month},{$total}<br />");
}
?>