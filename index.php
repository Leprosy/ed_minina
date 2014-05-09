<?php
session_start();

/* Bootstrap */
require 'vendor/autoload.php';
require 'config.php';

$consumer_key = "wk30aGZtbH5Hbu1vY5Is7yI2U";
$consumer_secret = "ORNx9CPK38Sph8mBnnvufOSa85H93pj2BrYTUOiuLUaqNLDJU5";


/* erase session*/
if (isset($_GET['logout'])) {
    session_destroy();
    die('erase');
}


/* Query API */
if (file_exists($app_file) && $access_token = unserialize(file_get_contents($app_file))) {
    $lock = file_get_contents($app_file . '.lock');

    if (time() - $lock < 60) {
        die('TOO SOON');
    } else {
        $lock = fopen($app_file . '.lock', 'w');
        fwrite($lock, time());
    }

    $mau = array('mamaauuuu...', 'prr...', 'prrr...', 'cruuñaauu...', 'maaaaaau...','mmau...', 'prrrñau...', 'ñau...', 'miaaaau...', 'MIAAAAAAAAUUUU...');
    $mmau = '';
    $hums = array('@aindir', '@leprosy', '@aindir', '@angebrik', '@_Natilla');
    $emos = array(':D', 'XD', ':P');
    $toks = rand(2, 10);
    $cc = rand(0, 30);
    $emoc = rand(0, 30);

    if ($cc < 10) {
        $mmau .= $hums[rand(0, count($hums) - 1)] . ' ';
    }

    for ($i = 0; $i < $toks; ++$i) {
        $mmau .= $mau[rand(0, count($mau) - 1)];
    }

    if ($emoc < 10) {
        $mmau .= $emos[rand(0, count($emos) - 1)];
    }

    $connection = new \TwitterOAuth\Api($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    var_dump($connection->post('statuses/update', array('status' => $mmau)));
} else {
    if (isset($_SESSION['oauth_token'])) {
        /* The temp tokens arrived */
        $connection = new \TwitterOAuth\Api($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        $file = fopen($app_file, 'w');
        fwrite($file, serialize($access_token));

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
        die('TOKEN CREATED');
    } else {
        /* Starting...no token */
        $connection = new \TwitterOAuth\Api($consumer_key, $consumer_secret);
        $request_token = $connection->getRequestToken($appurl);
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        $url = $connection->getAuthorizeURL($token);
        header('Location: ' . $url);
    }
}

die();

