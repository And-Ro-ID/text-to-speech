<?php

session_start();

$dl_path = __DIR__.'/..'; // parent folder of this script
$config_name = 'includes/config.ini';
$config_path = $dl_path . DIRECTORY_SEPARATOR . $config_name;
$config = parse_ini_file($config_path);
$dev = $config['dev'];
$vendor = $config['vendor_path'];
$vendor_path = $dl_path . DIRECTORY_SEPARATOR . $vendor;
$db_name = $config['db_path'];
$db_path = $dl_path . DIRECTORY_SEPARATOR . $db_name;
require $vendor_path;

$URL = $config['URL'];
$redirection = $config['browser_red'];
$wave = $config['wavenet'];
$standard = $config['standard'];

$locale = 'en-US';


$locale_path = $dl_path . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'translation.ini';
$translation = parse_ini_file($locale_path);



$menuEnabled = $translation['menuEnabled'];
$menuDisabled = $translation['menuDisabled'];
$menuQualDisabled = $translation['menuQualDisabled'];
$menuLangDisabled = $translation['menuLangDisabled'];
$menuAudioEnabled = $translation['menuAudioEnabled'];
$menuAudioDisabled = $translation['menuAudioDisabled'];
$ssmlButtonsDisabled = $translation['ssmlButtonsDisabled'];
$tooltipEmail = $translation['email'];
$tooltipPassword = $translation['password'];
$tooltipCounter = $translation['counter'];
$tooltipText = $translation['btnText'];
$tooltipSSML = $translation['btnSSML'];
$tooltipPlayer = $translation['player'];
$btnBreak = $translation['btnBreak'];
$btnVolume = $translation['btnVolume'];
$btnSpeed = $translation['btnSpeed'];
$btnPitch = $translation['btnPitch'];
$btnEmphasis = $translation['btnEmphasis'];
$btnSayAs = $translation['btnSayAs'];

if (isset($_SESSION['token'])) {
    require_once($db_path);

    if ($dev === '1') {
        $pepper = $config['pepperMain'];
    } else {
        $pepper = getenv('PEPPER');
    }
    $sess_time = $config['sess_time'];
    $timezone = $config['timezone'];
    $dbh = OpenCon();
    $token_session_id = session_id();

    // Pepper current SESSION id
    $token_peppered2 = hash_hmac('sha3-512', $token_session_id, $pepper);
    $stmt = $dbh->prepare('SELECT users.email,session_token.TokenID,session_token.token,session_token.created_at FROM session_token JOIN users ON users.UserID=session_token.UserID where session_token.sID = ?');
    $stmt->execute([$token_peppered2]);
    $rows = $stmt->rowCount();
    $_token = $stmt->fetch();
    $userEmail = $_token[0];
    $token_id = $_token[1];
    $token_stored = $_token[2];
    $timestamp = $_token[3];

    // Check if current SESSION token and stored SESSION token ara matched 
    if (password_verify($token_peppered2, $token_stored)) {  
        $minutes = (time() - strtotime($timestamp)) / 60;
        if ($minutes >= $sess_time) {
            $stmt3 = $dbh->prepare('DELETE FROM session_token WHERE session_token.TokenID = ?');
            $stmt3->bindParam(1, $token_id);
            $stmt3->execute();
            $sess_expire = 'Your session is expired';
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            $sid = 'false';
        } else {
            $sid = 'true';
            $sess_expire = 'you are logged';
        }
    } else {
        $sid = 'false';
        $sess_expire = 'Your session is expired';
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    CloseCon($dbh);
} else {
    $sid = 'false';
    $sess_expire = 'Please log in.';
    $userEmail = 'none';
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}


if (isset($_SESSION['container'])) {
    $container = $_SESSION['container'];
} else {
    $container = 'null';
}



if (isset($_SESSION['tokenInfo'])) {
    $tokens = $_SESSION['tokenInfo'];
    $tokenInfo = 'yes';
    $_tokens = json_decode($tokens);
  
} else {
    $tokenInfo = 'no';
    $tokens = 0;
}

function withoutRounding($number, $total_decimals) {
    $_number = (string) $number;
    if ($_number === '') {
        $_number = '0';
    }
    if (strpos($_number, '.') === false) {
        $_number .= '.';
    }
    $number_arr = explode('.', $_number);

    $decimals = substr($number_arr[1], 0, $total_decimals);
    if ($decimals === false) {
        $decimals = '0';
    }

    $return = '';
    if ($total_decimals == 0) {
        $return = $number_arr[0];
    } else {
        if (strlen($decimals) < $total_decimals) {
            $decimals = str_pad($decimals, $total_decimals, '0', STR_PAD_RIGHT);
        }
        $return = $number_arr[0] . '.' . $decimals;
    }
    return $return;
}
?>


<!DOCTYPE HTML>  
<html>
    <head>

        <title>Uvoicer.com - Best Text To Speech App ⭐ 40+ languages and 240+ voices supported ⭐ Convert text to human voice MP3⭐</title>
        <meta name=”description” content="Uvoicer.com - Best Text To Speech App ⭐ 40+ languages and 235+ voices supported ⭐ Convert text to human voice MP3⭐ 
              This web application allows you to convert your text to speech. Simply insert your text, select quality, 
              language, voice - and you are all set! Our SSML editor allows you to fully control your audio output.  
              If you are an online course creator, youtuber or gamer - this application is for you!" />
        <meta name=”keywords” content="Uvoicer,uvoicer.com,text-to-speech,Text To Speech,google voices,tts,convert text to voice, text to voice, 
              convert text to speech,text to mp3,convert text to mp3,ssml, ssml editor, online ssml edtitor, free ssml,
              free ssml editor,Uvoicer.com,Uvoicer,text-to-speech,Text To Speech,SSML,SSML editor,online tts,tts,
              online text to speech,mp3 text to speech,text to mp3 converter,convert text to mp3,online mp3 converter,
              best text to speech 2020,best text to speech in 2020,best text to speech in 2021,best text to speech 2021,
              best tts in 2020,best tts 2020,best text to speech,best text to speech converter,top 10 text to speech,
              best text to voice converter,best text to voice"/>
        <meta property="og:image" content="https://www.uvoicer.com/favicon.png"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            body {
                margin-left:5px;
                margin-right:5px;
                margin-top:0;
            }
            .paypal,.sum3 {
                color: red;
            }
            #paypal-button-container {
                width: 60%;
            }

            .label {
                float: left;
            }
            .iteration {
                padding-right:5px;
                font-family:courier,arial,helvetica;
                padding-left: 5px;
            }
            .iteration2 {
                float:right;
            }

            .total1,.total2 {
                display:none;
            }
            .odd {
                background-color: #BDB76B;
            }
            .even {
                background-color: whitesmoke;
            }
            .subiteration {
                width: 100%;
                padding-top:10px;
                padding-bottom:10px;
                background-color: skyblue;
                display:none;
            }

            .subiterationOn {
                width: 100%;
                padding-top:10px;
                padding-bottom:10px;
                background-color: skyblue;
                display:none;
            }
            .spansubiteration {
                font-family:courier,arial,helvetica;
                font-size: 12pt;
                padding-left: 5px;
            }

            .innerToken{
                cursor: pointer;
                font-family:courier,arial,helvetica;
                font-size: 12pt;
            }
            .innerToken:hover{
                color: blue;
                cursor: pointer;
                font-family:courier,arial,helvetica;
                font-weight: bold;
                font-size: 12pt;
            }
            .spansubiteration1 {
                font-family:courier,arial,helvetica;
                font-size: 12pt;
                padding-left: 5px;
            }
            .spansubiteration2 {
                font-family:courier,arial,helvetica;
                font-weight: bold;
                font-size: 12pt;
                padding-left: 20px;

            }
            .control {

                width: 88%;
                margin-left: auto;
                margin-right: auto;
                padding-top: 10px;
            }
            input.control,label {
                display:block;
                text-align: center;
            }

            button.login {
                background: skyblue;
                border: 1px solid red;
                border-color: #2d2d2d;
                border-radius: 4px;
                margin-left:15px;
                width: 80px;
            }

            button.signout {
                background: skyblue;
                border: 1px solid red;
                border-color: #2d2d2d;
                border-radius: 4px;
                margin-left:15px;   

            }
            #buyToken {
                float: left;
                margin-left:0;
                margin-top: 0;  
                margin-bottom: 0.5em; 
            }

            button,select {
                cursor: pointer;
            }
            input.login {
                border-width: 1px;
                border-color: #2d2d2d;
                background: skyblue;  
                border-radius: 4px;
            }



            input.payment {
                float: right
            }

            .mainDiv {
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                margin-top: 0;
                border-width: 0px;
                border-style: solid;
                border-radius: 10px;
                border-color: #008040;
                overflow: auto;
            }


            .char_result1 {
                font-size: 16px;
                font-weight: bold;
                color: black;
                padding-top: 0px;

            }
            .char_result2 {
                padding-top: 0px;
                font-size: 20px;
                font-weight: bold;
                color: red;

            }

            .area {
                width: 50%;
                height: 380px;
                min-width: 55%;
                min-height: 380px;
                border: 1px solid grey;
                border-radius: 30px;
                padding: 20px;
                color: white;
                font-size:18px;
                font-family: Consolas, Courier;
                float: left;
                background-color: #2d2d2d;
                resize: vertical;
                overflow: auto;
                position: absolute;
                top: 150px;

            }


            .container {
                border: 1px solid grey;
                display: block;
                width: 35%;
                background-color: skyblue;
                border-left-color: white;
                border-left-width: 0px;
                border-radius: 30px;
                padding: 0;
                margin: 0;
                float: left;               
                overflow:auto;
                position:absolute;


            }

            .outerTag{
                color:white;
                font-size:18px;
            }

            .innerTag1{
                color: #f92672;
                font-size:18px;
            }

            .innerTag2{
                color:#a6e22e;
                font-size:18px;
            }
            .equalTag {
                color:#f92672;
            }

            .valueTag {
                color:skyblue;
            }

            .TableToken{
                display: table;
                width: 100%;
            }
            .RowToken
            {
                display: table-row;


            }
            .CellToken{
                display: table-cell;
                text-align: left;
                width:50%;

            }
            .TableLog{
                display: table;
                float: right;
            }
            .RowLog
            {
                display: table-row;


            }
            .CellLog{
                display: table-cell;
                text-align: left;

            }

            .TableLogged{
                display: table;
                float: right;
                border: solid 0px;
                width: 100%;
                float:right;
                height: 350px;
                padding: 5px;
                overflow: scroll;
            }
            .RowLogged
            {
                display: table-row;
            }
            .CellLogged{
                display: table-cell;
                text-align: left;
                width: 70%;
                border: solid 0px;
                font-family:arial,helvetica;
            }
            #CellLogged01 {
                text-align: right; 
                height: 20px;
                border-bottom-width: 1px;
            }

            #CellLogged02 {
                text-align: right;
                border-bottom-width: 1px;
            }
            #CellLogged03 {
                padding-top: 0.5em;  
                height: 70px;
            }
            
            #CellLogged05 {
            }
            .SpanLogged03 {
                color: black;
                display:block;
                font-weight: 700;
                z-index: 20;
            }
            
            .TableSign{
                display: table;
                float: right;
            }
            .RowSign
            {
                display: table-row;


            }
            .CellSign{
                display: table-cell;
                text-align: left;
                height: 10px;

            }

            .Table
            {
                display: table;
                width: 100%;
                border-spacing: 0px 0px;
                margin-top:0;
                margin-left: auto;
                margin-right: auto;
                border:solid;
                border-width: 0px;
                padding: 0;
            }
            .Title
            {
                display: table-caption;
                text-align: center;
                font-weight: bold;
                font-size: larger; 
                margin:0;
            }

            .Row
            {
                display: table-row;
            }
            .Cell
            {
                display: table-cell;
                width: 50%;
                border-width: 0px;
                margin:0px;

            }

            .error{
                color:red;
                float: none;

            }



            p {
                padding: 0;
                margin: 0;              
            }
            p.btnHeading {
                font-weight :bold;
                font-size: 18px;
            }
            .btnBreak {
                background-color: pink;
            }
            .btnBreakHover {
                background-color: #D3A0A9;
                cursor: pointer;
            }

            .btnEmphasis {
                background-color: greenyellow;
                float:right;
            }
            .btnEmphasisHover {
                background-color: #90D127;
                cursor: pointer;
            }

            .btnVolume {
                background-color: #BDB76B;
                float:right;
            }
            .btnVolumeHover {
                background-color: #918C52;
                cursor: pointer;
            }

            .btnRate {
                background-color: #F76095;
                float:right;
            }
            .btnRateHover {
                background-color: #f92672;
                cursor: pointer;
            }

            .btnPitch {
                background-color: #FFA947;
                float:right;
            }
            .btnPitchHover {
                background-color: #FF8800;
                cursor: pointer;
            }

            .btnSayAs {
                background-color: #44cc44; 
            }
            .btnSayAsHover {
                background-color: #2E892E;
                cursor: pointer;
            }

            .btnBreak,.btnSayAs,.btnEmphasis,.btnVolume,.btnRate,.btnPitch,.btnAudio{

            }


            .main {
                width: 150px;
                height:30px;
                background: pink;
                border: 2px solid red;
                border-radius: 4px;
                margin-left:15px;
                text-align: justify;
                text-align-last: center;
            }

            #media{
                height:50px;
                width:30%;
                padding-left: 150px;
            }


            .sum {
                border: 1px solid #2d2d2d;
                border-radius: 2px;
                background: skyblue;
                width: max-content;
                margin-top: 30px;
                height:30px;
                padding-left: 10px;

            }

            .btnAudio {
                cursor: pointer;
                background-color: pink;
            }
            .btnAudioHover {
                cursor: pointer;
                background-color:#F76095;
            }

            .audio {
                width: 150px;
                height:30px;
                background: #44cc44;
                border: 2px solid #2E892E;
                border-radius: 4px;
                margin-left:15px;
            }
            .audioHover{ 
                width: 150px;
                height:30px;
                background-color: #2E892E;;
                border: 2px solid #2E892E;
                border-radius: 4px;
                margin-left:15px;
            }


            .text, .ssml {
                height:1.7em;
                width:4em;                
            }
            
            .auto {
                height:1.7em;
                width:4em;   
            }

            #toggle_text {
                color:blue;
                border-color:dodgerblue;
                border-width:1px;
                padding: 0;
                margin: 0;
            }
            #toggle_ssml {
                color:black;
                border-color:grey;
                border-width:1px;
                padding: 0;
                margin: 0;
            }

            #tabs {
                border-width: 0px;           
                padding: 0px; 
                margin:0px;
                border-width:0px;                
            } 
            .ui-tabs-nav { 
                float:right;
                background: transparent;
                border:none;
                border-width: 0px; 
                -moz-border-radius: 0px; 
                -webkit-border-radius: 0px; 
                border-radius: 0px; 
                padding: 0px; 
                margin:0px;

            } 
            .ui-tabs-panel {
                width:36%;
                padding: 0px; 
                margin-left:auto;
                text-align: right;
            }


            .tabs-background { 
                height: 2em; 
                background: transparent; 
                border: transparent; 
                border-width:0px;
                -moz-border-radius: 0px; 
                -webkit-border-radius: 0px; 
                border-radius: 0px; 
                margin:0;
                padding:0;
            }

            p.align {
                text-align:right;
                margin:0;
                padding: 5px;
                width:100%;
            }
            .ui-tabs-nav li { 
                position: static; 
                border: 1px solid #c0c0c0 !important; 
                -moz-border-radius: 0px; 
                -webkit-border-radius: 0px; 
                border-radius: 0px; 
                margin:0px !important;
                padding:0px;
                font-size: 14px;

            } 
            .ui-tabs-nav li:first-child { 
                -moz-border-radius: 6px 0px 0px 6px; 
                -webkit-border-radius: 6px 0px 0px 6px; 
                border-radius: 6px 0px 0px 6px; 
            } 
            .ui-tabs-nav li:last-child { 
                -moz-border-radius: 0px 6px 6px 0px; 
                -webkit-border-radius: 0px 6px 6px 0px; 
                border-radius: 0px 6px 6px 0px;
            }

            .errorIN {
                border-width: 1px;
                border-color: #2d2d2d;
                background: pink;
            }
            .errorOUT {
                border-width: 1px;
                border-color: #2d2d2d;
                background: skyblue;
            }

            #mainspan {
                padding: 10px;
            }
            #fragment-4,#fragment-5{
                text-align:left;
            }
            .faq{
                font-family:"https://fonts.googleapis.com/css?family=Roboto:400,700,900&display=swap";
                font-size: 13pt;
            }

            .logo{
                position:absolute;
                top:10px;
                left:10px;
                z-index: 9;
            }
            .logo1{
                position:absolute;
                top:110px;
                left:330px;
                z-index: 10;
            }
            .tts{
                color: #000000;
                font-family: Arial;
                font-size: 10pt;
                font-weight: 800;
            }
            .ui-tooltip {
                padding: 5px 5px;
                color: black;
                border-radius: 10px;
                font: normal 12px arial,helvetica;
                box-shadow: 0 0 7px black;
            }

            .slider {
                -webkit-appearance: none;
                width: 100%;
                height: 10px;
                outline: none;
                opacity: 0.7;
                -webkit-transition: .2s;
                transition: opacity .2s;
                border-radius: 4px;
            }


            .slider:hover {
                opacity: 1;
            }

            .slider::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 20px;
                height: 20px;
                background: black;
                cursor: pointer;
            }

            .slider::-moz-range-thumb {
                width: 20px;
                height: 20px;
                background: #4CAF50;
                cursor: pointer;
                background: black;
                border: 0px solid red;
                border-radius: 12.5px;
                margin-left: 15px;
                border-width: 0px;
            }


            .slidecontainer {
                width: 100%;
            }

            #sliderEmphasis{
                background:  greenyellow;
            }

            #sliderPitch{
                background:  #FF8800;
            }

            #sliderSpeed{
                background:  #f92672;
            }
            
            #sliderVolume{
                background:  #918C52;
            }
            .display {
                color:black;
                float:right;
                padding-right: 33%;
                font-weight:400;
                text-decoration: underline;
            }

        </style>

        <script src=js/jquery-3.5.1.min.js></script>     
        <script src=js/jquery-1.12.4.js></script>
        <script src=js/jquery-ui.js></script>
        <script src="js/platform.js"></script>
        <script>
             var browser = platform.name;
            var version = parseFloat(platform.version);
            var redir = '<?php echo $redirection; ?>';
            if (browser.match(/WebView/i)) {
                if (version < 55) {
                    alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                    window.location.replace(redir);
                }
            }

            switch (browser) {
                case 'Firefox':
                    if (version < 52) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
                case 'Firefox for iOS':
                    if (version < 52) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
                case 'Microsoft Edge':
                    if (version < 15) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
                case 'IE':
                    alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                    window.location.replace(redir);
                    break;
                case 'Chrome':
                    if (version < 55) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }

                    break;
                case 'Chrome Mobile':
                    if (version < 55) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }

                    break;
                case 'Opera':
                    if (version < 42) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
                case 'Opera Mini':
                    if (version < 42) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                case 'Safari':
                    if (version < 10.1) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
                case 'Samsung Internet':
                    if (version < 6) {
                        alert('Your browser\'s version can\'t use our application. Please consider the following browsers:\r\n------------------------------------------------------------------------------------\r\n* Firefox 52+\r\n* Microsoft Edge 15+\r\n* Chrome 55+\r\n* Opera 42+\r\n* Safari 10.1+');
                        window.location.replace(redir);
                    }
                    break;
            } 


            window.onload = function () {
                document.getElementById("area").focus();
            };
            $(document).ready(function () {
                document.getElementById('displayEmphasis').textContent = "moderate";
                document.getElementById('displayPitch').textContent = "medium";
                document.getElementById('displaySpeed').textContent = "medium";
                document.getElementById('displayVolume').textContent = "medium";
                var checkedClass = $("input[type=radio]:checked").attr('class');
                myToken = $('input[type=hidden].' + checkedClass).attr('value');
                $('#area').each(function () {
                    this.contentEditable = true;
                    $(this).append('<span></span>');
                    // $(this).text($.trim($(this).text()));
                });
                ////////////////////////////////////////////////////////////////////////////////////////////////

                $("#tabs").tabs({
                    heightStyle: "auto"
                });
                var groupEL = $('#pass, #pass2, #pass21');
                groupEL.prop('disabled', true);
                var session = '<?php echo $sid; ?>';
                var container = '<?php echo $container; ?>';
                var sess_expire = '<?php echo $sess_expire; ?>';
                var tokenSet = '<?php echo $tokenInfo; ?>';
                var token_info = <?php echo $tokens; ?>;
                var URL = '<?php echo $URL; ?>';
                var newInt;
                var menuEnabled = '<?php echo $menuEnabled; ?>';
                var menuDisabled = '<?php echo $menuDisabled; ?>';
                var menuQualDisabled = '<?php echo $menuQualDisabled; ?>';
                var menuLangDisabled = '<?php echo $menuLangDisabled; ?>';
                var menuAudioEnabled = '<?php echo $menuAudioEnabled; ?>';
                var menuAudioDisabled = '<?php echo $menuAudioDisabled; ?>';
                var ssmlButtonsDisabled = '<?php echo $ssmlButtonsDisabled; ?>';
                var tooltipEmail = '<?php echo $tooltipEmail; ?>';
                var tooltipPassword = '<?php echo $tooltipPassword; ?>';
                var tooltipCounter = '<?php echo $tooltipCounter; ?>';
                var tooltipText = '<?php echo $tooltipText; ?>';
                var tooltipSSML = '<?php echo $tooltipSSML; ?>';
                var tooltipPlayer = '<?php echo $tooltipPlayer; ?>';
                var btnBreak = '<?php echo $btnBreak; ?>';
                var btnVolume = '<?php echo $btnVolume; ?>';
                var btnSpeed = '<?php echo $btnSpeed; ?>';
                var btnPitch = '<?php echo $btnPitch; ?>';
                var btnEmphasis = '<?php echo $btnEmphasis; ?>';
                var btnSayAs = '<?php echo $btnSayAs; ?>';
                
                    $('.btnBreak').attr({'title': btnBreak});
                    $('.btnVolume').attr({'title': btnVolume});
                    $('.btnRate').attr({'title': btnSpeed});
                    $('.btnPitch').attr({'title': btnPitch});
                    $('.btnEmphasis').attr({'title': btnEmphasis});
                    $('.btnSayAs').attr({'title': btnSayAs});
                    $('#char_result1').attr({'title': tooltipCounter});
                    $('#toggle_text').attr({'title': tooltipText});
                    $('#toggle_ssml').attr({'title': tooltipSSML});
                    $('#media').attr({'title': tooltipPlayer});
                    $('#qual').attr({'title': menuQualDisabled});
                    $('#lang').attr({'title': menuLangDisabled});
                    $('#voices').attr({'title': menuDisabled});
                    $('#audio').attr({'title': menuAudioDisabled});
                    $('#videoplayer').on('ended', function (e) {
                     
                    this.currentTime = 0;
                });
                $('#char_result2').text('0');
                
                if ($.trim(session) === 'true' && $.trim(tokenSet !== 'no')) {
                    $('#audio').attr({'title': menuAudioEnabled});
                    $('#qual,#lang,#voices').attr({'title': menuEnabled});
                    var usEmail = '<?php echo $userEmail; ?>';
                    if (token_info.length === 1)
                        var tokenStr = 'token';
                    else
                        var tokenStr = 'tokens';
                    $('.TableLogged').show();
                    $('.TableLog').hide();
                    $('.demo').hide();
                    $('#show_log').text('Hi, ' + usEmail);
                    if ($.trim(tokenSet) === 'yes') {
                        $('.SpanLogged03').text('My account');
                    } else {

                    }
                } else {
                    $('.TableLogged').hide();
                    $('.TableLog').show();
                    $('#error1').text(sess_expire);
                    $('#error1').position({
                        of: $("#email"),
                        my: "center top",
                        at: "center bottom",
                        collision: "none none"
                    });
                }

                if ($.trim(container) === 'true' || $.trim(session) !== 'true') {
                    var h_area = $("#area").height();
                    var w_area = $("#area").width();
                    $('#area').attr({'spellcheck': false, 'autocorrect': false, 'autocapitalize': false});
                    $('#toggle_ssml').css({"color": "blue", "border-color": "dodgerblue", "border-width": "1px"});
                    $('#toggle_text').css({"color": "black", "border-color": "grey", "border-width": "1px"});
                    $('.container').css({'display': 'block', 'padding': '20px', 'height': +h_area + 'px'});
                    $('#area').css({'background-color': '#2d2d2d', 'color': 'white', 'font-size': '18px', 'font-family': 'Consolas', 'height': +h_area + 'px', 'width': +w_area + 'px'});
                    document.getElementById("area").focus();
                    $('#ssml_panel').position({
                        of: $("#area"),
                        my: "left top",
                        at: "right top",
                        collision: "none none"
                    });
                } else {
                    $('#toggle_text').attr({'spellcheck': true, 'autocorrect': true, 'autocapitalize': true});
                    $(this).css({"color": "blue", "border-color": "dodgerblue", "border-width": "1px"});
                    $('#toggle_ssml').css({"color": "black", "border-color": "grey", "border-width": "1px"});
                    $('.container').css({"display": "none"});
                    $('#area').css({'background-color': '#2d2d2d', 'color': 'white', 'font-size': '18px', 'font-family': 'Consolas'});
                    document.getElementById("area").focus();
                }


                /*
                 if ($.trim(_areatext) !== '' ) {
                 $('#area').text(_areatext);
                 }
                 */


/////////////////////////////////////////////////////////////////////////////////////////////////////////

                paypal.Buttons({
                    style: {
                        size: 'small',
                        fundingicons: 'false',
                        color: 'gold',
                        shape: 'pill',
                        label: 'paypal',
                        height: 40
                    },
                    commit: true,
                    createOrder: function (data, actions) {
                        // This function sets up the details of the transaction, including the amount and line item details.
                        var tokenSum = $('#sum2').val();
                        $.ajax({
                            url: URL + 'randomInt.php',
                            method: 'POST',
                            dataType: 'text',
                            data: {rand: 'yes'
                            },
                            success: function (data) {
                                var newData = data.replace(/<[^>]+>/g, '');
                                newInt = parseInt($.trim(newData));
                                alert(newint);
                            }
                        });
                        return actions.order.create({

                            purchase_units: [{
                                    amount: {
                                        value: tokenSum
                                    },
                                    description: 'Uvoicer.com - your gateway to the Text-To-Speech world!',
                                    invoice_id: newInt,
                                    soft_descriptor: 'TTS-token'
                                }],
                            application_context: {
                                brand_name: 'Uvoicer.com'
                            },
                            payment_method: {
                                payee_preferred: 'IMMEDIATE_PAYMENT_REQUIRED'
                            }
                        });
                    },
                    onApprove: function (data, actions) {
                        // This function captures the funds from the transaction.
                        return actions.order.capture().then(function (details) {
                            // This function shows a transaction success message to your buyer.
                            var name = details.payer.name.given_name;
                            var amount = details.purchase_units[0].amount.value;
                            $.ajax({
                                url: URL + 'buyToken.php',
                                method: 'POST',
                                data: {value: amount,
                                    ppInvoice: newInt
                                },
                                beforeSend: function () {
                                    // Show image container
                                    $("body").css("cursor", "progress");
                                },
                                success: function (data) {
                                    var newdata = $.trim(data.replace(/<[^>]+>/g, ''));
                                    if (newdata === 'Token bought') {
                                        $('.total1,.total2').fadeOut(400, function () {
                                            $('#buyToken').text('Buy token');
                                        });
                                    } else {
                                        alert($.trim(data.replace(/<[^>]+>/g, '')));
                                    }
                                    //$('#lang').after('<span class="error">Submitted1!</span>');

                                },
                                complete: function () {
                                    // Hide image container
                                    $("body").css("cursor", "default");
                                },
                                fail: function () {
                                    // Hide image container
                                    alert('For some technical reasons we cannot credit your account.\r\n Please report the problem.');
                                }
                            });
                            alert('Thank you, ' + name + ' for your purchase of $' + amount + ' token!\r\n\r\n You will soon receive an official receipt from PayPal.\r\nPlease check your e-mail box.');
                            location.reload();
                        });
                    }
                }).render('#paypal-button-container');
                ///////////////////////////////////////////////////////////////////////////////////////////////////             

                function ValidateEmail(inputText)
                {
                    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                    if (inputText.match(mailformat))
                    {
                        return true;
                    } else {
                        return false;
                    }
                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////

                function ValidatePassword(inputText)
                {
                    var passwordformat = new RegExp("^(?=[a-zA-Z0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])");
                    var nonumeric = new RegExp("[^A-Za-z0-9]");
                    if (inputText.match(passwordformat))
                    {
                        if (!inputText.match(nonumeric)) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }

                ///////////////////////////////////////////////////

                async function audioFetch(lang, voice, ssml, auto) {
                    await fetch('getmp3.php', {
                        method: "POST",
                        body: 'lang=' + lang + '&voices=' + voice + '&hiddenSSML=' + ssml + '&auto=' + auto,
                        headers:
                                {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'Cache-Control': 'no-cache',
                                    'Expires': '-1',
                                    'Strict-Transport-Security': 'max-age=31536000;  includeSubDomains',
                                    'X-XSS-Protection': '1; mode=block',
                                    'X-Frame-Options': 'SAMEORIGIN',
                                    'X-Content-Type-Options': 'nosniff'
                                }

                    }).then(res => {
                        document.getElementById("qual").disabled = false;
                        document.getElementById("lang").disabled = false;
                        document.getElementById("voices").disabled = false;
                        document.getElementById("audio").disabled = false;
                        $("#audio").removeClass("audioHover");
                        $("#audio").addClass("audio");
                        $("body").css("cursor", "default");
                        if (res.ok) {
                            return res.blob();
                        } else {
                            throw new Error(res.statusText);
                        }
                    }).then(resblob => {
                        if (resblob !== undefined) {
                            var blob = new Blob([resblob], {type: 'audio/mpeg'});
                            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                                window.navigator.msSaveOrOpenBlob(blob, 'SSML.mp3');
                                console.log("Audio received.");
                            } else {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download = 'SSML.mp3'; // the filename you want to give
                                document.getElementById("media_src").src = url;
                                document.getElementById("media").load();
                                document.body.appendChild(a);
                                a.click();
                                //window.URL.revokeObjectURL(url);
                                console.log("Audio received. Auto set to " + auto);
                            }

                        }

                    }).catch(reason => {
                        $('.TableLogged').hide();
                        $('.TableLog').show();
                        alert("Unable to load audio file. \r\nYour session is expired. Please login again.\r\n");
                        console.log("Unable to load audio file: " + reason);
                    });
                }


                ///////////////////////////////////////////////////////////////////////////////////////

                function pasteHtmlAtCaret(openTag, closeTag, defaultText) {
                    var sel, range, html;
                    if (window.getSelection) {
                        // IE9 and non-IE
                        sel = window.getSelection();
                        if (sel === "" && closeTag !== "") {
                            html = openTag + defaultText + closeTag;
                        } else {

                            html = openTag + sel + closeTag;
                        }

                        if (sel.getRangeAt && sel.rangeCount) {
                            range = sel.getRangeAt(0);
                            range.deleteContents();
                            // Range.createContextualFragment() would be useful here but is
                            // only relatively recently standardized and is not supported in
                            // some browsers (IE9, for one)
                            var el = document.createElement("div");
                            el.innerHTML = html;
                            var frag = document.createDocumentFragment(), node, lastNode;
                            while ((node = el.firstChild)) {
                                lastNode = frag.appendChild(node);
                            }
                            range.insertNode(frag);
                            // Preserve the selection
                            if (lastNode) {
                                range = range.cloneRange();
                                range.setStartAfter(lastNode);
                                range.collapse(true);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            }
                        }
                    } else if (document.selection && document.selection.type !== "Control") {
                        // IE < 9
                        document.selection.createRange().pasteHTML(html);
                    }
                }


                //////////////////////////////////////////////////////////////////////////////////////////////////////////

                $('#log').on('click', function () {
                    var error1 = $('#error1');
                    var error2 = $('#error2');
                    var mail = $('#email').val();
                    var pass = $('#pass').val();
                    if (mail.length < 6 || mail.length > 30)
                    {
                        error1.text("Enter 6-30 characters");
                        error1.position({
                            of: $("#email"),
                            my: "center top",
                            at: "center bottom",
                            collision: "none none"
                        });
                        $('#email').removeClass("login");
                        $('#email').removeClass("errorOUT");
                        $('#email').addClass("errorIN");
                        $('#email').focus();
                    } else {
                        if (ValidateEmail(mail) === false) {
                            error1.text("Enter a valid email");
                            error1.position({
                                of: $("#email"),
                                my: "center top",
                                at: "center bottom",
                                collision: "none none"
                            });
                            $('#email').removeClass("login");
                            $('#email').removeClass("errorOUT");
                            $('#email').addClass("errorIN");
                            $('#email').focus();
                        } else {


                            if (pass.length < 10 || pass.length > 30)
                            {
                                error2.text("Enter 10-30 characters");
                                error2.position({
                                    of: $("#pass"),
                                    my: "left top",
                                    at: "left bottom",
                                    collision: "none none"
                                });
                                $('#pass').removeClass("login");
                                $('#pass').removeClass("errorOUT");
                                $('#pass').addClass("errorIN");
                                $('#pass').focus();
                            } else {
                                if (ValidatePassword(pass) === false) {
                                    error2.html("Password must contain<br> alphanumeric characters<br> with at least <font color=#000000>1 uppercase</font><br>and <font color=#000000>1 lowercase</font> letters");
                                    error2.position({
                                        of: $("#pass"),
                                        my: "left top",
                                        at: "left bottom",
                                        collision: "none none"
                                    });
                                    $('#pass').removeClass("login");
                                    $('#pass').removeClass("errorOUT");
                                    $('#pass').addClass("errorIN");
                                    $('#pass').focus();
                                } else {
                                    error2.text("");
                                    $('#pass').removeClass("errorIN");
                                    $('#pass').addClass("errorOUT");
                                }
                            }


                            error1.text("");
                            $('#email').removeClass("errorIN");
                            $('#email').addClass("errorOUT");
                        }


                    }
                    if ($(".error").text().length === 0) {
                        $.ajax({
                            url: URL + 'login.php',
                            method: 'POST',
                            headers:
                                    {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'Cache-Control': 'no-cache',
                                        'Expires': '-1',
                                        'Strict-Transport-Security': 'max-age=31536000;  includeSubDomains',
                                        'X-XSS-Protection': '1; mode=block',
                                        'X-Frame-Options': 'SAMEORIGIN',
                                        'X-Content-Type-Options': 'nosniff'
                                    },
                            dataType: 'text',
                            data: {email: mail,
                                password: pass
                            },
                            beforeSend: function () {
                                // Show image container
                                $("body").css("cursor", "progress");
                            },
                            success: function (data) {
                                var data1 = data.replace(/<[^>]+>/g, '');
                                var newdata = $.trim(data1);
                                switch (newdata) {
                                    case 'Email or password are not matched with those on record.':
                                        $('#error1').html('Email or password are <br>not matched');
                                        $('#error1').position({
                                            of: $("#email"),
                                            my: "center top",
                                            at: "center bottom",
                                            collision: "none none"
                                        });
                                        $('#email').val('');
                                        $('#pass').val('');
                                        $('#email').removeClass("login");
                                        $('#email').removeClass("errorOUT");
                                        $('#email').addClass("errorIN");
                                        $('#email').focus();
                                        break;
                                    case "0":
                                        alert('For some reasons your session cannot be started. \r\nPlease try again or report the problem.');
                                        break;
                                    case "1":
                                        $('.TableLog').hide();
                                        $('.TableLogged').show();
                                        $('#show_log').text('Hi, ' + mail);
                                        /*
                                         $.ajax({
                                         url: '/Projects/TTS/variables2.php',
                                         method: 'POST',
                                         dataType: 'text',
                                         data: {userEmail: mail
                                         }
                                         });
                                         */
                                        location.reload();
                                        break;
                                    default:
                                        $('.TableLogged').hide();
                                        $('.TableLog').show();
                                        $('#email,#password').val('');
                                        $('#email').focus();
                                        alert('\u26D4\t' + newdata + '\r\n\tPlease login again or report the problem.');
                                }


                            },
                            complete: function () {
                                // Hide image container
                                $("body").css("cursor", "default");
                            },
                            fail: function () {
                                // Hide image container
                                alert('\u26D4\tServer error. Try later or report the problem.');
                            }
                        });
                    }

                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $('#sign').on('click', function () {
                    var error4 = $('#error4');
                    var error5 = $('#error5');
                    var mail2 = $('#email2').val();
                    var pass2 = $('#pass2').val();
                    var pass21 = $('#pass21').val();
                    if (mail2.length < 6 || mail2.length > 30)
                    {
                        error4.text("Enter 6-30 characters");
                        error4.position({
                            of: $("#email2"),
                            my: "center top",
                            at: "center bottom",
                            collision: "none none"
                        });
                        $('#email2').removeClass("login errOUT");
                        $('#email2').addClass("errorIN");
                        $('#email2').focus();
                    } else {
                        if (ValidateEmail(mail2) === false) {
                            error4.text("Enter a valid email");
                            error4.position({
                                of: $("#email2"),
                                my: "center top",
                                at: "center bottom",
                                collision: "none none"
                            });
                            $('#email2').removeClass("login");
                            $('#email2').removeClass("errorOUT");
                            $('#email2').addClass("errorIN");
                            $('#email2').val('');
                            $('#email2').focus();
                        } else {


                            if (pass2.length < 10 || pass2.length > 30)
                            {
                                error5.text("Enter 10-30 characters");
                                error5.position({
                                    of: $("#pass21"),
                                    my: "left top",
                                    at: "left bottom",
                                    collision: "none none"
                                });
                                $('#pass2').removeClass("login");
                                $('#pass2').removeClass("errorOUT");
                                $('#pass2').addClass("errorIN");
                                $('#pass2').focus();
                            } else {
                                if (ValidatePassword(pass2) === false) {
                                    error5.html("Password must contain<br> alphanumeric characters<br> with at least <font color=#000000>1 uppercase</font><br>and <font color=#000000>1 lowercase</font> letters");
                                    error5.position({
                                        of: $("#pass21"),
                                        my: "left top",
                                        at: "left bottom",
                                        collision: "none none"
                                    });
                                    $('#pass2').removeClass("login");
                                    $('#pass2').removeClass("errorOUT");
                                    $('#pass2').addClass("errorIN");
                                    $('#pass2').focus();
                                } else {

                                    if (pass21.length < 10 || pass21.length > 30)
                                    {
                                        error5.text("Enter 10-30 characters");
                                        error5.position({
                                            of: $("#pass21"),
                                            my: "left top",
                                            at: "left bottom",
                                            collision: "none none"
                                        });
                                        $('#pass21').removeClass("login");
                                        $('#pass21').removeClass("errorOUT");
                                        $('#pass21').addClass("errorIN");
                                        $('#pass21').focus();
                                    } else {
                                        if (pass2 !== pass21)
                                        {
                                            $('#pass21').removeClass("login errorOUT");
                                            $('#pass21').addClass("errorIN");
                                        } else {

                                            $('#pass21').removeClass("errorIN");
                                            $('#pass21').addClass("errorOUT");
                                        }
                                    }

                                }
                            }
                            error4.text("");
                            $('#email2').removeClass("errorIN");
                            $('#email2').addClass("errorOUT");
                            $('#pass2').prop('disabled', false);
                        }
                    }

                    if ($("#error4,#error5").text().length === 0) {
                        $.ajax({
                            url: URL + 'sign.php',
                            method: 'POST',
                            data: {email: mail2,
                                password: pass2
                            },
                            beforeSend: function () {
                                // Show image container
                                $("body").css("cursor", "progress");
                                $('#email2,#pass2,#pass21').prop('disabled', true);
                            },
                            success: function (data) {
                                var newdata = $.trim(data.replace(/<[^>]+>/g, ''));

                                switch (newdata) {
                                    case 'Success.Email.':
                                        $('#email2, #pass2, #pass21').val('');
                                        alert('\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\r\n\r\n\r\nWe sent you a verification link to your e-mail address.\r\nYou have 30 minutes to confirm your e-mail.\r\n\r\n\r\n\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705\u2705');
                                        break;
                                    case "Not emailed.":
                                        $('#email2, #pass2, #pass21').val('');
                                        $('#email2').removeClass("login errorOUT");
                                        $('#email2').addClass("errorIN");
                                        $('#email2').focus();
                                        alert('\u26D4\tUnfortunalely we have a problem with our email system.\r\nPlease try later or report the problem.');
                                        break;
                                    case "error":
                                        $('#email2, #pass2, #pass21').val('');
                                        $('#email2').removeClass("login errorOUT");
                                        $('#email2').addClass("errorIN");
                                        $('#email2').focus();
                                        alert('\u26D4\tUnfortunalely we have a problem with out Database. \r\nPlease try later or report the problem.');
                                        break;
                                    case "exists":
                                        $('#email2, #pass2, #pass21').val('');
                                        $('#email2').removeClass("login errorOUT");
                                        $('#email2').addClass("errorIN");
                                        $('#email2').focus();
                                        alert('\u26D4\tUser with email\r\n' + mail2 + '\r\nalready exists.');
                                        break;
                                    default:
                                        $('#email2, #pass2, #pass21').val('');
                                        $('#email2').removeClass("login errorOUT");
                                        $('#email2').addClass("errorIN");
                                        $('#email2').focus();
                                        alert('\u26D4\t' + newdata);
                                }

                            },
                            complete: function () {
                                // Hide image container
                                $("body").css("cursor", "default");
                                $('#email2,#pass2,#pass21').prop('disabled', false);
                                $('#email2').focus();
                            },
                            fail: function () {
                                $("body").css("cursor", "default");
                                alert('\u26D4\tFor some technical reasons we cannot proceed.\n Please report the problem.');
                            }
                        });
                    }

                });
//////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////

                $('#email').on('keyup contextmenu focus change', function () {
                    var email = $(this).val();
                    var error1 = $('#error1');
                    var error2 = $('#error2');
                    var pass = $('#pass');
                    $(this).removeClass('login errorOUT');
                    $(this).addClass('errorIN');
                    pass.removeClass('errorIN');
                    pass.addClass('errorOUT');
                    pass.val('');
                    error2.text('');
                    if (ValidateEmail(email) && email.length >= 6 && email.length <= 30) {
                        $(this).removeClass("errorIN");
                        $(this).addClass("errorOUT");
                        error1.text('');
                        $('#pass').prop('disabled', false);
                        $('#pass').removeClass("errorOUT login");
                        $('#pass').addClass("errorIN");
                    } else {
                        $(this).removeClass("login errorOUT");
                        $(this).addClass("errorIN");
                        $('#pass').prop('disabled', true);
                        $('#pass').removeClass('errorIN');
                        $('#pass').addClass('errorOUT');
                        $('#pass').val('');
                    }
                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $('#pass').on('keyup mouseup contextmenu focus', function () {
                    var email = $('#email').val();
                    var pass = $(this).val();
                    $('#pass').removeClass("errorIN");
                    $('#pass').addClass("errorOUT");
                    if (ValidateEmail(email) && email.length >= 6 && email.length <= 30) {
                        $('#email').removeClass("errorIN");
                        $('#email').addClass("errorOUT");
                        $('#pass').prop('disabled', false);
                    }

                    if (ValidatePassword(pass) && pass.length >= 10 && pass.length <= 30) {
                        $(this).removeClass("errorIN");
                        $(this).addClass("errorOUT");
                        $('#error2').text('');
                        $('#pass').prop('disabled', false);
                    } else {
                        $(this).removeClass("login");
                        $(this).removeClass("errorOUT");
                        $(this).addClass("errorIN");
                    }
                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $('#email2').on('keyup mouseup contextmenu focus', function () {
                    var email2 = $(this).val();
                    var error5 = $('#error5');
                    var error4 = $('#error4');
                    var pass21 = $('#pass21');
                    var pass2 = $('#pass2');
                    $(this).removeClass('login errorOUT');
                    $(this).addClass('errorIN');
                    pass2.removeClass('errorIN');
                    pass2.addClass('errorOUT');
                    error5.text('');
                    pass21.val('');
                    pass2.val('');
                    $(this).removeClass('login errorOUT');
                    $(this).addClass('errorIN');
                    pass21.removeClass('errorIN');
                    pass21.addClass('errorOUT');
                    if (ValidateEmail(email2) && email2.length >= 6 && email2.length <= 30) {
                        $(this).removeClass("errorIN");
                        $(this).addClass("errorOUT");
                        error4.text('');
                        $('#pass2').prop('disabled', false);
                    } else {
                        $(this).removeClass("login errorOUT");
                        $(this).addClass("errorIN");
                        $('#pass2, #pass21').prop('disabled', true);
                        $('#pass2, #pass21').removeClass('errorIN');
                        $('#pass2, #pass21').addClass('errorOUT');
                        $('#pass2, #pass21').val('');
                    }
                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $('#pass2').on('keyup mouseup contextmenu focus', function () {
                    var email2 = $('#email2').val();
                    var pass = $(this).val();
                    $('#pass21').prop('disabled', true);
                    $('#pass21').removeClass("errorIN");
                    $('#pass21').val('');
                    $('#pass21').addClass("errorOUT");
                    if (ValidateEmail(email2) && email2.length >= 6 && email2.length <= 30) {
                        $('#email2').removeClass("errorIN");
                        $('#email2').addClass("errorOUT");
                    }

                    if (ValidatePassword(pass) && pass.length >= 10 && pass.length <= 30) {
                        $(this).removeClass("errorIN");
                        $(this).addClass("errorOUT");
                        $('#error5').text('');
                        $('#pass21').prop('disabled', false);
                    } else {
                        $(this).removeClass("login");
                        $(this).removeClass("errorOUT");
                        $(this).addClass("errorIN");
                    }
                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $('#pass21').on('keyup mouseup contextmenu focus', function () {
                    var pass2 = $('#pass2').val();
                    var pass21 = $(this).val();
                    $('#pass21').removeClass("login errorOUT");
                    $('#pass21').addClass("errorIN");
                    $('#pass2').removeClass('errorIN');
                    $('#pass2').addClass('errorOUT');
                    if (ValidatePassword(pass2) && pass2.length >= 10 && pass2.length <= 30) {
                        if (pass21.length >= 10 && pass21.length <= 30) {
                            if (pass2 === pass21) {
                                $('#error5').text('');
                                $('#pass21').removeClass("errorIN");
                                $('#pass21').addClass("errorOUT");
                            } else {
                                $('#error5').html('Passwords are not <br>matched');
                                $('#error5').position({
                                    of: $("#pass21"),
                                    my: "center top",
                                    at: "center bottom",
                                    collision: "none none"
                                });
                                $('#pass2').removeClass('errorOUT login');
                                $('#pass2').addClass('errorIN');
                            }
                        }
                    }
                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////


                $('#area').on('keydown mouseenter', function () {
                    var count = $('#area').text().length;
                    var selL = document.getElementById("lang");
                    var selectedLang = selL.options[selL.selectedIndex].text;
                    var selQ = document.getElementById("qual");
                    var selectedQual = selQ.options[selQ.selectedIndex].text;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(count);
                    if (document.getElementById("qual").disabled)
                    {
                        if (count >= 0 && $.trim(tokenSet) === 'yes' && $.trim(session) === 'true')
                        {

                            document.getElementById("qual").disabled = false;
                            //document.getElementById("lang").disabled = false;

                            if (selectedLang !== "Select Language")
                            {
                                document.getElementById("lang").disabled = false;
                                document.getElementById("voices").disabled = false;
                                document.getElementById("audio").disabled = false;
                            }

                            if (selectedLang === "Select Language" && selectedQual !== "Select Quality") {
                                document.getElementById("lang").disabled = false;
                            }
                            if ($.trim(session) === 'true' && $.trim(tokenSet) !== 'no') {

                            }


                        }
                    }


                });
                
                $('#lang').change(function () {
                    //e.preventDefault();

                    var langID = $('#lang').val();
                    var sel = document.getElementById("lang");
                    var selectedText = sel.options[sel.selectedIndex].text;
                    var qua = $('#qual').val();
                    $.ajax({
                        url: URL + 'get_voices.php',
                        method: 'POST',
                        data: {language: langID,
                            sel_lang: selectedText,
                            quality: qua},
                        beforeSend: function () {
                            // Show image container
                            $("body").css("cursor", "progress");
                        },
                        success: function (data) {
                            if ($('#voices').prop('disabled', false)) {
                                $('#voices').html(data);
                            }
                            document.getElementById("voices").disabled = false;
                            var sel = document.getElementById("voices");
                            var selectedText = sel.options[sel.selectedIndex].text;
                            if (selectedText !== "") {
                                document.getElementById("audio").disabled = false;
                            } else {
                                alert("\u26D4\tSomething wrong with Google TTS API connection.\r\nTry later or report the problem.");
                            }
                            
                        },
                        complete: function () {
                            $("body").css("cursor", "default");
                        },
                        fail: function () {
                            $("body").css("cursor", "default");
                            alert('\u26D4\tNo connection to Google server.\r\nTry later or report the problem.');
                        }
                    });
                });
                $('#lang').on('focus mouseenter', function () {
                    var count = $('#area').text().length;
                    if (count === 0)
                    {
                        document.getElementById("qual").disabled = true;
                        document.getElementById("lang").disabled = true;
                        document.getElementById("voices").disabled = true;
                        document.getElementById("audio").disabled = true;
                        document.getElementById("area").focus();
                    }


                });
                $('#qual').change(function () {
                    // e.preventDefault();
                    var sel = document.getElementById("lang");
                    var selectedText = sel.options[sel.selectedIndex].text;
                    var qua = $('#qual').val();
                    //alert(selectedText);   
                    $.ajax({
                        url: URL + 'get_languages.php',
                        method: 'POST',
                        data: {quality: qua},
                        beforeSend: function () {
                            // Show image container
                            $("body").css("cursor", "progress");
                        },
                        success: function (data) {
                            var data1 = data.replace(/<[^>]+>/g, '');
                            var newdata = $.trim(data1);
                            if (newdata !== '') {
                                var langsOFF = newdata.split(',');
                                //alert(langsOFF);
                                $('.langOFF').each(function () {
                                    var lang = $(this).val();
                                    //alert(langsOFF);

                                    if (jQuery.inArray(lang, langsOFF) !== -1) {
                                        $(this).prop('disabled', true);
                                    }


                                });
                            } else {

                                $('.langOFF').prop('disabled', false);

                            }
                            
                            if (selectedText !== "Select Language" && $("option.langOFF:selected").prop('disabled') !== true)
                            {
                                var langID = $('#lang').val();
                                var qua = $('#qual').val();

                                $.ajax({

                                    url: URL + 'get_voices.php',
                                    method: 'POST',
                                    data: {language: langID,
                                        quality: qua},
                                    beforeSend: function () {
                                        // Show image container
                                        $("body").css("cursor", "progress");
                                    },
                                    success: function (data) {
                                        if (document.getElementById("lang").disabled) {
                                            document.getElementById("lang").disabled = false;
                                            $('#voices').prop('disabled', false);
                                        } else {
                                            $('#voices').html(data);
                                            $('#voices').prop('disabled', false);
                                        }
                                    },
                                    complete: function () {
                                        // Hide image container
                                        $("body").css("cursor", "default");
                                    }
                                });
                            } else {

                                if (document.getElementById("lang").disabled) {
                                    document.getElementById("lang").disabled = false;
                                } else {
                                    $('#voices,#audio,#lang').prop('disabled', true);
                                }
                            }
                        },
                        complete: function () {
                            // Hide image container
                            $("body").css("cursor", "default");
                        }
                    });
                });

                $('#qual').on('focus mouseenter mouseleave', function () {
                    //e.preventDefault();
                    var count = $('#area').text().length;
                    //alert(count);
                    if (count === 0 || session === 'false')
                    {
                        document.getElementById("qual").disabled = true;
                        document.getElementById("lang").disabled = true;
                        document.getElementById("voices").disabled = true;
                        document.getElementById("audio").disabled = true;
                        document.getElementById("area").focus();
                    }


                });
                $('#voices').on('focus mouseenter', function () {
                    var count = $('#area').text().length;
                    //alert(count);
                    if (count === 0)
                    {
                        document.getElementById("qual").disabled = true;
                        document.getElementById("lang").disabled = true;
                        document.getElementById("voices").disabled = true;
                        document.getElementById("audio").disabled = true;
                        document.getElementById("area").focus();
                    }

                });
                $('#audio').on('focus mouseenter', function () {
                    var count = $('#area').text().length;
                    //alert(count);
                    if (count === 0)
                    {
                        document.getElementById("qual").disabled = true;
                        document.getElementById("lang").disabled = true;
                        document.getElementById("voices").disabled = true;
                        document.getElementById("audio").disabled = true;
                        document.getElementById("area").focus();
                    } else {
                        var text = $('#area').text();
                        $("#hiddenSSML").val(text);
                        if ($("#audio").attr("disabled"))
                        {
                            $("#audio").removeClass("audioHover");
                            $("#audio").addClass("audio");
                        } else {
                            $("#audio").removeClass("audio");
                            $("#audio").addClass("audioHover");
                        }
                        console.log($("#hiddenSSML").val());
                    }
                });
                $('#audio').on('mouseleave', function () {

                    if ($("#audio").attr("disabled"))
                    {

                    } else {
                        $("#audio").removeClass("audioHover");
                        $("#audio").addClass("audio");
                    }

                });
                $('#audio').on('click', function () {
                    var text = $('#hiddenSSML').val();
                    var langID = $('#lang').val();
                    var voiceID = $('#voices').val();
                    var auto    = $('#auto').prop('checked');
                    var qual = document.getElementById("qual");
                    var selectedQual = qual.options[qual.selectedIndex].text;
                    var quality = selectedQual;
                    if (text.length > 4985) {
                        alert('You exceeded maximum allowed 5000 characters.\r\nAdditional 15 characters are added for auto generated "<speak></speak>" tags');
                    } else {
                        if (myToken !== '') {

                            if (quality === 'Premium') {
                                var qualPrice = <?php echo $wave; ?>;
                            } else {
                                if (quality === 'Standard') {
                                    var qualPrice = <?php echo $standard; ?>;
                                }
                            }
                            var checkedClass = $("input[type=radio]:checked").attr('class');
                            var myClass = checkedClass.substr(7);
                            var funds = parseFloat($('#funds' + myClass).val());
                            var chars_requested = text.length + 15;
                            var max_chars = Math.trunc((funds / qualPrice).toFixed(2));
                            //alert('Max chars: '+ max_chars +' > ' + 'chars requested: '+ chars_requested);
                            if (max_chars >= chars_requested) {
                                //alert('characters: '+ chars+' quality: '+selectedQual+' \r\n hash: '+ myToken);

                                $.ajax({
                                    url: URL + 'transaction.php',
                                    method: 'POST',
                                    dataType: 'text',
                                    data: {characters: text,
                                        hash: myToken,
                                        quality: selectedQual
                                    },
                                    before: function () {
                                        $("body").css("cursor", "progress");
                                    },
                                    success: function (data) {
                                        function toFixedTrunc(x, n) {
                                            const v = (typeof x === 'string' ? x : x.toString()).split('.');
                                            if (n <= 0)
                                                return v[0];
                                            let f = v[1] || '';
                                            if (f.length > n)
                                                return `${v[0]}.${f.substr(0, n)}`;
                                            while (f.length < n)
                                                f += '0';
                                            return `${v[0]}.${f}`;
                                        }
                                        var newdata = $.trim(data.replace(/<[^>]+>/g, ''));
                                        $("body").css("cursor", "cursor");

                                        if (newdata.substr(0, 7) === 'updated') {
                                            audioFetch(langID, voiceID, text, auto);
                                            var variables = newdata.substr(7).split(',', 4);
                                            var balance = parseFloat(variables[0]);
                                            var newBalance = toFixedTrunc(balance, 2);
                                            var newTotalChars = variables[1];
                                            var hash = variables[2];
                                            var savedChars = parseInt(variables[3]);
                                            var savedBucks = toFixedTrunc(qualPrice * savedChars, 5);
                                            $("input[class^='payment']").each(function () {

                                                if ($(this).attr('value') === hash) {
                                                    var checkedClass = $(this).attr('class');
                                                    var myClass = checkedClass.substr(7);
                                                    $('#newBalance' + myClass).text(newBalance);
                                                    $('#newTotalChars' + myClass).text(newTotalChars);
                                                    $('#funds' + myClass).val(newBalance);
                                                    if (savedChars > 0) {
                                                        alert('We removed ' + savedChars + ' unnecessary spaces\r\nAnd saved you $' + savedBucks + '.');
                                                    } else {

                                                    }
                                                }

                                                //alert('Success: ' + newdata);  
                                            });
                                        } else {
                                            alert(newdata + ' . Please report the problem.');
                                            //("body").css("cursor", "cursor");


                                        }
                                    },
                                    fail: function (data) {
                                        var newdata = $.trim(data.replace(/<[^>]+>/g, ''));
                                        $("body").css("cursor", "cursor");
                                        alert('Fail: ' + newdata);
                                    }
                                });
                            } else {
                                if (max_chars > 15) {

                                    alert('\u26D4 You have insufficient funds. \r\n\tPlease buy a token or reduce a number of characters to ' + max_chars + '.');
                                } else {
                                    alert('\u26D4 You have insufficient funds. \r\n\tPlease buy or choose another token.');
                                }
                            }
                        } else {
                            alert('\u26D4 Please select a payment token from tokens\' list.');
                        }
                    }
                });
                if ($.trim(session) === 'true' && $.trim(tokenSet === 'yes')) {
                    $(".SpanLogged03").position({
                        of: $("#CellLogged05"),
                        my: "left+5px top-5px",
                        at: "left top",
                        collision: "none none"
                    });
                    
                    if($('.container').css('display') === 'block'){
                    $(".SpanLogged03").css({"opacity": 0.5});
                    $(".SpanLogged03").on('mouseenter', function () {
                    $('.SpanLogged03').css({"color": 'red','cursor':'pointer'});
                    });
                    $(".SpanLogged03").on('mouseleave', function () {
                    $('.SpanLogged03').css({"color": 'black'});
                    });
                    }
                }
               

                $(".control").position({
                    of: $("#area"),
                    my: "left top",
                    at: "left bottom",
                    collision: "none none"
                });
                $("#char_counter").position({
                    of: $("#area"),
                    my: "left bottom",
                    at: "left+20px top",
                    collision: "none none"
                });
                $('#toggle_ssml').position({
                    of: $("#area"),
                    my: "right bottom",
                    at: "right top",
                    collision: "none none"
                });
                $('#toggle_text').position({
                    of: $("#toggle_ssml"),
                    my: "right bottom",
                    at: "left bottom",
                    collision: "none none"
                });
                $('#auto').position({
                            of: $("#toggle_text"),
                            my: "right-10px bottom",
                            at: "left bottom",
                            collision: "none none"
                        });
                //alert(rect.right + " " + rect_tog.right);

                $('#ssml_panel').position({
                    of: $("#area"),
                    my: "left top",
                    at: "right top",
                    collision: "none none"
                });
                
               
                
                $('#area').on('mousemove focus', function () {
                    var control = document.getElementById("control");
                    var rect_control = control.getBoundingClientRect();
                    var area = document.getElementById("area");
                    var rect = area.getBoundingClientRect();
                    var tog = document.getElementById("toggle_ssml");
                    var rect_tog = tog.getBoundingClientRect();
                    var rect_cont = tog.getBoundingClientRect();
                    var h_area = $("#area").height();
                    var h_cont = $("#ssml_panel").height();
                    var entered = $(this).text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                    if (rect.right !== rect_tog.right) {
                        $("#toggle_ssml").position({
                            of: $("#area"),
                            my: "right bottom",
                            at: "right top",
                            collision: "none none"
                        });
                        $('#toggle_text').position({
                            of: $("#toggle_ssml"),
                            my: "right bottom",
                            at: "left bottom",
                            collision: "none none"
                        });
                        $('#auto').position({
                            of: $("#toggle_text"),
                            my: "right-10px bottom",
                            at: "left bottom",
                            collision: "none none"
                        });
                    }

                    if (rect.right !== rect_cont.left || rect.top !== rect_cont.top) {
                        $("#ssml_panel").position({
                            of: $("#area"),
                            my: "left top",
                            at: "right top",
                            collision: "none none"
                        });
                    }

                    if (h_area !== h_cont) {
                        $('.container').css({'height': +h_area + 'px'});
                    }

                    if (rect_control.top <= rect.bottom || rect_control.top >= rect.bottom) {
                        $("#control").position({
                            of: $("#area"),
                            my: "left top",
                            at: "left bottom",
                            collision: "none none"
                        });
                    }
                });
                
                $('#toggle_text,.SpanLogged03').click(function () {
                    $('#area').attr({'spellcheck': true, 'autocorrect': true, 'autocapitalize': true});
                    $('#toggle_text').css({"color": "blue", "border-color": "dodgerblue", "border-width": "1px"});
                     $('.SpanLogged03').css({'opacity':1,'color':'black'});
                     $(".SpanLogged03").on('mouseenter', function () {
                    $('.SpanLogged03').css({"color": 'black','cursor':'text'});
                    });
                    
                    $('#toggle_ssml').css({"color": "black", "border-color": "grey", "border-width": "1px"});
                    $('.container').fadeOut(400);
                    $('#area').css({'background-color': '#2d2d2d', 'color': 'white', 'font-size': '18px', 'font-family': 'Consolas'});
                    document.getElementById("area").focus();
                    if ($.trim(session) === 'true' && $.trim(tokenSet) !== 'no') {
                        $.ajax({
                            url: URL + 'variables.php',
                            method: 'POST',
                            dataType: 'text',
                            data: {toggle_text: 'toggle_text'
                            },
                            success: function () {

                            }
                        });
                    }

                });
                $('#toggle_ssml').click(function () {
                    $('.SpanLogged03').css({'opacity':0.5});
                    var h_area = $("#area").height();
                    var w_area = $("#area").width();
                    $('#area').attr({'spellcheck': false, 'autocorrect': false, 'autocapitalize': false});
                    $(this).css({"color": "blue", "border-color": "dodgerblue", "border-width": "1px"});
                    $(".SpanLogged03").on('mouseenter', function () {
                    $('.SpanLogged03').css({"color": 'red','cursor':'pointer'});
                    });
                    $(".SpanLogged03").on('mouseleave', function () {
                    $('.SpanLogged03').css({"color": 'black'});
                    });
                    $('#toggle_text').css({"color": "black", "border-color": "grey", "border-width": "1px"});
                    $('.container').fadeIn(400);
                    $('.container').css({'padding': '20px', 'height': +h_area + 'px'});
                    $('#area').css({'background-color': '#2d2d2d', 'color': 'white', 'font-size': '18px', 'font-family': 'Consolas', 'height': +h_area + 'px', 'width': +w_area + 'px'});
                    document.getElementById("area").focus();
                    $('#ssml_panel').position({
                        of: $("#area"),
                        my: "left top",
                        at: "right top",
                        collision: "none none"
                    });
                    if ($.trim(session) === 'true' && $.trim(tokenSet) !== 'no') {
                        $.ajax({
                            url: URL + 'variables.php',
                            method: 'POST',
                            dataType: 'text',
                            data: {toggle_ssml: 'toggle_ssml'
                            },
                            success: function () {

                            }
                        });
                    }
                });
                $('#ssml_panel').mousemove(function () {
                    $(this).position({
                        of: $("#area"),
                        my: "left top",
                        at: "right top",
                        collision: "none none"
                    });
                });
                $('.btnBreak').mouseenter(function () {
                    $(this).addClass("btnBreakHover");
                });
                $('.btnBreak').mouseleave(function () {
                    $(this).removeClass("btnBreakHover");
                    $(this).addClass("btnBreak");
                });
                $('.btnBreak').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">break</span><span class="innerTag2"> time</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">/&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, ''));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                    $('.btnEmphasis').mouseenter(function () {
                            $(this).addClass("btnEmphasisHover");
                    });
                    $('.btnEmphasis').mouseleave(function () {
                        $(this).removeClass("btnEmphasisHover");
                        $(this).addClass("btnEmphasis");
                    });
                
                $('.btnEmphasis').on('click keypress', function () {

                    var value = $('#displayEmphasis').text();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">emphasis</span> <span class="innerTag2">level</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">emphasis</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'REPLACE THIS TEXT'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                    $('.btnVolume').mouseenter(function () {
                            $(this).addClass("btnVolumeHover");
                    });
                    $('.btnVolume').mouseleave(function () {
                        $(this).removeClass("btnVolumeHover");
                        $(this).addClass("btnVolume");
                    });
                $('.btnVolume').on('click keypress', function () {
                   var value = $('#displayVolume').text();
                   if(value === 'x-loud'){
                       value = '24dB';
                   }
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">prosody</span> <span class="innerTag2">volume</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">prosody</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'REPLACE THIS TEXT'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                    $('.btnRate').mouseenter(function () {
                            $(this).addClass("btnRateHover");
                    });
                    $('.btnRate').mouseleave(function () {
                        $(this).removeClass("btnRateHover");
                        $(this).addClass("btnRate");
                    });
               
                $('.btnRate').on('click keypress', function () {

                    var value = $('#displaySpeed').text();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">prosody</span> <span class="innerTag2">rate</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">prosody</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'REPLACE THIS TEXT'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                    $('.btnPitch').mouseenter(function () {
                            $(this).addClass("btnPitchHover");
                    });
                    $('.btnPitch').mouseleave(function () {
                        $(this).removeClass("btnPitchHover");
                        $(this).addClass("btnPitch");
                    });
                $('.btnPitch').on('click keypress', function () {
                    var value = $('#displayPitch').text();
                    if(value === 'x-low'){
                        value = '-24st';
                    } else {
                        if(value === 'x-high'){
                            value = '24st';
                        }
                    }
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">prosody</span> <span class="innerTag2">pitch</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">prosody</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'REPLACE THIS TEXT'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                    $('.btnSayAs').mouseenter(function () {
                            $(this).addClass("btnSayAsHover");
                    });
                    $('.btnSayAs').mouseleave(function () {
                        $(this).removeClass("btnSayAsHover");
                        $(this).addClass("btnSayAs");
                    });
                $('#number.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '12345'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#digits.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '12345'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#SpellOut.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'SPELL'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#fraction.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '2 1/4'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#ordinal.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '4'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#unit.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '10 foot'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#bleep.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, 'CENSORED'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#dateMMDDYYYY.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span> <span class="innerTag2">format</span><span class="equalTag">=</span><span class="valueTag">"mmddyyyy"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '12-31-2020'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#dateMD.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span> <span class="innerTag2">format</span><span class="equalTag">=</span><span class="valueTag">"md"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '12-9'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#timeHMS12.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span> <span class="innerTag2">format</span><span class="equalTag">=</span><span class="valueTag">"hms12"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '14:30'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#timeHMS24.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span> <span class="innerTag2">format</span><span class="equalTag">=</span><span class="valueTag">"hms24"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '2:30pm'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                $('#telephone.btnSayAs').on('click keypress', function () {
                    var value = $(this).val();
                    var openTag = '<span class="tag"><span class="outerTag">&lt</span><span class="innerTag1">say-as</span> <span class="innerTag2">interpret-as</span><span class="equalTag">=</span><span class="valueTag">"' + value + '"</span><span class="outerTag">&gt</span>';
                    var closeTag = '<span class="outerTag">&lt/</span><span class="innerTag1">say-as</span><span class="outerTag">&gt</span></span>';
                    $("#area").focus().html(pasteHtmlAtCaret(openTag, closeTag, '(581) 912-1234'));
                    var entered = $('#area').text().length;
                    $('#char_result1').text('Characters entered: ');
                    $('#char_result2').text(entered);
                });
                if ($.trim(tokenSet) === 'yes') {
                    $('.btnAudio').mouseenter(function () {
                        if (session === 'true')
                            $(this).addClass("btnAudioHover");
                    });
                    $('.btnAudio').mouseleave(function () {
                        $(this).removeClass("btnAudioHover");
                        $(this).addClass("btnAudio");
                    });
                }
                
                $('#signout').on('click keypress', function () {
                    $.ajax({
                        url: URL + 'signout.php',
                        beforeSend: function () {
                            // Show image container
                            $("body").css("cursor", "progress");
                        },
                        success: function (data) {
                            $('.TableLog').css({"display": "block"});
                            $('.TableLogged').css({"display": "none"});
                            $('#area').text('');
                            session = 'false';
                            $('.main,#audio').attr({'disabled': true, 'title': ssmlButtonsDisabled});
                            
                            //var newdata = $.trim(data.replace(/<[^>]+>/g, ''));
                            $('#email,#pass').val('');
                        },
                        complete: function () {
                            // Hide image container
                            $("body").css("cursor", "default");
                        }


                    });
                });
                $('#buyToken').on('click', function () {
                    if ($(this).text() === 'Buy token') {
                        $("#sum2").val("0").select();
                        $('.sum3,.sum4').hide();
                        $(this).text('Cancel').fadeIn(400);
                    } else {
                        $(this).text('Buy token');
                    }
                    $('.total1').slideToggle(400);
                });
                $('#sum2').on('change', function () {
                    $('.total2').show();
                    $('.sum3,.sum4').show(400);
                    $('#sum2').focusout();
                    $('#paypal-button').focus();
                });
                $('.even,.odd').on('click', function () {

                    $(this).find('input.payment').on('click', function () {
                    });
                    if ($(this).find('.subiteration').length > 0) {
                        $('.even,.odd').find('.subiterationOn').not(this).slideToggle(400);
                        $('.even,.odd').find('div').not(this).removeClass('subiterationOn');
                        $('.even,.odd').find('div').not(this).addClass('subiteration');
                        $(this).find('.subiteration').slideToggle(400);
                        $(this).find('div').removeClass('subiteration');
                        $(this).find('div').addClass('subiterationOn');
                        //var myClass = $(this).find('div').attr("class");
                        //alert('First: ' + myClass + ' lenght:' + $(this).find('div').length);
                        //$(this).find('.subiterationOn').slideToggle(200);
                    } else {
                        //$('.even,.odd').find('.subiterationOn').not(this).hide();
                        $(this).find('.subiterationOn').slideToggle(400);
                        $(this).find('div').removeClass('subiterationOn');
                        $(this).find('div').addClass('subiteration');
                    }

                });
                $('.iteration2').on('click', function () {
                    if ($(this).find('input').prop('checked')) {
                        $(' input[type=radio]:checked').not(this).prop("checked", false);
                        $(this).find('input').prop("checked", true);
                        var checkedClass = $("input[type=radio]:checked").attr('class');
                        myToken = $('input[type=hidden].' + checkedClass).attr('value');
                    }
                });
                $(function () {
                    $("#accordion").accordion({
                        heightStyle: "content",
                        active: false,
                        collapsible: true
                    });
                });
                $('.li-hide').on('click', function () {
                    $('#area').attr({'spellcheck': true, 'autocorrect': true, 'autocapitalize': true});
                    $('#toggle_text').css({"color": "blue", "border-color": "dodgerblue", "border-width": "1px"});
                    $('#toggle_ssml').css({"color": "black", "border-color": "grey", "border-width": "1px"});
                    $('.container').fadeOut(400);
                    $('#area').css({'background-color': '#2d2d2d', 'color': 'white', 'font-size': '18px', 'font-family': 'Consolas'});

                });

            });

            $(function () {
                $(document).tooltip();
            });

        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices. -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet  -->
        <noscript>
        <style type="text/css">
            .pagecontainer {display:none;}
        </style>
    <div class="noscriptmsg">
        <h2>You don't have JavaScript enabled.  <br>This application won't work properly.
            </div>
            </noscript>
            </head>
            <body> 
                <div class="pagecontainer">
                    <script src="https://www.paypal.com/sdk/js?client-id=AZ81LfZOD2kuNFjB266CWXmmj6EqFjUNp5A5BtFWX97LVcZsDquSKbenQdQybhWWBddQsxKfVd651spO&currency=USD&commit=true"></script>
                    <div  class="logo">
                        <img src="uvoicer-logo.jpg" width="300px" height="59px">
                    </div>
                    <div  class="logo1">
                        <span class="tts">TEXT-TO-SPEECH</span>
                    </div>
                    <div id="tabs" class="tabs">
                        <ul>
                            <li><a href="#fragment-1"><span class="ui-icon ui-icon-key"></span><span>Login</span></a></li>
                            <li><a href="#fragment-2"><span class="ui-icon ui-icon-pencil"></span><span>Sign up</span></a></li>
                            <li class='li-hide'><a href="#fragment-3"><span class="ui-icon ui-icon-video"></span><span>Video Guide</span></a></li>
                            <li class='li-hide'><a href="#fragment-4"><span class="ui-icon ui-icon-cart"></span><span>Price</span></a></li>
                            <li class='li-hide'><a href="#fragment-5"><span class="ui-icon ui-icon-info"></span><span>FAQ</span></a></li>
			    <li class='li-hide'><a href="#fragment-6"><span class="ui-icon ui-icon-heart"></span><span>Referral</span></a></li>
                            <li><a href="#fragment-7"><span class="ui-icon ui-icon-mail-closed"></span><span>Contact</span></a></li>
                        </ul>

                        <div class="tabs-background"></div>
                        <div id="fragment-1" class="fragment"> 
                            <div class="TableLogged">
                                <div class="RowLogged">
                                    <div class="CellLogged" id="CellLogged01">
                                        <button  id="buyToken" class="signout">Buy token</button> <span id="show_log"></span>
                                    </div>
                                    <div class="CellLogged" id="CellLogged02">
                                        <button  id="signout" class="signout">Sign out</button>
                                    </div>
                                </div>
                                <div class="RowLogged">
                                    <div class="CellLogged" id="CellLogged03">

                                        <div class="total1">
                                            <center><div  class="paypal" id="sum1" style="padding-top:10px">Select Token Value</div>
                                                <div style="padding-bottom:30px" class="paypal"><select name="sum"  id="sum2" class="sum">  
                                                        <option value="0" selected disabled>--- $USD --</option>
                                                        <option value="5">--- $5 --</option>
                                                        <option value="10">-- $10 --</option>
                                                        <option value="20">-- $20 --</option>
                                                        <option value="50">-- $50 --</option>
                                                    </select></div> 
                                                <div class="total2">
                                                    <center> <div  class="sum3">Final step: pay with PayPal</div>
                                                        <div id="paypal-button-container"  class="sum4" style="padding-top:10px; padding-bottom:30px"></div>
                                                </div></div>
                                        </center>
                                    </div>
                                    <div class="CellLogged" id="CellLogged04"> 
                                    </div>
                                </div>
                                <div class="RowLogged">
                                    <div class="CellLogged" id="CellLogged05">
                                       <span class="SpanLogged03"></span>
                                        <?php
                                        if (isset($_SESSION['tokenInfo'])) {
                                            $i = 1;
                                            foreach ($_tokens as $token):
                                                ?>
                                                <span class="iteration2"><input type="radio" id="radioToken" class="payment<?php echo $i ?>" value="Pay" <?php
                                                    if ($i === 1) {
                                                        echo ' checked';
                                                    }
                                                    ?>></span>
                                                <div class="<?php
                                                if ($i % 2 === 0) {
                                                    echo 'even';
                                                } else {
                                                    echo 'odd';
                                                }
                                                ?>"><span class="iteration"><?php echo $i . '.'; ?></span>
                                                    <span class="innerToken">Token<?php echo $i . ' '; ?><?php echo '$' . $token[2]; ?></span>

                                                    <div class="subiteration"><span class="spansubiteration1">Token's balance:</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="spansubiteration2" id="newBalance<?php echo $i; ?>"><?php echo '$' . withoutRounding($token[3], 2); ?></span>
                                                        <br><span class="spansubiteration1">Characters bought:</span><span class="spansubiteration2" id="newTotalChars<?php echo $i; ?>"><?php echo number_format($token[4]); ?></span>
                                                        <br><span class="spansubiteration1">Purchase's date:</span>&nbsp;&nbsp;<span class="spansubiteration2">
                                                            <?php
                                                            $new_datetime = DateTime::createFromFormat("Y-m-d H:i:s", $token[6]);
                                                            echo $new_datetime->format('M d,Y');
                                                            ?></span>
                                                        <input type='hidden' class='payment<?php echo $i ?>' value=<?php echo '"' . $token[1] . '"' ?>> 
                                                        <input type='hidden' id='funds<?php echo $i++ ?>' value=<?php echo withoutRounding($token[3], 2); ?>></div>
                                                </div>
                                            <?php endforeach; ?><?php } ?>

                                    </div>
                                    <div class="CellLogged" id="CellLogged06"> 
                                    </div>
                                </div>
                            </div>

                            <div class="TableLog">
                                <div class="RowLog">
                                    <div class="CellLog">
                                        <div class="label">
                                            <label for="email">1. Email</label>
                                            <input type="email" id="email" class="login"   maxlength="30"  placeholder="Enter e-mail">
                                        </div>
                                    </div>
                                    <div class="CellLog">
                                        <div class="label">
                                            <label for="pass" autocomplete="off">2. Password</label>
                                            <input type="password" id="pass" class="login"  maxlength="30"  placeholder="Enter password">
                                        </div>
                                    </div>
                                    <div class="CellLog">
                                        <div class="label">
                                            <label for="log">3. Log in</label>
                                            <button  id="log" class="login">Log in</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="RowLog">
                                    <div class="CellLog"><span id="error1" class="error"></span> </div>
                                    <div class="CellLog"><span id="error2" class="error"></span></div>
                                    <div class="CellLog" style="padding-bottom:15px"><span id="error3" class="error"></span></div>
                                </div>
                            </div>
                        </div>
                        <div id="fragment-2" class="fragment">
                            <div class="TableSign">
                                <div class="RowSign">
                                    <div class="CellSign">
                                        <div class="label">
                                            <label for="email2">1. Email</label>
                                            <input type="email" id="email2" class="login"  maxlength="30"  placeholder="Enter e-mail" tabindex="1">
                                        </div>
                                    </div>
                                    <div class="CellSign">
                                        <div class="label">
                                            <label for="pass2" autocomplete="off">2.Password</label>
                                            <input type="password" id="pass2" class="login"   maxlength="30"  placeholder="Enter password" tabindex="2">
                                        </div>
                                    </div>

                                    <div class="CellSign">
                                        <div class="label">
                                            <label for="sign">4. Sign up</label>
                                            <button  id="sign" class="login" tabindex="4">Sign up</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="RowSign">
                                    <div class="CellSign"><span  class="error"></span> </div>
                                    <div class="label">
                                        <label for="pass21" autocomplete="off">3. Re-enter password</label>
                                        <div class="CellSign"><input type="password" id="pass21" class="login"   maxlength="30"  placeholder="Re-enter password" tabindex="3"></span></div>
                                    </div>
                                    <div class="CellSign"><span  class="error"></span></div>
                                </div>
                                <div class="RowSign">
                                    <div class="CellSign"><span  id="error4" class="error"></span> </div>
                                    <div class="CellSign"><span  id="error5" class="error"></span></div>
                                    <div class="CellSign"><span  class="error"></span></div>
                                </div>     
                            </div>
                        </div>
                        <div id="fragment-3" class="fragment">
                            <video width="100%" id="videoplayer" controls>
                                <source src='video/uvoicer.mp4'>
                            </video> 
                        </div>
                        <div id="fragment-4" class="fragment">
                            <p  class="faq">
                                You pay for what you use. No subscription fees. <br>
                                To use our service you need to sign up and buy a token.<br><br>
                                Tokens are available in <b>$5 USD, $10 USD, $20 USD and $50 USD</b> values.<br><hr>
                            We charge on per character basis. All voices come in two qualities - <b>Standard</b> and <b>Premium</b><br>
                            So, we have two rates:</b>.</p>
                            <ul>
                                <li class="faq"><b>Standard</b> - $0.00002 per character or $0.02 per 1,000 characters.</li>
                                <li class="faq"><b>Premium</b> -  $0.00005 per character or $0.05 per 1,000 characters.</li>
                            </ul>
                            For instance, for one $5 token you can buy 250,000 characters (up to 80 pages) of <b>Standard</b> quality or 100,000 characters of <b>Premium</b> quality.                  
                            </p>
                        </div>
                        <div id="fragment-5" class="fragment">FAQ
                            <div id="accordion">
                                <h3>What services Uvoicer.com offers?</h3>
                                <div>
                                    <p  class="faq">We do offer Text-To-Speech simple solution for those who need to convert text to a high quality voice.
                                    <p class="faq"><br>For example, if you have online course or Youtube video, but don't have voice/narration skills - the service is for you.</p>
                                </div>
                                <h3>How to start using Uvoicer.com?</h3>
                                <div>
                                    <p class="faq">In order to use our Text-To-Speech services you need:</p>
                                    <ul>
                                        <li class="faq">Signup and open an account with us.</li>
                                        <li class="faq">Buy a token/s of one the following values: $5, $10, $20, $50</li>
                                    </ul>
                                    </p>
                                </div>
                                <h3>Do you have trial or free versions?</h3>
                                <div>
                                    <p  class="faq">No, we don't have trial/free versions. You must buy a token from as low as $5. <br><br>
                                        We highly advise
                                        to try and experiment with SSML language using Google free tool before switching to paid service. 
                                        <a href="https://cloud.google.com/text-to-speech#section-2" target="_blank">https://cloud.google.com/text-to-speech#section-2</a><br><br></p>
                                </div>
                                <h3>How many words I can buy per token?</h3>
                                <div>
                                    <p class="faq">We charge on per character basis. All voices come in two qualities - <b>Standard</b> and <b>Premium</b><br>
                                        So, we have two rates:</b>.</p>
                                    <ul>
                                        <li class="faq"><b>Standard</b> - $0.00002 per character or $0.02 per 1,000 characters.</li>
                                        <li class="faq"><b>Premium</b> -  $0.00005 per character or $0.05 per 1,000 characters.</li>
                                    </ul>
                                    <p class="faq">For instance, for one $5 token you can buy 250,000 characters of <b>Standard</b> quality or 100,000 characters of <b>Premium</b> quality.</p>
                                </div>
                                <h3>How many Languages and Voices do you support?</h3>
                                <div>
                                    <p class="faq">We support 40 languages and provide you with 235+ voices.
                                    <ol><b>
                                            <li class="faq"> English (US)
                                            <li class="faq">English (Australia)
                                            <li class="faq">English (India)
                                            <li class="faq">English (UK)
                                            <li class="faq">Arabic
                                            <li class="faq">Bengali (India)
                                            <li class="faq">Czech   (Czech Republic)
                                            <li class="faq">Danish  (Denmark)
                                            <li class="faq">Dutch   (Netherlands)
                                            <li class="faq">Filipino  (Philippines)
                                            <li class="faq">Finnish (Finland)
                                            <li class="faq">French  (Canada)
                                            <li class="faq">French  (France)
                                            <li class="faq">German  (Germany)
                                            <li class="faq">Greek   (Greece)
                                            <li class="faq">Gujarati (India)
                                            <li class="faq">Hindi    (India)
                                            <li class="faq">Hungarian  (Hungary)
                                            <li class="faq">Indonesian  (Indonesia)
                                            <li class="faq">Italian (Italy)
                                            <li class="faq">Japanese (Japan)
                                            <li class="faq">Kannada (India)
                                            <li class="faq">Korean  (South Korean)
                                            <li class="faq">Malayalam (India)
                                            <li class="faq">Mandarin   (China)
                                            <li class="faq">Mandarin  (Taiwan)
                                            <li class="faq">Norwegian  (Norway)
                                            <li class="faq">Polish (Poland)
                                            <li class="faq">Portuguese  (Brazil)
                                            <li class="faq">Portuguese  (Portugal)
                                            <li class="faq">Russian (Russia)
                                            <li class="faq">Slovak (Slovakia)
                                            <li class="faq">Spanish (Spain)
                                            <li class="faq">Swedish  (Sweden)
                                            <li class="faq">Tamil (India)
                                            <li class="faq">Telugu (India)
                                            <li class="faq">Thai (Thailand)
                                            <li class="faq">Turkish (Turkey)
                                            <li class="faq">Ukrainian  (Ukraine)
                                            <li class="faq">Vietnamese (Vietnam)
                                        </b></ol>
                                    </p>
                                </div>
                                <h3>What is SSML feature?</h3>
                                <div>
                                    <p  class="faq">SSML stands for Speech Synthesis Markup Language and allows more customization in your audio response by providing details on pauses, 
                                        and audio formatting for acronyms, dates, times, abbreviations, or text that should be censored.<br><br>
                                        For quick tutorial please refer to <a href="https://developer.nexmo.com/voice/voice-api/guides/customizing-tts" target="_blank">https://developer.nexmo.com/voice/voice-api/guides/customizing-tts</a></p>
                                </div>
                                <h3>Do you count SSML tags as paid text?</h3>
                                <div>
                                    <p  class="faq">Yes. We do count SSML tags when calculating total price of each MP3 file downloaded.</p>
                                </div>
                                <h3>How large my text might be?</h3>
                                <div>
                                    <p  class="faq">You text, including SSML tags, might be as large as 5,000 characters per one MP3 download.<br><br> We reserve 15 
                                        characters for automatically added '<b>&lt;speak&gt;&lt;/speak&gt;</b>' tags. Once you enter over 4985 characters, application will issue a 
                                        'maximum characters exceeded' warning.</p>
                                </div>
                                <h3>What does a 'text' button mean?</h3>
                                <div>
                                    <p  class="faq">The 'Text' button means, that you can enter a plain text without SSML tags. The outputed MP3 file 
                                        will sound more generic than SSML-enhanced one, but will require less efforts on your behalf.</p>
                                </div>
                                <h3>Do you charge more for SSML feature than for plain Text?</h3>
                                <div>
                                    <p  class="faq">No. We do equally charge for both SSML and Text features. Higher costs involved only for Premium enhanced quality.</p>
                                </div>
                                <h3>What are the benefits of using Uvoicer.com?</h3>
                                <div>
                                    <p  class="faq">
                                    <ul>
                                        <li class="faq">We don't charge monthly fees. You only pay for what you use.</li>
                                        <li class="faq">Simple to use interface.</li>
                                        <li class="faq">Free and fullly enabled SSML editor for paid users.</li>
                                        <li class="faq">Automatic removal of unnecessary/extra spaces to reduce your final cost.</li>
                                        <li class="faq">Support of 40 languages with 235+ voices - one of the richest selection on the market.</li>
                                        <li class="faq">Secure transactions over PayPal.</li>
                                        <li class="faq">We don't collect any sensitive information from you.</li>
                                    </ul></p>
                                </div>
                            </div>
                        </div>

			<div id="fragment-6" class="fragment">
                            <p  class="referral">
                                Referral
                            </p>
                        </div>			

                        <div id="fragment-7" class="fragment">
                            <p  class="faq">
                                Email address: support@uvoicer.com
                            </p>
                        </div>
                    </div>
                    <div id="char_counter">
                        <span id="char_result1" class="char_result1">Characters entered: </span><span id="char_result2" class="char_result2">0</span>
                    </div>
                    <input type="checkbox" id="auto" class="auto"><button id="toggle_text" name="toggle_text" class="text">text</button><button id="toggle_ssml" name="toggle_ssml" class="ssml">ssml</button><br>
                    <div class="mainDiv">           
                        <div class="area" id="area"></div>
                        <div class="container" id="ssml_panel">
                            <div class="Table">
                                <div class="Title">
                                    SSML EDITOR<br>
                                </div>
                                <div class="Row">
                                    <div class="Cell">
                                        <p class="btnHeading" id="break">Break:</p>
                                        <p><button class="btnBreak" value="50ms">50ms</button>
                                            <button class="btnBreak" value="100ms">100ms</button>
                                            <button class="btnBreak" value="200ms">200ms</button>
                                            <button class="btnBreak" value="500ms">500ms</button>
                                            <button class="btnBreak" value="750ms">750ms</button>
                                            <button class="btnBreak" value="1s">1s</button>
                                            <button class="btnBreak" value="2s">2s</button>
                                            <button class="btnBreak" value="3s">3s</button>
                                            <button class="btnBreak" value="5s">5s</button>
                                        </p><br>
                                    </div></div>
                                <div class="Row">
                                    <div class="Cell">
                                        <p class="btnHeading">Pitch:<span id="displayPitch" class="display"></span></p>
                                        <p><div class="slidecontainer">
                                            <input type="range" min="-24" max="24" value="0" step="1" class="slider" id="sliderPitch">
                                            <button class="btnPitch">add</button>
                                        </div>
                                        </p><br>
                                    </div></div>
                                <div class="Row">
                                    <div class="Cell">
                                        <p class="btnHeading">Speed:<span id="displaySpeed" class="display"></span></p>
                                        <p><div class="slidecontainer">
                                            <input type="range" min="1" max="200" value="100" step="1" class="slider" id="sliderSpeed">
                                            <button class="btnRate">add</button>
                                        </div>
                                        </p><br>
                                    </div>

                                </div>

                                <div class="Row">
                                    <div class="Cell" id="volume">
                                       <p class="btnHeading">Volume:<span id="displayVolume" class="display"></span></p>
                                        <p><div class="slidecontainer">
                                            <input type="range" min="-24" max="24" value="0" step="1" class="slider" id="sliderVolume">
                                            <button class="btnVolume">add</button>
                                        </div>
                                        </p><br>
                                    </div>                 
                                </div>
                                <div class="Row">
                                    <div class="Cell">
                                        <p class="btnHeading">Emphasis:<span id="displayEmphasis" class="display"></span></p>
                                        <p><div class="slidecontainer">
                                            <input type="range" min="1" max="3" value="2" step="1" class="slider" id="sliderEmphasis">
                                            <button class="btnEmphasis">add</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="Row">
                                    <div class="Cell">
                                        <p class="btnHeading">Say-as:</p>
                                        <p> <button class="btnSayAs" value="number" id="number">number</button>
                                            <button class="btnSayAs" value="digits" id="digits">digits</button>
                                            <button class="btnSayAs" value="spell-out" id="SpellOut">spell-out</button>
                                            <button class="btnSayAs" value="fraction" id="fraction">fraction</button>
                                            <button class="btnSayAs" value="ordinal" id="ordinal">ordinal</button>
                                            <button class="btnSayAs" value="unit" id="unit">unit</button>
                                            <button class="btnSayAs" value="bleep" id="bleep">bleep</button>
                                            <button class="btnSayAs" value="date" id="dateMMDDYYYY">date mm/dd/yyyy</button>
                                            <button class="btnSayAs" value="date" id="dateMD">date m/d</button>
                                            <button class="btnSayAs" value="time" id="timeHMS12">time-12h</button>
                                            <button class="btnSayAs" value="time" id="timeHMS24">time-24h</button>
                                            <button class="btnSayAs" value="telephone" id="telephone">telephone</button>
                                        </p><br>   
                                    </div></div>

                            </div>             
                        </div>  
                    </div>    

                    <div class="control" id="control">
                        <div class="label">
                            <label for="qual">1. Quality</label>
                            <select name="qual"  id="qual" class="main" disabled>
                                <option value="std" selected disabled>Select Quality</option>
                                <option value="Standard">Standard</option>
                                <option value="Wavenet">Premium</option>
                            </select>
                        </div>
                        <div class="label">
                            <label for="lang">2. Language</label>
                            <select name="lang"  id="lang" class="main" disabled>
                                <option value="def" selected disabled>Select Language</option>
                                <option value="en-US" class="langOFF">English (US)    </option>
                                <option value="en-AU" class="langOFF">English (Australia)</option>
                                <option value="en-IN" class="langOFF">English (India) </option>
                                <option value="en-GB" class="langOFF">English (UK)    </option>
                                <option value="ar-XA" class="langOFF">Arabic          </option>
                                <option value="bn-IN" class="langOFF">Bengali (India) </option>
                                <option value="cs-CZ" class="langOFF">Czech   (Czech Republic) </option>
                                <option value="da-DK" class="langOFF">Danish  (Denmark)        </option>
                                <option value="nl-NL" class="langOFF">Dutch   (Netherlands)    </option>
                                <option value="fil-PH" class="langOFF">Filipino  (Philippines) </option>
                                <option value="fi-FI" class="langOFF">Finnish (Finland) </option>
                                <option value="fr-CA" class="langOFF">French  (Canada)</option>
                                <option value="fr-FR" class="langOFF">French  (France)</option>
                                <option value="de-DE" class="langOFF">German  (Germany)</option>
                                <option value="el-GR" class="langOFF">Greek   (Greece)   </option>
                                <option value="gu-IN" class="langOFF">Gujarati (India)    </option>
                                <option value="hi-IN" class="langOFF">Hindi    (India)</option>
                                <option value="hu-HU" class="langOFF">Hungarian  (Hungary)</option>
                                <option value="id-ID" class="langOFF">Indonesian  (Indonesia)   </option>
                                <option value="it-IT" class="langOFF">Italian (Italy)          </option>
                                <option value="ja-JP" class="langOFF">Japanese (Japan) </option>
                                <option value="kn-IN" class="langOFF">Kannada (India) </option>
                                <option value="ko-KR" class="langOFF">Korean  (South Korean)        </option>
                                <option value="ml-IN" class="langOFF">Malayalam (India)    </option>
                                <option value="cmn-CN" class="langOFF">Mandarin   (China) </option>
                                <option value="cmn-TW" class="langOFF">Mandarin  (Taiwan) </option>
                                <option value="nb-NO" class="langOFF">Norwegian  (Norway)</option>
                                <option value="pl-PL" class="langOFF">Polish (Poland)</option>
                                <option value="pt-BR" class="langOFF">Portuguese  (Brazil)</option>
                                <option value="pt-PT" class="langOFF">Portuguese  (Portugal) </option>
                                <option value="ru-RU" class="langOFF">Russian (Russia) </option>
                                <option value="sk-SK" class="langOFF">Slovak (Slovakia)</option>
                                <option value="es-ES" class="langOFF">Spanish (Spain)</option>
                                <option value="sv-SE" class="langOFF">Swedish  (Sweden)</option>
                                <option value="ta-IN" class="langOFF">Tamil (India)   </option>
                                <option value="te-IN" class="langOFF">Telugu (India)    </option>
                                <option value="th-TH" class="langOFF">Thai (Thailand)</option>
                                <option value="tr-TR" class="langOFF">Turkish (Turkey)</option>
                                <option value="uk-UA" class="langOFF">Ukrainian  (Ukraine)   </option>
                                <option value="vi-VN" class="langOFF">Vietnamese (Vietnam)          </option>

                            </select>
                        </div>
                        <div class="label">
                            <label for="voices">3. Voice</label>
                            <select name="voices"  id="voices" class="main" disabled>
                                <option value="def" selected disabled>Select Voice</option>
                            </select>
                        </div>
                        <div class="label">
                            <label for="audio">4. Download MP3</label>
                            <button  name="audio" id="audio"  class="audio" disabled>Download MP3</button> 
                        </div>
                        
                            <audio name="mp3" id="media" controls>
                                    <source id="media_src" src="audio/samplemp3.mp3" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                    </audio>  
                        
                        
                        <input type="hidden" id="hiddenSSML" name="hiddenSSML" value="Cool hidden staff">    
                    </div> 
                </div>
                <script>
            function fadeIn(element, display) {
                var op = 0.1;  // initial opacity
                element.style.display = 'block';
                var timer = setInterval(function () {
                    if (op >= 1) {
                        clearInterval(timer);
                    }
                    element.style.opacity = op;
                    element.style.filter = 'alpha(opacity=' + op * 100 + ")";
                    op += op * 0.1;
                    element.textContent = display;
                }, 0.1);
            }

            var sliderEmphasis = document.getElementById("sliderEmphasis");
            var outputEmphasis = document.getElementById("displayEmphasis");
            outputEmphasis.textContent = sliderEmphasis.value;

            sliderEmphasis.oninput = function () {
                var display = parseInt(this.value);
                if (display === 1) {
                    display = 'reduced';
                } else {
                    if (display === 2) {
                        display = 'moderate';
                    } else {
                        if (display === 3) {
                            display = 'strong';
                        }
                    }
                }
                fadeIn(outputEmphasis, display);

            };

            var sliderPitch = document.getElementById("sliderPitch");
            var outputPitch = document.getElementById("displayPitch");
            outputEmphasis.textContent = sliderEmphasis.value;

            sliderPitch.oninput = function () {
                var display = parseInt(this.value);
                var suffix = 'st';
                if (display === 0) {
                    display = 'medium';
                    suffix = '';
                } else {
                    if (display === -24) {
                        display = 'x-low';
                        suffix = '';
                    } else {
                        if (display === 24) {
                            display = 'x-high';
                            suffix = '';
                        }
                    }
                }
                fadeIn(outputPitch, display + suffix);
            };

            var sliderSpeed = document.getElementById("sliderSpeed");
            var outputSpeed = document.getElementById("displaySpeed");
            outputSpeed.textContent = sliderEmphasis.value;

            sliderSpeed.oninput = function () {
                var display = parseInt(this.value);
                var suffix = '%';
                if (display === 100) {
                    display = 'medium';
                    suffix = '';
                } else {
                    if (display === 1) {
                        display = 'x-slow';
                        suffix = '';
                    } else {
                        if (display === 200) {
                            display = 'x-fast';
                            suffix = '';
                        }
                    }
                }
                fadeIn(outputSpeed, display + suffix);
            };
            var sliderVolume = document.getElementById("sliderVolume");
            var outputVolume = document.getElementById("displayVolume");
            outputVolume.textContent = sliderVolume.value;

            sliderVolume.oninput = function () {
                var display = parseInt(this.value);
                var suffix = 'dB';
                if (display === 0) {
                    display = 'medium';
                    suffix = '';
                } else {
                    if (display === -24) {
                        display = 'silent';
                        suffix = '';
                    } else {
                        if (display === 24) {
                            display = 'x-loud';
                            suffix = '';
                        }
                    }
                }
                fadeIn(outputVolume, display + suffix);
            };
            

                </script>                                                                                

            </body>
            </html>

