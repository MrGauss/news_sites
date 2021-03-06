<?php
/**
 * index.php
 *
 * ����� ����� ����� � CMS
 *
 * @category  main
 * @package   cmska.org
 * @author    MrGauss <author@cmska.org>
 * @copyright 2018
 * @license   GPL
 * @version   0.4
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * TOTAL FUNCTIONS: -
 * (This index is automatically created/updated by the plugin "DocBlock Comments")
 *
 */

 $_BEGIN_TIME = microtime( true );

error_reporting ( E_ALL );
ini_set ( 'display_errors', true );
ini_set ( 'html_errors', false );
ini_set ( 'error_reporting', E_ALL );

define ( 'DOMAIN',          $_SERVER['Host'] );
define ( 'HOME',            '/' );
define ( 'SCHEME',          strtolower( explode(':',$_SERVER['SCRIPT_URI'])[0] ) );
define ( 'HOMEURL',         SCHEME.'://'.DOMAIN.HOME );
define ( 'GAUSS_CMS',       true );
define ( 'DS',              DIRECTORY_SEPARATOR );
define ( 'ROOT_DIR',        dirname ( __FILE__ ) );
define ( 'LOGS_DIR',        dirname ( ROOT_DIR ).DS.'logs' );
define ( 'CORE_DIR',        ROOT_DIR.DS.'core' );
define ( 'CLASSES_DIR',     CORE_DIR.DS.'classes' );
define ( 'CACHE_DIR',       ROOT_DIR.DS.'cache' );
define ( 'MODS_DIR',        CORE_DIR.DS.'mod' );
define ( 'TPL_DIR',         ROOT_DIR.DS.'tpl' );
define ( 'UPL_DIR',         ROOT_DIR.DS.'uploads' );
define ( 'USER_IP',         isset($_SERVER['HTTP_CF_CONNECTING_IP'])?$_SERVER['HTTP_CF_CONNECTING_IP']:$_SERVER['REMOTE_ADDR'] );
define ( 'CHARSET',         'Windows-1251' /*'CP1251'*/ );
define ( 'CACHE_TYPE',      'FILE' );

////////////////////////////////////////////////////////////////

if( isset($_REQUEST['no_cache']) && $_REQUEST['no_cache'] == '1' ){ define ( '_NO_CACHE_', true ); }

////////////////////////////////////////////////////////////////

setlocale ( LC_ALL, 'uk_UA.CP1251' );
mb_internal_encoding( CHARSET );

////////////////////////////////////////////////////////////////
// ��������� ������
// if( isset($_SERVER['HTTP_CDN_LOOP']) && $_SERVER['HTTP_CDN_LOOP'] == 'cloudflare' ){ echo PHP_VERSION; exit; }

////////////////////////////////////////////////////////////////

ob_start();

/**
 * ϳ��������� ��������� �������
 */
require( CLASSES_DIR.DS.'class.err_handler.php' );
//err_handler::start();

/**
 * ϳ��������� ����
 */
require( CORE_DIR.DS.'init.php' );

/**
 * ��������� �����
 */

        $tpl->load( 'content' );
        $tpl->compile( 'content' );

echo    stats::ins2html( $tpl->result( 'content' ) );

header('Content-type: text/html; charset=' . CHARSET);
echo ob_get_clean();
exit;

?>