<?php
$host = 'hostname';
$username = 'username';
$password = 'password';
$database_name = 'database_name';
$conn = mysqli_connect($host,$username,$password,$database_name);
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
$conn->set_charset("utf8");
// Get All Table Names From the Database
$tables = array();
$sql = "SHOW TABLES";
$sqlScript = "";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}
    foreach ($tables as $table) {
        // Prepare SQLscript for creating table structure
        $query = "SHOW CREATE TABLE $table";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
        $columnCount = mysqli_num_fields($result);
        // Prepare SQLscript for dumping data for each table
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"';
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        $sqlScript .= "\n"; 
    }

if(!empty($sqlScript)){
    // Save the SQL script to a backup file
    $backup_file_name = $database_name  .'_'. date("y-m-d") . '.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    $number_of_lines = fwrite($fileHandler, $sqlScript);
    //print_r($number_of_lines);die;
    fclose($fileHandler); 
    //converting file to zip
    $zip = new ZipArchive;
    if ($zip->open($backup_file_name .'.zip', ZipArchive::CREATE) === TRUE){
        $zip->addFile($backup_file_name);
        $zip->close();
    }
    //deleting backup older than last 7 days
    $days = 7 * 84600;
    if ($handle = opendir('.')) {
        while (false !== ($zipfiles = readdir($handle))) {
            if ($zipfiles != "." && $zipfiles != "..") {
                if(time() - filemtime($zipfiles) > $days){
                    unlink( $zipfiles );  
                }
            }
        }
        closedir($handle);
    }
    }
?>
