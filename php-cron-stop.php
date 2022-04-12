<?php
// php-cron (terminator script)
// v1.0 [2022-04-12]
// by @aaviator42

//cron job name
$cronName = "mycron_01";



//-----------------------
//feel free to ignore everything below this line

//include DB library
require __DIR__ . '/StorX.php';

//cron status DB name
$dbName = __DIR__ . '/' . $cronName . ".cron.db";


$sx = new \StorX\Sx;
$sx->openFile($dbName, 1);
$sx->setTimeout(360 * 1000);
$sx->modifyKey("cronstop", true);
$sx->closeFile();

header('Content-Type: text/plain');

echo "Terminating cron: $cronName";