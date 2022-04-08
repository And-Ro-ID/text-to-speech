<?php

session_start();
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: same-origin");

if (isset($_SESSION['token']) && isset($_POST['characters']) && isset($_POST['hash']) && isset($_POST['quality'])) {
    $dl_path = __DIR__.'/..'; // parent folder of this script
    $config_name = 'includes/config.ini';
    $config_path = $dl_path . DIRECTORY_SEPARATOR . $config_name;
    $config = parse_ini_file($config_path);
    $dev = $config['dev'];
    $db_name = $config['db_path'];
    $db_path = $dl_path . DIRECTORY_SEPARATOR . $db_name;
    $sess_time = $config['sess_time'];
    $timezone = $config['timezone'];
    $promoID = $config['promo'];
    $token_session_id = session_id();
    $quality = htmlspecialchars($_POST['quality']);
     if($dev === '1'){
        $pepper = $config['pepperMain'];
    }
    else {
        $pepper = getenv('PEPPER');
    }
    require_once($db_path);
     $dbh = OpenCon();
    
    if ($quality === 'wavenet') {
        $qualPrice = $config['wavenet'];
    } else {
        $qualPrice = $config['standard'];
    }
    $chars_before = iconv_strlen($_POST['characters']);
    $html_spaces = htmlentities($_POST['characters']);
    $stripped1 = html_entity_decode(trim(preg_replace('/(&nbsp;)+|\s\K\s+/', '', $html_spaces)));
    $strippedOpen = str_replace(' <', '<', $stripped1);
    $finalString = str_replace('> ', '>', $strippedOpen);
    $chars = iconv_strlen($finalString);
    $saved_chars = $chars_before - $chars;



    $tokenHash = htmlspecialchars($_POST['hash']);
    // Pepper current SESSION id
    $token_peppered = hash_hmac('sha3-512', $token_session_id, $pepper);
    $stmt = $dbh->prepare('SELECT T.TokenID, T.value, T.balance, T.chars, P.cost, U.UserID, S.TokenID,S.sID,S.token,S.created_at FROM session_token S JOIN users U ON U.UserID=S.UserID JOIN tokens T ON T.UserID = U.UserID JOIN promo P ON P.PromoID = T.PromoID WHERE S.sID = ? AND T.hash = ?');
    $stmt->bindParam(1, $token_peppered);
    $stmt->bindParam(2, $tokenHash);
    if ($stmt->execute()) {
        $rows = $stmt->fetch();
        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            $TokenID = $rows[0];
            $TokenValue = $rows[1];
            $TokenBalance = $rows[2];
            $TokenChars = $rows[3];
            $cost = $rows[4];
            $userID = $rows[5];
            $Session_token_id = $rows[6];
            $token_stored = $rows[8];
            $timestamp2 = $rows[9];

            // Check if current SESSION token and stored SESSION token ara matched 
            if (password_verify($token_peppered, $token_stored)) {

                //date_default_timezone_set($timezone);
                $minutes = (time() - strtotime($timestamp2)) / 60;
                if ($minutes >= $sess_time) {
                    $stmt3 = $dbh->prepare('DELETE FROM session_token WHERE session_token.TokenID = ?');
                    $stmt3->bindParam(1, $Session_token_id);
                    $stmt3->execute();
                    $sess_expire = 'Your session is expired';
                    $_SESSION = array();
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                        );
                    }
                    session_destroy();
                    echo 'expired';
                } else {
                    //echo "Successfuly looked up";
                    echo updateToken($userID, $TokenID, $tokenHash, $TokenBalance, $chars, $TokenChars, $cost, $qualPrice, $dbh, $saved_chars);
                }
            } else {
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                    );
                }
                session_destroy();
                echo "wrong pass";
            }

            CloseCon($dbh);
        } else {
            echo 'empty results';
        }
    } else {
        echo 'not executed';
    }
} else {
    echo 'No session';
    $sid = 'false';
    $sess_expire = 'Please log in.';
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function updateToken($UserID, $TokenID, $hash, $TokenBalance, $chars, $TokenChars, $cost, $qualPrice, $dbh, $saved_chars) {
    $totalChars = 15 + $chars;
    $newTotalChars = $TokenChars + $totalChars;
    $total = round($cost * $qualPrice * $totalChars, 5);
    $subtotal = round(($TokenBalance - $total), 5);

    if ($subtotal >= 0) {
        $newBalance = $subtotal;
        $stmt = $dbh->prepare('UPDATE tokens SET balance=?, chars=?  WHERE TokenID=?');
        $stmt->bindParam(1, $newBalance);
        $stmt->bindParam(2, $newTotalChars);
        $stmt->bindParam(3, $TokenID);
        if ($stmt->execute()) {
            addTransaction($UserID, $total, 2, $totalChars, $dbh);
            updateTokenInfo($TokenID, $newBalance, $newTotalChars);
            $data = strval('updated' . $newBalance . ',' . $newTotalChars . ',' . $hash . ',' . $saved_chars);
        } else {
            $data = 'Database problem.';
        }
    } else {

        $data = 'no funds';
    }
    return $data;
}

function addTransaction($userID, $sum, $code, $chars, $dbh) {
    $stmt = $dbh->prepare('INSERT INTO transactions (UserID,sum,code,chars) VALUES (?, ?, ?, ?)');
    $stmt->bindParam(1, $userID);
    $stmt->bindParam(2, $sum);
    $stmt->bindParam(3, $code);
    $stmt->bindParam(4, $chars);

    if ($stmt->execute()) {
        $data = 'Transaction updated';
    } else {
        $data = 'Transaction failed';
        //$data = 'UserID: '.$userID.' Inv: '.$ppInvoice.' Sum: '.$sum.' Code: '.$code. ' Chars: '.$chars. ' ';
    }
    return $data;
}

function updateTokenInfo($token_id, $new_balance, $total_chars) {
    $data = json_decode($_SESSION['tokenInfo']);
    foreach ($data as &$value) {
        if ($value[0] === $token_id) {
            $value[3] = $new_balance;
            $value[4] = $total_chars;
        }
    }
    $_SESSION['tokenInfo'] = json_encode($data);
}
