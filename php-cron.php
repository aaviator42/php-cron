<?php
// php-cron 
// v1.0 [2022-04-12]
// by @aaviator42

//cron job name (avoid spaces)
$cronName = "mycron_01";

//interval after which to run cronMaster() (in seconds)
$cronInterval = 30 * 60; //every 30 mins

//cronMaster: the actual cron job commands go here
function cronMaster(){
	
	//do some stuff
	file_put_contents("lorem.txt", "lorem ipsum dolor etc etc..." . PHP_EOL, FILE_APPEND);
	
	//write to log
	cronLog("We did some stuff @ " . time());
}




//-----------------------
//feel free to ignore everything below this line

//run this script indefinitely
ini_set('max_execution_time', 0);
ini_set('ignore_user_abort', 1);

//include DB library
require __DIR__ . '/StorX.php';

//cron status DB name
$dbName = __DIR__ . '/' . $cronName . ".cron.db";

header('Content-Type: text/plain');

echo "CRON: " . $cronName . PHP_EOL;
cronLog("START: " . time());
cronLog("TERMINATING OTHER INSTANCES");
cronStopOthers();
cronLog("SLEEP(10)");
sleep(10);
cronLog("OK NOW RESUME");
cronStopOthersReset();
$prevTime = time() - $cronInterval;

while(1){
	sleep(5);
	createDB();
	cronStop();

	if(time() - $prevTime < $cronInterval){
		echo "..." . PHP_EOL;
		continue;
	}

	cronLog("EXEC: " . time());
	cronMaster();
	updateDB();
	
	$prevTime = time();
	cronLog("SLEEP($cronInterval)");
}


//-----------------

function createDB(){
	//create cron status DB if it doesn't exist
	global $dbName;
	if(!file_exists($dbName)){
		$sx = new \StorX\Sx;
		$sx->createFile($dbName);
	}
}

function updateDB(){
	//update DB with last run time
	global $dbName;
	$sx = new \StorX\Sx;
	$sx->openFile($dbName, 1);
	$sx->modifyKey("lastrun", time());
	$sx->closeFile();
}

function cronStop(){
	//function to stop cron
	global $dbName;
	$sx = new \StorX\Sx;
	$sx->openFile($dbName, 1);
	if($sx->checkKey("cronstop") && $sx->returnKey("cronstop")){
		$sx->deleteKey("cronstop");
		$sx->modifyKey("lastrun", time());
		$sx->closeFile();
		cronLog("EXIT: " . time());
		exit(0);
	}
	$sx->closeFile();
}

function cronStopOthers(){
	//stop other instances of this cron job
	global $dbName;
	if(file_exists($dbName)){
		$sx = new \StorX\Sx;
		$sx->openFile($dbName, 1);
		$sx->modifyKey("cronstop", true);
		$sx->closeFile();
	}
}

function cronStopOthersReset(){
	//reset DB after stopping other instances of this cron job
	global $dbName;
	if(file_exists($dbName)){
		$sx = new \StorX\Sx;
		$sx->openFile($dbName, 1);
		if($sx->checkKey("cronstop") && $sx->returnKey("cronstop")){
			$sx->deleteKey("cronstop");
		}
		$sx->closeFile();
	}
}

function cronLog($status){
	//log status, and also print it
	global $cronName;
	$status .= PHP_EOL;
	$logFile = __DIR__ . '/' . $cronName . ".cron.log";
	file_put_contents($logFile, $status, FILE_APPEND);
	echo $status;
}