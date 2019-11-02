<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !class_exists( 'parser' ) ){ require( CLASSES_DIR.DS.'class.parser.php' ); }

header('Content-type: text/plain; charset=' . CHARSET);

$f = MODS_DIR.DS.'module.'._MOD_.DS.DOMAIN.'.php';
if( !file_exists($f) ){ echo "NO TAGS!"; exit; }
$tags = array_unique( array_keys( require( $f ) ) );

if( is_array($tags) && count($tags) )
{
    foreach( $tags as $tag )
    {
        $count = common::integer( $db->super_query( 'SELECT count(id) as count FROM tags WHERE name=\''.$db->safesql($tag).'\';' )['count'] );
        if( $count ){ continue; }

        $db->query( 'INSERT INTO tags (name,altname) VALUES (\''.$db->safesql($tag).'\', \''.$db->safesql(common::totranslit( $tag )).'\');' );

        echo "INSERT TAG:\n".$tag."\n";
        flush();
    }
    echo "\n\n";
}

$parser = new parser;

if( DOMAIN == 'cknews.pp.ua' )
{
    $parser->load_from_dzvin_media();
    $parser->load_from_18000_com_ua();
    $parser->load_from_shpola_otg_gov_ua();
    $parser->load_from_vch_uman_in_ua();
    $parser->load_from_zolotonosha_ck_ua();
    $parser->load_from_provce_ck_ua();
    $parser->load_from_vycherpno_ck_ua();
    $parser->load_from_ridnyi_com_ua();

    $parser->unian_load( 'https://www.unian.ua/m/tag/cherkasi', array( 'черкаси' ) );

    //

    //$parser->unian_load( 'https://www.unian.ua/m/tag/seks', array( 'секс' ) );
    //$parser->unian_load( 'https://www.unian.ua/m/health', array( 'медицина' ) );
    //$parser->unian_load( 'https://www.unian.ua/m/tag/tserkva', array( 'церква' ) );
}
if( DOMAIN == 'live-in-ck.pp.ua' )  { $parser->unian_load( 'https://www.unian.ua/m/war', array( 'війна', 'ато' ) ); }
if( DOMAIN == 'it-news.pp.ua' )
{
    $parser->load_from_ukr_media('https://ukr.media/science/', array( 'наука', 'технології' ));

    $parser->load_from_playua_net();
    $parser->load_from_cikavosti( 'https://cikavosti.com/category/tehnologiyi/', array( 'технології' ) );
    $parser->load_from_pingvin_pro();

    $parser->unian_load( 'https://www.unian.ua/m/tag/hakeri', array( 'хакер' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/kosmos', array( 'космос' ) );
    $parser->unian_load( 'https://www.unian.ua/m/science', array( 'технології', 'наука' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/gadjeti', array( 'девайс' ) );
}

$parser->load_and_process_tags();

exit;

?>