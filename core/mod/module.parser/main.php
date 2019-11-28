<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

/*$page_id = '106830904087035';

$data = array(
    'access_token' => 'EAAF5OShKUF4BAD1r5hZAfXcDR7DZAZAouJY8zZA45cN0XhHv1XHFxhzM6L2yGSlxDDZCdWsZCAg4VRG8LfNa4aZAAWU0KLzLDxSYuUZA6nyEy6y8dZCWPyQuAe0kdANHgCA3TE0lH6dTgmyCGp1Ixvv4SBjK2zsPYPUhyMRZCDZAZA84MnYVnHcthwgk6c6ZAG2vGyccZD',
    'message'      => 'Hello, world!',
    'link'         => 'https://cknews.pp.ua/index.php',
    'name'         => 'Анкор',
    'picture'      => 'https://cknews.pp.ua/uploads/cknews.pp.ua.png'
);

            $h = 0;
            $c = 0;
            $t = 5;
            $ch = curl_init( 'https://graph.facebook.com/' . $page_id . '/feed' );
            curl_setopt($ch, CURLOPT_HEADER, $h);
            curl_setopt($ch, CURLOPT_NOBODY, $c);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_TIMEOUT, $t);
            curl_setopt($ch, CURLOPT_REFERER, 'https://facebook.com/');
            curl_setopt($ch, CURLOPT_USERAGENT, 'TELEGRAM BOT');
            $page = curl_exec($ch);
            curl_close($ch);


var_export($page);
exit; */

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
    $parser->load_from_pingvin_pro(); 
    $parser->load_from_ukr_media('https://ukr.media/science/', array( 'наука', 'технології' ));

    $parser->load_from_playua_net();
    $parser->load_from_cikavosti( 'https://cikavosti.com/category/tehnologiyi/', array( 'технології' ) );
    //

    $parser->unian_load( 'https://www.unian.ua/m/tag/hakeri', array( 'хакер' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/kosmos', array( 'космос' ) );
    $parser->unian_load( 'https://www.unian.ua/m/science', array( 'технології', 'наука' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/gadjeti', array( 'девайс' ) );
}

$parser->load_and_process_tags();

exit;

?>