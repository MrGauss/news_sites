<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !class_exists( 'sitemap' ) ){ require( CLASSES_DIR.DS.'class.sitemap.php' ); }

header('Content-type: text/xml; charset=' . CHARSET);

if( !isset($_GET['rss']) )
{
    $file = sitemap::generate();
    if( !file_exists($file) ){ exit; }
    $f = fopen( $file, 'rb' );
    while( !feof($f) ){ echo fread($f,1024); }
    fclose( $f );
}
else
{
    echo sitemap::rss();
}


exit;