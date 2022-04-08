<?php

session_start();
$config = parse_ini_file('../includes/config.ini');
$dev = $config['dev'];
$db_name = $config['db_path'];
$db_path = $dl_path . DIRECTORY_SEPARATOR . $db_name;
$crypt = $config['crypt'];
$crypt_cost = $config['crypt'];
if($dev === '1'){
        $pepper = $config['pepperMain'];
    }
    else {
        $pepper = getenv('PEPPER');
    }
require_once($db_path);

// Check if SESSION token was issued and stored in DB
if (isset($_SESSION['token'])) {
    $dbh = OpenCon();
    $token_session_id = session_id();
    $data = '';

    // Pepper current SESSION id
    $token_peppered2 = hash_hmac('sha3-512', $token_session_id, $pepper);
    $stmt = $dbh->prepare('SELECT session_token.TokenID,session_token.sID,session_token.token FROM session_token JOIN users ON users.UserID=session_token.UserID where session_token.sID = ?');
    $stmt->execute([$token_peppered2]);
    $rows = $stmt->rowCount();
    $_token2 = $stmt->fetch();
    $token_id = $_token2[0];
    $token_stored = $_token2[2];


    // Check if current SESSION token and stored SESSION token ara matched 
    if (password_verify($token_peppered2, $token_stored)) {
        $stmt3 = $dbh->prepare('DELETE FROM session_token WHERE session_token.TokenID = ?');
        $stmt3->bindParam(1, $token_id);
        $stmt3->execute();
        //echo 'Your session has been expired. Please log in again.';
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    } else {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
    CloseCon($dbh);
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $password = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email']);
    if(preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/",$email) && preg_match("/^(?=[a-zA-Z0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/",$password)) {
        $dbh = OpenCon();
    $stmt = $dbh->prepare('SELECT passwords.hash,users.UserID FROM passwords JOIN users ON users.UserID=passwords.UserID where users.email = ?');
    $stmt->execute([$email]);
    $stored_hash = $stmt->fetch();
    $rowCount = $stmt->rowCount();
    if($rowCount > 0){
    $userid = $stored_hash[1];
    else {
        $userid = 0;
        $stored_hash[0] = '';
    }
    echo login($password, $userid, $stored_hash[0], $pepper, $crypt, $crypt_cost, $dbh);

    CloseCon($dbh);
    }
    else {
        echo 'Non-valid password or email.';
    }
}

function login($password, $uid, $stored_hash, $pepper, $crypt, $crypt_cost, $con) {
    $pwd_peppered = hash_hmac("sha3-512", $password, $pepper);
    // $pass = password_hash(password, PASSWORD_DEFAULT);

    if (password_verify($pwd_peppered, $stored_hash)) {
        $token_random = session_id();
        $token_peppered = hash_hmac('sha3-512', $token_random, $pepper);
        $token = password_hash($token_peppered, $crypt, [$crypt_cost]);
        $stmt2 = $con->prepare('INSERT INTO session_token (UserID,sID,token) VALUES (?, ?, ?)');
        $stmt2->bindParam(1, $uid);
        $stmt2->bindParam(2, $token_peppered);
        $stmt2->bindParam(3, $token);
        if ($stmt2->execute()) {
            $_SESSION['token'] = 'set';
            $_SESSION['userID'] = $uid;
            $data = tokenInfo($uid, $con);
            if ($data === '') {
                $data1 = '1';
            } else {
                $data1 = $data;
            }
        } else {
            $data1 = '0';
        }
    } else {
        $data1 = 'Email or password are not matched with those on record. ';
    }

    return $data1;
}

function tokenInfo($userID, $dbh) {
    try {
        $stmt = $dbh->prepare('SELECT tokens.TokenID,tokens.hash,tokens.value,tokens.balance,tokens.chars,tokens.updated_at,tokens.created_at FROM tokens  WHERE tokens.UserID = ? AND tokens.balance >= 0.01 ORDER BY created_at DESC');
        $stmt->bindParam(1, $userID);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            $rowCount = $stmt->rowCount();
           
            if($rowCount > 0){
            $_SESSION['tokenInfo'] = json_encode($rows);
            }
            $data1 = '';
           
        } else {
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            $data1 = "Database error. ";
        }

        return $data1;
    } catch (PDOException $e) {
        
    }
}
