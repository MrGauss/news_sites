<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){ require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !trait_exists( 'security' ) ){ require( CLASSES_DIR.DS.'trait.security.php' ); }

class common
{
	use basic, security;

    public static final function get_pages_navbar( $current_url, $posts_count )
    {
        $curr_page = defined('_PAGE_ID')?_PAGE_ID:0;
        if( $curr_page == 1 ){ $curr_page = 0; }

        $_config    = config::get();
        $posts_pages = intval( ceil( $posts_count / $_config['posts_limit'] ) );

        $N = 3;

        $current_url_no_pages = preg_replace( '!page:(\d+)(\/|)!i', '', $current_url );
        $current_url_no_pages = preg_replace( '!index\.(php|htm|html)!i', '', $current_url_no_pages );

        $begin = 1;
        if( $curr_page > $N ){ $begin = $curr_page - $N; }

        $end = $begin + ($N*2);
        if( $end > $posts_pages ){ $end = $posts_pages; }
        if( $end == $curr_page ){ $begin = $end - ($N*2); }
        if( $begin < 1 ){ $begin = 1; }

        $tpl = new tpl;

        $_F = 0;
        for( $i = $begin; $i <= $end; $i++ )
        {
            $_F++;

            $tpl->load( 'navbar_posts' );

            if( $i > 1 )
            {
                $page_url = $current_url_no_pages.'page:'.$i.'/';
            }
            else
            {
                $page_url = $current_url_no_pages.'';
            }

            $tpl->set( '{url}', $page_url );

            if( $i == $curr_page || ( $i == 1 && $curr_page == 0 ) )
            {
                $tpl->set( '{curr}', 'current' );
            }
            else
            {
                $tpl->set( '{curr}', '' );
            }

            $tpl->set( '{I}', $i );

            $tpl->compile( 'navbar_posts' );
        }
        return $tpl->result( 'navbar_posts' );
    }
}

?>