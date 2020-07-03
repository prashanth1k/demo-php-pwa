<?php

// DB details - MySQL
// $dbHost = 'localhost';
// $dbUsername = 'root';
// $dbPassword = '';
// $dbName = 'testdb';
// Create connection and select DB
// $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
// if($db->connect_error){
//    die("Unable to connect database: " . $db->connect_error);
// }


// DB details - SQLite
$db = new MyDB();

if(!$db) {
    echo $db->lastErrorMsg();
} else {
    // echo "Opened database successfully\n";

    $init_sql="CREATE TABLE IF NOT EXISTS claim_images (claim_id INTEGER,claim_number CHAR(50), image_url CHAR(255))";

    $ret = $db->exec($init_sql);
    if(!$ret) {
        echo $db->lastErrorMsg();
    }

    // seed
    // $count = $db->querySingle("SELECT COUNT(*) from claim_images");
    // if ($count <= 0) {
    //     $ret = $db->exec("INSERT INTO claim_images VALUES (1, '001', 'https://picsum.photos/seed/1/300/200'), (2, '002', 'https://picsum.photos/seed/2/300/200'), (3, '003', 'https://picsum.photos/seed/3/300/200'), (4, '004', 'https://picsum.photos/seed/4/300/200'), (5, '005', 'https://picsum.photos/seed/5/300/200')");
    // }
    return $db;
}

class MyDB extends SQLite3 {
    function __construct() {
        $this->open('./data/test.db');
    }
}

?>