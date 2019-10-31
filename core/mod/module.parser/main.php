<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !class_exists( 'parser' ) ){ require( CLASSES_DIR.DS.'class.parser.php' ); }

header('Content-type: text/plain; charset=' . CHARSET);

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

    $parser->unian_load( 'https://www.unian.ua/m/tag/cherkasi', array( '�������' ) );

    //

    //$parser->unian_load( 'https://www.unian.ua/m/tag/seks', array( '����' ) );
    //$parser->unian_load( 'https://www.unian.ua/m/health', array( '��������' ) );
    //$parser->unian_load( 'https://www.unian.ua/m/tag/tserkva', array( '������' ) );
}
if( DOMAIN == 'live-in-ck.pp.ua' )  { $parser->unian_load( 'https://www.unian.ua/m/war', array( '����', '���' ) ); }
if( DOMAIN == 'it-news.pp.ua' )
{
    $parser->load_from_playua_net();
    $parser->load_from_pingvin_pro();

    $parser->unian_load( 'https://www.unian.ua/m/tag/hakeri', array( '�����' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/kosmos', array( '������' ) );
    $parser->unian_load( 'https://www.unian.ua/m/science', array( '�������㳿', '�����' ) );
    $parser->unian_load( 'https://www.unian.ua/m/tag/gadjeti', array( '������' ) );
}

$parser->load_and_process_tags();

exit;

?>