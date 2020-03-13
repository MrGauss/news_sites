<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){ require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !class_exists( 'posts' ) ){ require( CLASSES_DIR.DS.'class.posts.php' ); }

//////////////////////////////////////////////////////////////////////////////////////////

class sitemap
{
    use basic;

    const SKIN_MAIN =
             '<?xml version="1.0" encoding="'.CHARSET.'" ?>'
            ."\n".'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'
            ."\n\t".'<channel>'
            ."\n\t\t".'<atom:link href="'.HOMEURL.'rss.xml" rel="self" type="application/rss+xml" />'
            ."\n\t\t".'<title>{TITLE}</title>'
            ."\n\t\t".'<link>'.HOMEURL.'</link>'
            ."\n\t\t".'<language>uk</language>'
            ."\n\t\t".'<description>{DESCRIPTION}</description>'
            ."\n\t\t".'<image>'
            ."\n\t\t\t".'<link>'.HOMEURL.'</link>'
            ."\n\t\t\t".'<title>{TITLE}</title>'
            ."\n\t\t\t".'<url>'.HOMEURL.'uploads/'.DOMAIN.'.png</url>'
            ."\n\t\t\t".'<description>'.DOMAIN.'</description>'
            ."\n\t\t".'</image>{ITEMS}'
            ."\n\t".'</channel>'
            ."\n\t".'</rss>';

    const SKIN_ITEM = "\n\t\t".'<item>'
                      ."\n\t\t".'<title>{TITLE}</title>'
                      ."\n\t\t".'<link>{LINK}</link>'
                      ."\n\t\t".'<guid>{LINK}</guid>'
                      ."\n\t\t".'<description>{DESCRIPTION}</description>{CATEGORY}'
                      ."\n\t\t".'<pubDate>{DATE}</pubDate>'.'{ENCLOSURE}'
                      ."\n\t".'</item>';

    static public final function generate()
    {
        $file = ROOT_DIR.DS.DOMAIN.'-sitemap.xml';

        if( file_exists($file) && filemtime( $file ) > ( time() - 60*60 ) ){ return $file; }

        $posts = new posts;
        $data = array();

        $_config    = config::get();

        $_SKIN =
                     "\n\t".'<url>'
                    ."\n\t\t".'<loc>{LINK}</loc>'
                    ."\n\t\t".'<lastmod>{DATE}</lastmod>'
                    ."\n\t\t".'<changefreq>{CHANGE}</changefreq>'
                    ."\n\t\t".'<priority>{PRIORITY}</priority>'
                    ."\n\t".'</url>';

        $tags = new tags;
        foreach( $tags->get_tags() as $tag )
        {
            $line = $_SKIN;
            $line = str_replace( '{TITLE}',           common::htmlspecialchars_decode( common::stripslashes($tag['name']) ), $line );
            $line = str_replace( '{LINK}',            SCHEME.'://'.DOMAIN.$tags->get_url( $tag['name'] ), $line );
            $line = str_replace( '{DATE}',            date('Y-m-d'), $line );
            $line = str_replace( '{PRIORITY}',        '0.7',      $line );
            $line = str_replace( '{CHANGE}',          'daily',      $line );
            $data[] = $line;
        }

        foreach( $posts->get( array( 'offset' => 0, 'limit' => 10000, 'post.posted' => 1 ) ) as $post_id => $post_data )
        {
            $tags = array();
            foreach( $post_data['tags'] as $tag_id => $tag_data )
            {
                $tags[] = "\n\t\t".'<category>'.common::htmlspecialchars_decode( common::stripslashes( $tag_data['name'] ) ).'</category>';
            }
            $tags = implode( "", $tags );

            $post_data['post']['created_time'] = strtotime( $post_data['post']['created_time'] );

            $line = $_SKIN;
            $line = str_replace( '{TITLE}',           common::htmlspecialchars_decode( common::stripslashes($post_data['post']['title']) ), $line );
            $line = str_replace( '{LINK}',            SCHEME.'://'.DOMAIN.$posts->get_url( $post_data ), $line );
            $line = str_replace( '{DATE}',            date('Y-m-d', $post_data['post']['created_time']), $line );
            $line = str_replace( '{DESCRIPTION}',     common::htmlspecialchars_decode( common::stripslashes($post_data['post']['short_post']) ), $line );
            $line = str_replace( '{CATEGORY}',        $tags,      $line );
            $line = str_replace( '{PRIORITY}',        '0.6',      $line );
            $line = str_replace( '{CHANGE}',          'never',      $line );

            $data[] = $line;
        }
        $posts = null;


        $skin = '<?xml version="1.0" encoding="'.CHARSET.'" ?>'
                ."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
                ."\n".'<url>'
                ."\n\t".'<loc>'.HOMEURL.'</loc>'
                ."\n\t\t".'<lastmod>'.date('Y-m-d').'</lastmod>'
                ."\n\t\t".'<changefreq>always</changefreq>'
                ."\n\t\t".'<priority>0.8</priority>'
                ."\n\t".'</url>'
                ."".'{ITEMS}'
                ."\n".'</urlset>';

        $skin = str_replace( '{ITEMS}', implode( "", $data ), $skin );
        $skin = str_replace( '{TITLE}', $_config['title'], $skin );
        $skin = str_replace( '{DESCRIPTION}', $_config['site_descr'], $skin );

        $fp = fopen( $file, 'w' );
              fwrite( $fp, common::trim( $skin ) );
              fclose( $fp );

        return $file;
    }

    static public final function rss()
    {
        $N = 50;
        $_cache_var = 'posts-rss-'.$N;
        $skin = cache::get( $_cache_var );

        if( $skin ){ return $skin; }

        $skin = false;

        $posts = new posts;
        $data = $posts->get( array( 'offset' => 0, 'limit' => $N, 'post.posted' => 1 ) );

        $_config    = config::get();

        foreach( $data as $post_id => $post_data )
        {
            $tags = array();
            foreach( $post_data['tags'] as $tag_id => $tag_data )
            {
                $tags[] = "\n\t\t".'<category>'.common::htmlspecialchars_decode( common::stripslashes( $tag_data['name'] ) ).'</category>';
            }
            $tags = implode( "", $tags );

            $post_data['post']['created_time'] = strtotime( $post_data['post']['created_time'] );

            $post_data['post']['short_post'] = common::stripslashes( common::html_entity_decode( common::stripslashes( $post_data['post']['short_post']) ) );
            $post_data['post']['short_post'] = common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( $post_data['post']['short_post']) ) );

            $post_data['post']['full_post'] = common::stripslashes( common::html_entity_decode( common::stripslashes( $post_data['post']['full_post']) ) );
            $post_data['post']['full_post'] = common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( $post_data['post']['full_post']) ) );

            $images = array();
            preg_match( '!src=\"\/(\S+(jpg|jpeg|png))\"!i', $post_data['post']['full_post'], $images );
            $images = isset($images['1'])?$images['1']:false;
            $imagefile = ROOT_DIR.DS.$images;
            $images = HOMEURL.$images;

            if( $images && file_exists($imagefile) )
            {
                $ext = explode( '.', $images );
                $ext = end( $ext );
                $ext = strtolower( $ext );

                switch( $ext )
                {
                    case 'jpg':  $ext = 'image/jpeg'; break;
                    case 'jpeg': $ext = 'image/jpeg'; break;
                    case 'png':  $ext = 'image/png';  break;
                    default: $ext = false;
                }

                if( $ext )
                {
                    $images = "\n\t\t".'<enclosure url="'.$images.'" length="'.filesize($imagefile).'" type="'.$ext.'" />';
                }
                else
                {
                    $images = '';
                }
            }

            $data[$post_id] = "\n\t".common::trim( self::SKIN_ITEM );
            $data[$post_id] = str_replace( '{TITLE}',           common::htmlspecialchars_decode( common::stripslashes($post_data['post']['title']) ), $data[$post_id] );
            $data[$post_id] = str_replace( '{LINK}',            SCHEME.'://'.DOMAIN.$posts->get_url( $post_data ), $data[$post_id] );
            $data[$post_id] = str_replace( '{DATE}',            date('r', $post_data['post']['created_time']), $data[$post_id] );
            $data[$post_id] = str_replace( '{DESCRIPTION}',     $post_data['post']['short_post'], $data[$post_id] );
            $data[$post_id] = str_replace( '{CATEGORY}',        $tags,      $data[$post_id] );
            $data[$post_id] = str_replace( '{ENCLOSURE}',       $images,      $data[$post_id] );
        }

        $skin = common::trim( self::SKIN_MAIN );

        $skin = str_replace( '{ITEMS}', implode( "", $data ), $skin );
        $skin = str_replace( '{TITLE}', $_config['title'], $skin );
        $skin = str_replace( '{DESCRIPTION}', $_config['site_descr'], $skin );

        cache::set( $_cache_var, $skin);

        return $skin;
    }
}