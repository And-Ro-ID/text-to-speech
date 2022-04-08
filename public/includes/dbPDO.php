<?php

function OpenCon() {
    $config = parse_ini_file('config.ini');
    $dev = $config['dev'];
    
    if($dev === '1'){
    $dsn = $config['dsn'];
    $username = $config['username'];
    $password = $config['password'];
    }
    else {
        $dsn = getenv('MYSQL_DSN');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');
    }

    static $conn;
    if (!isset($conn)) {
        try {
            $conn = new PDO($dsn, $username, $password) or die("Connection failed: %s\n" . $conn->error);
        } catch (PDOException $e) {
            print "Connection Failed!";
            die();
        }
    }
    return $conn;
}

function CloseCon($conn) {
    $conn = null;
}
