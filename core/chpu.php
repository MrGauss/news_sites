<?php

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

////////////////////////////////////////////////////////////////////////////////////////////////////////////

if( defined('_NO_CACHE_') )
{
    $_SERVER['REQUEST_URI'] = preg_replace( '!(\?no_cache=1|\&no_cache=1)!i', '', $_SERVER['REQUEST_URI'] );
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!^\/index\.(htm|html)$!i', $_SERVER['REQUEST_URI'], $data ) )
{
    header( 'Location: '.HOME.'' ); exit;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////
$data = false;
if( preg_match( '!\/page:(\d+?)(\/|)$!i', $_SERVER['REQUEST_URI'], $data ) )
{
    $data = isset($data[1])?$data[1]:false;
}
define( '_PAGE_ID' , intval( is_array($data)?0:$data ) );
////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!^\/download:(.+?).html$!i', $_SERVER['REQUEST_URI'], $data ) )
{
    if( is_array($data) && isset($data[1]) )
    {
        $data = files::decode_url( $data[1] );
    }

    if( is_array($data) && isset($data['md5']) && isset($data['name']) )
    {
        $_REQUEST['mod'] =  'download';
        $_REQUEST['file_name'] = $data['name'];
        $_REQUEST['file_md5']  = $data['md5'];
    }
    else
    {
        header('HTTP/1.0 404 Not Found');
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');

        echo( 'Downloading failed! File not found!' );
        exit;
    }
}
define( '_DOWNLOAD_HASH', isset($_REQUEST['file_md5'])?$_REQUEST['file_md5']:false );
define( '_DOWNLOAD_NAME', isset($_REQUEST['file_name'])?$_REQUEST['file_name']:false );

////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!\/tag:(.+?)\/!i', $_SERVER['REQUEST_URI'], $data ) )
{
    $_REQUEST['mod'] =  'posts';
    $data = isset($data[1])?tags::tag_decode($data[1]):false;

    if( $data )
    {
        if( !isset($GLOBALS['_TAGS']) || !is_object($GLOBALS['_TAGS']) ){ $GLOBALS['_TAGS'] = new tags; }

        $data = common::integer( $GLOBALS['_TAGS']->get_id( $data, true ) );
        $data = $data?$data:false;
    }else{ $data = false; }
}
define( '_TAG_ID', intval( $data ) );

////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!\/search:(.+?)\/!i', $_SERVER['REQUEST_URI'], $data ) )
{
    $_REQUEST['mod'] =  'posts';
    $_REQUEST['searchTerm'] = isset($data[1])? common::strtolower( common::utf2win(common::rawurldecode($data[1])) ) : false;
}
define( '_SEARCH', isset($_REQUEST['searchTerm'])?$_REQUEST['searchTerm']:false );

////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!\/(\d+)-(\w+)\.html$!i', $_SERVER['REQUEST_URI'], $data ) )
{
    $_REQUEST['mod'] =  'posts';
    $data = intval( isset($data[1])?$data[1]:false );
}
define( '_POST_ID', intval( $data ) );

////////////////////////////////////////////////////////////////////////////////////////////////////////////

$data = false;
if( preg_match( '!^\/([a-z0-9_\/]+?|)$!i', $_SERVER['REQUEST_URI'], $data ) && ( !isset($_REQUEST['mod']) || ( $_REQUEST['mod'] != 'admin' ) ) )
{
    $_REQUEST['mod'] =  'posts';
    $data = isset($data[1])?$data[1]:false;
    $data = explode( '/', $data );

    if( $data && is_array($data) && count($data) )
    {
        if( !isset($GLOBALS['_CATEG']) || !is_object($GLOBALS['_CATEG']) ){ $GLOBALS['_CATEG'] = new categ; }
        $data = common::totranslit( common::filter( end( $data ) ) );
        $data = common::integer( $GLOBALS['_CATEG']->get_id( $data ) );
        $data = $data?$data:false;
    }else{ $data = false; }
}
define( '_CATEG_ID', intval( $data ) );

?>