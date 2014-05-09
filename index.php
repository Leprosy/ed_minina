<?php
session_start();

/* Bootstrap */
require 'vendor/autoload.php';
require 'config.php';

$consumer_key = "wk30aGZtbH5Hbu1vY5Is7yI2U";
$consumer_secret = "ORNx9CPK38Sph8mBnnvufOSa85H93pj2BrYTUOiuLUaqNLDJU5";


/* Functions */
function getMau($withcc = True) {
    $maus = array('mamaauuuu...', 'prr...', 'prrr...', 'cruuñaauu...', 'maaaaaau...','mmau...', 'prrrñau...', 'ñau...', 'miaaaau...', 'MIAAAAAAAAUUUU...');
    $mau = '';
    $hums = array('@aindir', '@leprosy', '@aindir', '@angebrik', '@_Natilla');
    $emos = array(':D', 'XD', ':P', 'xD', ':@');
    $mausP = rand(2, 6);
    $humsP = rand(0, 30);
    $emosP = rand(0, 30);

    if ($humsP < 10 && $withcc) {
        $mau .= $hums[rand(0, count($hums) - 1)] . ' ';
    }

    for ($i = 0; $i < $mausP; ++$i) {
        $mau .= $maus[rand(0, count($maus) - 1)];
    }

    if ($emosP < 10) {
        $mau .= $emos[rand(0, count($emos) - 1)];
    }

    return $mau;
}

/* Query API */
if (file_exists($app_file) && $access_token = unserialize(file_get_contents($app_file))) {
    $lock = file_get_contents($app_file . '.lock');

    if (time() - $lock < $app_time) {
        die('TOO SOON, ME BOY ;)');
    } else {
        $lock = fopen($app_file . '.lock', 'w');
        fwrite($lock, time());
    }

    $mau = getMau();
    $connection = new \TwitterOAuth\Api($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

    /* Answer calls */
    foreach ($connection->get('statuses/mentions_timeline', array()) as $twit) {
        if (time() - strtotime($twit->created_at) < $app_time) {
            $mau = '@' . $twit->user->screen_name . ' ' . getMau(false);
            var_dump("REPLY", $connection->post('statuses/update', array('status' => $mau, 'in_reply_to_status_id' => $twit->id)));
            sleep(1);
        }
    }

    /* Send a tweet */
    var_dump("POSTING", $connection->post('statuses/update', array('status' => $mau)));
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

