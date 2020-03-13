<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

$_POSTS = new posts;

$_config    = config::get();

$posts_count = 0;
$skin       = 'postshort';
$filter     = array();
$url        = '/';

// ВСТАНОВЛЕННЯ ФІЛЬТРІВ ДЛЯ ПОШУКУ ПУБЛІКАЦІЙ ///////////////////////////////////////////

// ВИВІД ТІЛЬКИ ОПУБЛІКОВАНОГО //
$filter['post.posted']  = 1;
$filter['offset']  = _PAGE_ID * $_config['posts_limit'];
$filter['searchTerm']  = isset($_REQUEST['searchTerm'])?common::trim($_REQUEST['searchTerm']):false;

if( _CATEG_ID ){    $filter['post.categ']   = _CATEG_ID; }
if( _TAG_ID ){      $filter['tag.id']       = _TAG_ID; }
if( _POST_ID )
{
    $filter['searchTerm']   = false;
    $filter['post.id']      = _POST_ID;
    $skin = 'postfull';
}

foreach( $_POSTS->get( $filter, $posts_count ) as $id => $row )
{
    $_POSTS->html( $row, $tpl, $skin );

    if( _POST_ID )
    {
        $img = false;
        if( preg_match( '!src=\"\/(\S+?(jpeg|jpg|png))\"!i', $row['post']['full_post'], $img ) )
        {
            $img = $img[1]?HOMEURL.$img[1]:false;
            if( $img )
            {
                $tpl->head_tags['og:image'] = $img;
            }
        }

        //
        $tpl->head_tags['title'] = common::htmlspecialchars( $_config['title'].' - '.$row['post']['title'] );
        $url = $_POSTS->get_url( $row );
    }
}

if( _CATEG_ID && $posts_count )
{
    $tpl->head_tags['title'] = common::htmlspecialchars($_config['title'].' - '. $GLOBALS['_CATEG']->get_categories()[_CATEG_ID]['name'] );
    $url = $GLOBALS['_CATEG']->get_url( _CATEG_ID );
}

if( _TAG_ID && $posts_count )
{
    $tpl->head_tags['title'] = common::htmlspecialchars( $_config['title'].' - '.$GLOBALS['_TAGS']->get_tags()[_TAG_ID]['name'] );
    $url = $GLOBALS['_TAGS']->get_url( $GLOBALS['_TAGS']->get_tags()[_TAG_ID]['name'] );
}

if( !_CATEG_ID && !_TAG_ID && !_POST_ID && _PAGE_ID && $posts_count )
{
    $url = $GLOBALS['_CATEG']->get_url( _CATEG_ID );
}

$url = $url.common::searchTermUrl();

if( defined('_PAGE_ID') && _PAGE_ID > 1 )
{
    $url = $url.'/page:'._PAGE_ID.'/';
    $url = mb_ereg_replace('\/\/', '/', $url);
}

$tpl->head_tags['og:url'] = SCHEME.'://'.DOMAIN.$url;

if( !$posts_count )
{
    header('HTTP/1.0 404 Not Found');
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');

    $tpl->info( 'Матеріал не знайдено!', 'В процесі обробки запиту не вдалося знайти запис.', 'warn' );
}

if( $posts_count && $url != $_SERVER['REQUEST_URI']  )
{
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$url);
    exit;
}

$tpl->load( 'posts' );
$tpl->set( '{posts}', $tpl->result( $skin ) );

if( !_POST_ID && $posts_count )
{
    $tpl->set( '{navbar}', common::get_pages_navbar( $url, ( $posts_count > $_config['posts_limit'] ) ? ( $posts_count-$_config['posts_limit'] ) : $posts_count ) );
}
else
{
    $tpl->set( '{navbar}', '' );
}

$tpl->compile( 'posts' );

//////////////////////////////////////////////////////////////////////////////////////////

?>