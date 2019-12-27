<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

define( 'FACEBOOK_APP_ID', '414761372766302' );
define( 'FACEBOOK_APP_SECRET', 'fe825a387498f9e1df02da19f483f22b' );

error_reporting(E_ALL);
ini_set("display_errors", 1);

require( ROOT_DIR.DS.'core/classes/php-graph-sdk-5.x/src/Facebook/autoload.php' );

$accessToken  = 'EAAF5OShKUF4BAOsIRqpT4GqNp6IoEM3P5j778KEMOB3Uk9HypXuxBEOkNUF8UZA8mCpgIuQvVNWyjkV38J3V3yl44W5F7i3Ii7nZBqdTGOYhV4POxPhz0uEDXC0aoA2GFSSGyjJcInwzpK65Vd6VJZCcIxvcXTozrXWCzEDN26IsCDi9CmF';
$page_token = 'EAAF5OShKUF4BANwVeg3ZA8bRZBdaCVhNFZAGkZCrPv78bVysLtK64MYkfuYL6k5UBpQNMPOlpYK8jaSTrDc2ZCvdSCcwso3nBmHnwHOQAZBWsRbahkA8gMeHvqaN8UG6uX0nEjHM5lCXPtWFduLympeU5Nhj9BYT04DVIG3C3d35dYklOZCQ26z';


$fb = new Facebook\Facebook([
      'app_id' => FACEBOOK_APP_ID, //Замените на ваш id приложения
      'app_secret' => FACEBOOK_APP_SECRET //Ваш секрет приложения
      ]);

if( !$accessToken )
{
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['manage_pages','publish_pages'];
    $loginUrl = $helper->getLoginUrl('https://cknews.pp.ua/index.php?mod=facebook', $permissions );
    echo '<a href="' . htmlspecialchars($loginUrl) . '">Вход</a>';
    exit;
}


$str_page = '/106830904087035/feed';
$feed = array('message' => 'тест');


try
{
	$response = $fb->post($str_page, $feed, $page_token );
}

catch (Facebook\Exceptions\FacebookResponseException $e)
{
	echo 'Graph вернул ошибку: ' . $e->getMessage();
	exit;
}

catch (Facebook\Exceptions\FacebookSDKException $e)
{
	echo 'Facebook SDK вернул ошибку: ' . $e->getMessage();
	exit;
}

$graphNode = $response->getGraphNode();

echo 'Опубликовано, id: ' . $graphNode['id'];


//$response = $fb->get('/106830904087035?fields=access_token', $accessToken );

exit;
