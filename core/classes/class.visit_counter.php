<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){ require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !trait_exists( 'login' ) ){ require( CLASSES_DIR.DS.'trait.login.php' ); }
if( !trait_exists( 'db_connect' ) ){ require( CLASSES_DIR.DS.'trait.db_connect.php' ); }

class visit_counter
{
    use basic;

    public final static function count()
    {
        $db = isset( $GLOBALS['db'] ) && is_object( $GLOBALS['db'] ) ?  $GLOBALS['db'] : false;
        if( !$db ){ return false; }

        $SQL = 'INSERT INTO visit_counter ( ua_hash, ip, browser ) VALUES ( \''.md5(USER_IP.$_SERVER['HTTP_USER_AGENT']).'\', \''.USER_IP.'\'::inet, \''.$db->safesql(common::get_browser_name($_SERVER['HTTP_USER_AGENT'])).'\' );';
        $db->query( $SQL );
    }

    public final static function stats_by_curr_day()
    {
        $db = isset( $GLOBALS['db'] ) && is_object( $GLOBALS['db'] ) ?  $GLOBALS['db'] : false;
        if( !$db ){ return false; }

        $m = intval( date('m') );
        if( $m < 15 )                { $m = 15; }
        elseif( $m >= 15 && $m < 30 ){ $m = 30; }
        elseif( $m >= 30 && $m < 45 ){ $m = 45; }
        else                         { $m = '00'; }

        $cache_var = 'visitors-'.date('Ymd-H').$m;
        $n = intval( cache::get( $cache_var ) );

        if( $n ){ return $n; }

        $SQL = 'SELECT count( DISTINCT ua_hash ) as count FROM visit_counter WHERE date_trunc(\'day\', ts) = date_trunc(\'day\', now());';
        $n = $db->super_query( $SQL );
        $n = $n['count'];

        cache::set( $cache_var, $n );

        return $n;
      }

}