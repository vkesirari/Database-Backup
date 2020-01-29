
<?php
//for json to array and then insert record to mysql
$con = new mysqli("", "", "", "");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
//step 1
//add fieldname id in fieldname table
$sqltablename = "SELECT name FROM tablename where fieldname = 0000";
$restablename = $con->query($sqltablename);
$rowsfieldname = array();
$sqlCharg = "SELECT DISTINCT fieldname FROM tablename";
$resCharg = $con->query($sqlCharg);
while ($rowCharg = mysqli_fetch_array($resCharg, MYSQLI_ASSOC)) {
    while ($rowfieldname = mysqli_fetch_array($restablename, MYSQLI_ASSOC)) {
        $rowsfieldname[] = $rowfieldname['name'];
    }
    if (in_array($rowCharg['fieldname'], $rowsfieldname)) {
        echo $sql = "INSERT INTO tablename(fieldnameid,name) VALUES(0000,'" . $rowCharg['fieldname'] . "')"."<br>";
        $con->query($sql);
    }
}
//echo "New Record for fieldname table with fieldname id added successfully";

//step 2
//add fieldname id to charging table
$sqlCharg = "SELECT DISTINCT fieldname FROM tablename";
$resCharg = $con->query($sqlCharg);
$rowsCharg = array();
//for fieldname_id
$sqltablename = "SELECT id,name FROM tablename where fieldnameid = 0000";
$restablename = $con->query($sqltablename);
while ($rowfieldname = mysqli_fetch_array($restablename, MYSQLI_ASSOC)) {
    while ($rowCharg = mysqli_fetch_array($resCharg, MYSQLI_ASSOC)) {
        $rowsCharg[] = $rowCharg;
    }
    if (in_array($rowfieldname['name'], $rowsCharg)) {
        $sql = "UPDATE tablename SET fieldname_id = '" . $rowfieldname['id'] . "' WHERE fieldname = '" . $rowfieldname['name'] . "'";
        $con->query($sql);
    }
}
//echo "fieldname id's updated successfully";

//step 3
//add fieldname id to tablename table
$sqltablename = "SELECT fieldname_id,name,slug FROM tablename group by fieldname_id, name,slug";
$restablename = $con->query($sqltablename);
$sqlCharg = "SELECT fieldname,fieldname_id FROM tablename group by fieldname, fieldname_id";
$resCharg = $con->query($sqlCharg);
while ($rowCharg = mysqli_fetch_array($resCharg, MYSQLI_ASSOC)) {
    $res = mysqli_data_seek($restablename, 0);
    while ($rowfieldname = mysqli_fetch_array($restablename, MYSQLI_ASSOC)) {
        $counter = 0;
        if ($rowfieldname['name'] == $rowCharg['fieldname'] && $rowfieldname['fieldname_id'] == $rowCharg['fieldname_id']) {
            $counter = 1;
            break;
        }
    }
    if ($counter == 0) {
        $tempSlug = $rowCharg['fieldname'];
        $templower = strtolower($tempSlug);
        $slug = str_replace(" ", "-", $templower);
        $newslug = $slug;
        $count = 1;
        $unique = false;
        while(!$unique){
            $tempqry = "SELECT COUNT(slug) as count from tablename where slug = '$newslug' group by slug";
            $tempqryexec = $con->query($tempqry);
            $tempcount = mysqli_fetch_array($tempqryexec, MYSQLI_ASSOC);
            $temp = $tempcount['count'];
            if ($temp > 0) {
                    $newslug = $slug . "-" . $count;
                    $count++;
            }else{
                $unique = true;
            }
            
        }
        $sql = "INSERT INTO tablename(fieldnameid,fieldname_id,name,slug) VALUES(0000,'" . $rowCharg['fieldname_id'] . "','" . $rowCharg['fieldname'] . "','$newslug')";
        $con->query($sql);
       
    }
}
//echo "fieldname id's inseted successfully";

//step 4
//add fieldname id to charging table
$sqlCharg = "SELECT fieldname_id,fieldname FROM tablename group by fieldname_id,fieldname";
$resCharg = $con->query($sqlCharg);
$sqltablename = "SELECT * FROM tablename";
$restablename = $con->query($sqltablename);
while ($rowCharg = mysqli_fetch_array($resCharg, MYSQLI_ASSOC)) {
    mysqli_data_seek($restablename, 0);
    while ($rowfieldname = mysqli_fetch_array($restablename, MYSQLI_ASSOC)) {
        if ($rowCharg['fieldname'] == $rowfieldname['name'] && $rowCharg['fieldname_id'] == $rowfieldname['fieldname_id']) {
            $sql = "UPDATE tablename SET fieldname_id = '" . $rowfieldname['id'] . "' WHERE fieldname = '" . $rowfieldname['name'] . "' AND fieldname_id  = '" . $rowfieldname['fieldname_id'] . "'";
            $con->query($sql);
        }
    }
}
echo "fieldname id's updated successfully";

//step 5
//inserting all data from charging station to tablename table and updating the tag in tag entry table
//for inserting data to enties and entry tag
$sqlCharg = "SELECT * FROM tablename";
$resCharg = $con->query($sqlCharg);
while ($rowCharg = mysqli_fetch_array($resCharg, MYSQLI_ASSOC)) {
    //update fieldname_id
    $tempSlug = trim($rowCharg['title']);
    $templower = strtolower($tempSlug);
    $slug = str_replace(" ", "-", $templower);
    $newslug = $slug;
    $unique = false;
    $count = 1;
    while(!$unique){
        $tempqry = "SELECT COUNT(slug) as count from tablename where slug = '$newslug' group by slug";
        $tempqryexec = $con->query($tempqry);
        $tempcount = mysqli_fetch_array($tempqryexec, MYSQLI_ASSOC);
        $temp = $tempcount['count'];
        if ($temp > 0) {
             $newslug = $slug . "-" . $count;
             $count++;
        }else{
            $unique = true; 
        }  
    }
    //i have inserted user_id 1235
    $sqltablename = "INSERT INTO tablename(user_id,fieldname_id,name,slug,address,lat,lng) VALUES(1235,'" . $rowCharg['fieldname_id'] . "','" . trim($rowCharg['title']) . "','$newslug','" . $rowCharg['address'] . "','" . $rowCharg['lat'] . "','" . $rowCharg['lng'] . "')";
    $con->query($sqltablename);
    $new_id = $con->insert_id;
    $sqlTags = "INSERT INTO fieldname(entry_id,tag_id) VALUES('$new_id',140)";
    $con->query($sqlTags);
}
echo "Record updated";
?>
