<?php

function telegram_send( $message )
{
    if( !$message || strlen($message) < 5 ){ return false; }
    // $access_token = '718914205:AAH-oFTPCKeIBYv0Pnv9PUnkghxG2vt3aUo';
    $access_token = '698022212:AAHwwrXGLJg8xrFOjXQ9w4Sqd5oPPRGxhCU';
    $api = 'https://api.telegram.org/bot' . $access_token;
    // $chat_id = '-1001153323330';
    $chat_id = '-1001456579507';

    $message = mb_convert_encoding($message, 'utf-8', 'cp1251');
    $message = urlencode( $message );

    $URL = $api . '/sendMessage?'
                    .'chat_id=' . $chat_id
                    .'&text=' . $message
                    .'&parse_mode=html'
                    .'&disable_notification=1';

    $h = 0;
    $c = 0;
    $t = 5;
    $id = curl_init( $URL );

        curl_setopt($id, CURLOPT_HEADER, $h);
        curl_setopt($id, CURLOPT_NOBODY, $c);
        curl_setopt($id, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($id, CURLOPT_TIMEOUT, $t);
        curl_setopt($id, CURLOPT_REFERER, $URL);
        curl_setopt($id, CURLOPT_USERAGENT, 'NDEKC.CK.UA TELEGRAM BOT');

    $page = curl_exec($id);
    curl_close($id);
    return true;
}

parse_str(implode('&', array_slice($argv, 1)), $_GET);

$_GET['action'] = isset($_GET['action'])?$_GET['action']:0;
$_GET['action'] = abs( intval( $_GET['action'] ) );

$_GET['site'] = isset($_GET['site'])?$_GET['site']:false;
$_GET['site'] = strip_tags($_GET['site']);

if( !$_GET['site'] ){ exit; }

if( $_GET['action'] == 1 )
{
    telegram_send( date('Y.m.d H:i:s').' - Розпочато процедуру резервного копіювання...' );
    exit;
}

if( $_GET['site'] && $_GET['action'] == 2 )
{
    telegram_send( 'Створено резервну копію ресурсу "'.$_GET['site'].'"' );
    exit;
}

if( $_GET['site'] && $_GET['action'] == 3 )
{
    telegram_send( 'Ресурс "'.$_GET['site'].'" завантажено до віддаленого репозиторію https://github.com/MrGauss/'.$_GET['site'].'' );
    exit;
}

if( $_GET['action'] == 4 )
{
    telegram_send( date('Y.m.d H:i:s').' - Процедуру резервного копіювання завершено!' );
    exit;
}


?>