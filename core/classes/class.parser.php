<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){ require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !class_exists( 'posts' ) ){ require( CLASSES_DIR.DS.'class.posts.php' ); }

// https://pingvin.pro/tag/kosmos
// https://dt.ua/tags/%D0%BA%D0%BE%D1%81%D0%BC%D0%BE%D1%81?page=1
// https://ua.interfax.com.ua/news/tag/%D0%BA%D0%BE%D1%81%D0%BC%D0%BE%D1%81.html
// https://24tv.ua/techno/kosmos_tag1810/
// https://www.unn.com.ua/uk/news/tag/kosmos

// https://itechua.com/
// https://gsminfo.com.ua/
// https://www.ukrinform.ua/rubric-technology/block-lastnews
// https://tehnofan.com.ua/
// https://ua.interfax.com.ua/news/tag/%D0%BA%D0%BE%D1%81%D0%BC%D0%BE%D1%81.html
// https://ua.interfax.com.ua/news/telecom.html
// https://politeka.net/ua/tag/nauka/
// https://techno.znaj.ua/
// https://www.unn.com.ua/rss/news_tech_uk.xml
// https://dt.ua/TECHNOLOGIES

trait tr_ukr_media
{
    // https://ukr.media/science/
    public final function load_from_ukr_media( $categ_link, $tags = array() )
    {
        $data = $this->curl( $categ_link );

        $N = 15;
        $I = 0;

        echo "\n\nLOAD: ".$categ_link."\n";

        if( $this->HTTP_STATUS != 200 ){ echo "\tHTTP ERROR!\n"; return false; }

        $data = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $data );
        $data = preg_replace( '!<\!--(.+?)-->!is', '', $data );
        $data = preg_replace( '!src=\"data(\S+?)\"!is', '', $data );
        $data = explode( '</h1>', $data, 2 ); $data = end( $data );
        $data = explode( 'class="bordered-title">', $data, 2 ); $data = reset( $data );

        if( !preg_match_all( '!<div class="item-article2">(.+?)<h2>(.+?)class="tegs"(.+?)<\/div>!is', $data, $data ) ){ echo "\tNO ARTICLES!\n"; return false; }
        $data = isset($data[0])?$data[0]:array();
        $data = common::trim( $data );

        foreach( $data as $k => $article )
        {
            unset( $data[$k] );

            $article = common::html_entity_decode( $article );
            $article = common::htmlspecialchars_decode( $article );
            $article = common::stripslashes( $article );
            $article = common::trim( $article );

            $article = array( 'page' => $article );

            if( !preg_match( '!href=\"(\S+?)\"!i', $article['page'], $article['link'] ) ){ continue; }
            $article['link'] = strip_tags( $article['link'][1] );

            if( !preg_match( '!<h2(.+?)h2>!i', $article['page'], $article['title'] ) ){ continue; }
            $article['title'] = strip_tags( $article['title'][0] );

            if( !preg_match( '!<div class=\"tegs\">(.+?)<\/div>!i', $article['page'], $article['category'] ) ){ continue; }
            $article['category'] = implode(',', common::trim(explode("\n",trim( strip_tags( preg_replace('!<a!i',"\n".'$0',$article['category'][0]) ) ))) );

            if( !$article['title'] || strlen($article['title']) < 20 ){ continue; }

            $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($article['link']).'%\' OR title =\''.$this->db->safesql( $article['title'] ).'\' ;';
            if( $this->db->super_query( $SQL )['count'] > 0 ){ echo "DUBLICATE! ".$article['title']."\n"; continue; }

            $article['page'] = $this->curl( $article['link'] );
            if( $this->HTTP_STATUS != 200 ){ continue; }

            $article['page'] = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $article['page'] );

            if( preg_match( '!og:image(.+?)content=\"(http\S+?)\"!i', $article['page'], $article['images'] ) ){ $article['images'] = array( $article['images'][2] ); }

            $article['page'] = explode( '</h1>', $article['page'], 2 ); $article['page'] = end( $article['page'] );
            $article['page'] = explode( 'articleBody', $article['page'], 2 ); $article['page'] = '<div class="dd'.end( $article['page'] );

            if( !preg_match( '!<div(.+?)gtegs(.+?)div>!i', $article['page'], $article['keywords'] ) ){ continue; }
            $article['keywords'] = implode(', ', common::trim(explode("\n",trim( strip_tags( preg_replace('!<a!i',"\n".'$0',$article['keywords'][0]) ) ))) );

            $article['page'] = explode( 'class="gtegs', $article['page'], 2 ); $article['page'] = reset( $article['page'] ).'>';

            $article = common::html_entity_decode( $article );
            $article = common::htmlspecialchars_decode( $article );
            $article = common::stripslashes( $article );
            $article = common::trim( $article );

            if( preg_match_all( '!src=\"((http)\S+?(jpg|jpeg|png))\"!i', $article['page'], $images ) )
            {
                $article['images'] =  array_merge( $article['images'],  $images[1] );
            }
            $article['images'] = array_values( array_unique($article['images']) );

            if( !count($article['images']) ){ continue; }

            $article['page'] = strip_tags( $article['page'] );
            $article['page'] = explode( "\n", $article['page'] );
            foreach( $article['page'] as $l => $p )
            {
                $article['page'][$l] = preg_replace( '!(\s+)!i', ' ', $p );
                $article['page'][$l] = common::trim( $article['page'][$l] );
                if( strlen($article['page'][$l]) < 10 ){ unset( $article['page'][$l] ); continue; }
                $article['page'][$l] = '[p]'.$article['page'][$l].'[/p]';
            }
            $article['page'] = implode( "\n", $article['page'] );
            $article['domain'] = 'ukr.media';

            echo "ADDED: ".$this->save_post( $article )."\n";
            $I++;
            if( $I > $N ){ break; }
        }
    }
}

trait tr_cikavosti
{
    public final function load_from_cikavosti( $categ_link, $tags = array() )
    {
        $data = $this->curl( $categ_link );

        $N = 15;
        $I = 0;

        echo "\n\nLOAD: ".$categ_link."\n";

        if( $this->HTTP_STATUS != 200 ){ echo "\tHTTP ERROR!\n"; return false; }

        $data = preg_replace( '!<(script|style)(.+?)(\1)>!is', '', $data );
        $data = preg_replace( '!<\!--(.+?)-->!is', '', $data );

        $data = explode( '</h1>', $data, 2 ); $data = end( $data );

        if( !preg_match_all( '!<article(.+?)article>!is', $data, $data ) ){ echo "\tNO ARTICLES!\n"; return false; }
        $data = isset($data[0])?$data[0]:array();

        foreach( $data as $k => $article )
        {
            unset( $data[$k] );

            $article = common::html_entity_decode( $article );
            $article = common::htmlspecialchars_decode( $article );
            $article = common::stripslashes( $article );
            $article = common::trim( $article );

            $article = array( 'page' => $article );
            if( !preg_match( '!<h2(.+?)h2>!i', $article['page'], $article['title'] ) ){ continue; }
            $article['title'] = strip_tags( $article['title'][0] );

            if( !$article['title'] || strlen($article['title']) < 50 ){ continue; }

            if( !preg_match( '!href=\"(\S+?)\"!i', $article['page'], $article['link'] ) ){ continue; }
            $article['link'] = strip_tags( $article['link'][1] );

            $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($article['link']).'%\' OR title =\''.$this->db->safesql( $article['title'] ).'\' ;';
            if( $this->db->super_query( $SQL )['count'] > 0 ){ echo "DUBLICATE! ".$article['title']."\n"; continue; }

            $article['page'] = $this->curl( $article['link'] );
            if( $this->HTTP_STATUS != 200 ){ continue; }

            $article['page'] = preg_replace( '!<(script|style)(.+?)(\1)>!is', '', $article['page'] );
            $article['page'] = preg_replace( '!<\!--(.+?)-->!is', '', $article['page'] );

            $article['images'] = array();

            // if( preg_match( '!og:image(.+?)content=\"(http\S+?)\"!i', $article['page'], $article['images'] ) ){ $article['images'] = array( $article['images'][2] ); }

            $article['page'] = explode( '<article', $article['page'], 2 ); $article['page'] = '<article '.end( $article['page'] );
            $article['page'] = explode( '</article>', $article['page'], 2 ); $article['page'] = ''.reset( $article['page'] );
            $article['page'] = explode( 'class="entry">', $article['page'], 2 ); $article['page'] = ''.end( $article['page'] );
            $article['page'] = explode( '<div class="adace-slot">', $article['page'], 2 ); $article['page'] = ''.reset( $article['page'] );

            if( preg_match_all( '!src=\"((http)\S+?(jpg|jpeg|png))\"!i', $article['page'], $images ) )
            {
                $article['images'] =  array_merge( $article['images'],  $images[1] );
            }
            $article['images'] = array_values( array_unique($article['images']) );

            if( !count($article['images']) ){ continue; }

            $article['page'] = strip_tags( $article['page'] );
            $article['page'] = explode( "\n", $article['page'] );
            foreach( $article['page'] as $l => $p )
            {
                $article['page'][$l] = preg_replace( '!(\s+)!i', ' ', $p );
                $article['page'][$l] = common::trim( $article['page'][$l] );
                if( strlen($article['page'][$l]) < 10 ){ unset( $article['page'][$l] ); continue; }
                $article['page'][$l] = '[p]'.$article['page'][$l].'[/p]';
            }
            $article['page'] = implode( "\n", $article['page'] );

            $article['domain'] = 'cikavosti.com';
            $article['keywords'] = '';
            $article['category'] = implode(',',$tags);

            $article = common::html_entity_decode( $article );
            $article = common::htmlspecialchars_decode( $article );
            $article = common::stripslashes( $article );
            $article = common::trim( $article );

            // var_export($article); exit;

            echo "ADDED: ".$this->save_post( $article )."\n";
            $I++;
            if( $I > $N ){ break; }
        }
    }
}

trait tr_provce_ck_ua
{
    private final function _provce_ck_ua_get_article( $data )
    {
        if( !$data['link'] || !$data['title'] ){ return false; }
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE! ".$data['title']."\n"; return false; }

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['keywords'] = '';

        $data['page'] = preg_replace( '!<(script|style)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = explode( '<div class="main-content">', $data['page'], 2 );
        $data['page'] = end( $data['page'] );

        $data['page'] = explode( '<div class="post-content clearfix">', $data['page'], 2 );
        $data['page'] = end( $data['page'] );

        $data['page'] = explode( '<div class="flexbox np-wrap">', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );
        $data['page'] = trim( $data['page'] );

        preg_match_all( '!<img(.+?)src=\"(\S+?)\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<img!i', '[img]$0', $data['page'] );

        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = str_replace( '[p][img][/p]', '[img]', $data['page'] );
        $data['page'] = preg_replace( '!Про все!i', '%SITENAME%', $data['page'] );

        return $data;
    }

    private final function _provce_ck_ua_get_links( $data )
    {
        preg_match_all( '!<(item)>(.+?)<\/\1>!is', $data, $data );
        $data = isset($data[0])?$data[0]:array();

        foreach( $data as $k => $_item )
        {
            $item = array();
            foreach( array( 'link', 'title' ) as $area )
            {
                preg_match_all( '!<('.$area.')>(.+?)<\/\1>!is', $_item, $item[$area] );
                $item[$area] = isset($item[$area][2])?reset( $item[$area][2] ):array();
            }

            $item['title'] = self::htmlspecialchars_decode($item['title']);
            $item['title'] = self::html_entity_decode($item['title']);
            $item['title'] = preg_replace( '!\((.+?)\)!i', '', $item['title'] );

            $item['title'] = trim( $item['title'] );

            preg_match_all( '!<(category)>(.+?)<\/\1>!is', $_item, $item['category'] );
            $item['category'] = isset($item['category'][2])?$item['category'][2]:array();
            $item['category'] = implode( "\n", $item['category'] );
            preg_match_all( '!CDATA\[(.+?)\]!is', $item['category'], $item['category'] );
            $item['category'] = implode( '|', isset($item['category'][1])?$item['category'][1]:array() );

            $data[$k] = $item;
        }

        return $data;
    }

    public final function load_from_provce_ck_ua()
    {
        $feed = 'https://provce.ck.ua/feed/';
        $data = $this->curl( $feed );

        echo "LOAD: ".$feed."\n";

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_provce_ck_ua_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_provce_ck_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!\n"; return false; }

        echo 'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_provce_ck_ua( $line )."\n";
        }

    }

    public final function save_provce_ck_ua( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }
        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "provce.ck.ua"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-3600, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_18000_com_ua
{
    public final function load_from_18000_com_ua()
    {
        $feed = 'https://18000.com.ua/feed/';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_18000_com_ua_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_18000_com_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            if( ( $F = $this->save_18000_com_ua( $line ) ) != false )
            {
                echo "ADDED: ".$F."\n";
            }
            else
            {
                echo "SKIP: ".$line['link']."\n";
            }

        }

    }

    private final function _18000_com_ua_get_links( $data )
    {
        return $this->_get_links( $data );
    }

    private final function _18000_com_ua_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        preg_match_all( '!<meta(.*?)property="article:tag"(.*?)content=\"(.+?)\"!i', $data['page'], $data['keywords'] );
        $data['keywords'] = isset($data['keywords'][3])?$data['keywords'][3]:array();
        $data['keywords'] = reset( $data['keywords'] );

        $data['page'] = preg_replace( '!<(script|style)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = explode( '<div class="single-body', $data['page'], 2 );
        $data['page'] = '<div class="w'.end( $data['page'] );

        $data['page'] = explode( '<div class="our_facebook_page">', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = explode( '<span class="share-article-text"', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );

        $data['page'] = common::trim( $data['page'] );
        $data['page'] = common::html_entity_decode( $data['page'] );
        $data['page'] = common::htmlspecialchars_decode( $data['page'] );

        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );
        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = common::trim( $data['page'] );

        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';
        $data['page'] = preg_replace( '!(\s{2,})!i', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );

        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = str_replace( '[p][/p]', '', $data['page'] );
        $data['page'] = str_replace( '[p][b][/b][/p]', '', $data['page'] );

        $data['page'] = preg_replace( '!18000.com.ua!i', '%SITENAME%', $data['page'] );
        $data['page'] = preg_replace( '!18000!i', '%SITENAME%', $data['page'] );

        $data['page'] = common::trim( $data['page'] );

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);

        $data['page'] = self::filter_utf8( $data['page'] );

        if( strlen( $data['page'] ) < 300 ){ echo "SKIP: ".$data['title']."\n"; return false; }

        return $data;
    }

    public final function save_18000_com_ua( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );
        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }
        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "18000.com.ua"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-43200, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_vycherpno_ck_ua
{
    public final function load_from_vycherpno_ck_ua()
    {
        $feed = 'https://vycherpno.ck.ua/feed/';
        $data = $this->curl( $feed );

        echo "\nLOAD: ".$feed."\n";

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_vycherpno_ck_ua_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_vycherpno_ck_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }   
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo 'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_vycherpno_ck_ua( $line )."\n";
        }

    }

    private final function _vycherpno_ck_ua_get_links( $data )
    {
        return $this->_get_links( $data );
    }

    private final function _vycherpno_ck_ua_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE! ".$data['title']."\n"; return false; }

        echo "\tLOADING:".$data['link']."\n";

        $data['page'] = $this->curl( $data['link'] );

        if( $this->HTTP_STATUS != 200 )
        {
            echo "\t\tLOAD ERROR: ".$data['link']."\n"; return false;
        }

        $data['keywords'] = false;

        $data['page'] = preg_replace( '!<(script|style)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = explode( '<div class="entry-content">', $data['page'], 2 );
        $data['page'] = end( $data['page'] );

        $data['page'] = explode( '</article>', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );

        preg_match_all( '!<img(.+?)src=\"(http\S+?)\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();
        $data['images'] = array_unique( $data['images'] );

        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = trim( strip_tags( $data['page'] ) );

        $data['page'] = common::html_entity_decode( $data['page'] );
        $data['page'] = common::htmlspecialchars_decode( $data['page'] );
        $data['page'] = common::trim( $data['page'] );
        $data['page'] = preg_replace( '!(\n+)(\s+)!is', "\n", $data['page'] );
        $data['page'] = preg_replace( '!^(\s+)!is', '', $data['page'] );
        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = common::trim( $data['page'] );

        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';
        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!( {2,})!is', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\[p\](\s+)!is', '[p]', $data['page'] );
        $data['page'] = preg_replace( '!\[p\]\[\/p\]!is', '', $data['page'] );
        $data['page'] = preg_replace( '!\[p\](\s*)\[\/p\]!is', '', $data['page'] );

        $data['page'] = preg_replace( '!vycherpno.ck.ua!i', '%SITENAME%', $data['page'] );
        $data['page'] = preg_replace( '!vycherpno!i', '%SITENAME%', $data['page'] );
        $data['page'] = preg_replace( '!Вичерпно!i', '%SITENAME%', $data['page'] );

        $data['page'] = trim( $data['page'] );

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);

        return $data;
    }

    public final function save_vycherpno_ck_ua( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }
        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "vycherpno.ck.ua"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-43200, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_dzvin_media
{
    public final function load_from_dzvin_media()
    {
        $feed = 'https://dzvin.media/news/feed/';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_dzvin_media_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_dzvin_media_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }   
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 150 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_dzvin_media( $line )."\n";
        }

    }

    private final function _dzvin_media_get_links( $data )
    {
        return $this->_get_links( $data );
    }

    private final function _dzvin_media_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        if( !count($data['images']) ){ return false; }

        $data['keywords'] = false;

        $data['page'] = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = explode( '<div class="article-text', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( '</article>', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = explode( '<div class="share-news-container">', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)(.+?|)>(.+?)<\/(\1)>!i', '$3'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = self::trim($data['page']);
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );
        $data['page'] = preg_replace( '!(\[p\])(\s+)!i', '\1', $data['page'] );
        $data['page'] = preg_replace( '!dzvin.media!i', '%SITENAME%', $data['page'] );
        $data['page'] = preg_replace( '!dzvin!i', '%SITENAME%', $data['page'] );
        $data['page'] = preg_replace( '!ДЗВІН!i', '%SITENAME%', $data['page'] );

        $data['page'] = trim( $data['page'] );
        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);

        return $data;
    }

    public final function save_dzvin_media( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }
        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }

        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "dzvin.media"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-43200, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_ridnyi_com_ua
{
    public final function load_from_ridnyi_com_ua()
    {
        $feed = 'http://ridnyi.com.ua/feed';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_ridnyi_com_ua_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_ridnyi_com_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }   
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_ridnyi_com_ua( $line )."\n";
        }

    }

    private final function _ridnyi_com_ua_get_links( $data )
    {
        return $this->_get_links( $data );
    }

    private final function _ridnyi_com_ua_get_article( $data )
    {

        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['keywords'] = false;

        $data['page'] = preg_replace( '!<(script|style|noscript|iframe)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = explode( '<article', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( '</h1>', $data['page'], 2 );
        $data['page'] = end( $data['page'] );

        $data['page'] = explode( '</article>', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );



        preg_match_all( '!<img(.+?)src=\"(http\S+?\.(jpg|jpeg|png))\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();
        $data['images'] = array_unique( $data['images'] );

        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );


        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';
        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\](\s)\[!is', '][', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );
        $data['page'] = preg_replace( '!ridnyi.com.ua!i', '%SITENAME%', $data['page'] );


        $data['page'] = trim( $data['page'] );

        return $data;
    }

    public final function save_ridnyi_com_ua( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }
        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "ridnyi.com.ua"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-43200, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_zolotonosha_ck_ua
{
    public final function load_from_zolotonosha_ck_ua()
    {
        $feed = 'http://zolotonosha.ck.ua/feed/';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_zolotonosha_ck_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_post( $line )."\n";
        }
    }

    private final function _zolotonosha_ck_ua_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['domain'] = explode('//',$data['link'],2);
        $data['domain'] = end( $data['domain'] );
        $data['domain'] = explode( '/', $data['domain'] );
        $data['domain'] = reset( $data['domain'] );
        $data['domain'] = self::strtolower( $data['domain'] );

        $data['category'] = explode('|', $data['category'] );
        foreach( $data['category'] as $k=>$v )
        {
            $data['category'][$k] = self::strtolower(trim( preg_replace( '!(\-|\.)+!i', ' ', $v ) ) );
            if( $data['category'][$k] == 'новини' )
            {
                $data['category'][$k] = 'Золотоноша';
            }
        }
        $data['category'] = implode( '|', $data['category'] );

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        $data['keywords'] = false;
        $data['page'] = preg_replace( '!<(script|style|noscript|iframe)(.+?)(\1)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(link)(.+?)>!is', '', $data['page'] );

        $data['page'] = explode( '<h2', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( '<div class="entry', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( 'class="uptolike-buttons"', $data['page'], 2 );
        $data['page'] = reset( $data['page'] ).'>';

        /*preg_match_all( '!<img(.+?)src=\"(http\S+?\.(jpg|jpeg|png))\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();
        $data['images'] = array_unique( $data['images'] ); */

        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );
        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );
        $data['page'] = preg_replace( '!ridnyi.com.ua!i', '%SITENAME%', $data['page'] );

        $data['page'] = trim( $data['page'] );

        return $data;
    }
}

trait tr_vch_uman_in_ua
{
    public final function load_from_vch_uman_in_ua()
    {
        $feed = 'https://vch-uman.in.ua/feed/';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_vch_uman_in_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_post( $line )."\n";
        }
    }

    private final function _vch_uman_in_ua_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['domain'] = explode('//',$data['link'],2);
        $data['domain'] = end( $data['domain'] );
        $data['domain'] = explode( '/', $data['domain'] );
        $data['domain'] = reset( $data['domain'] );
        $data['domain'] = self::strtolower( $data['domain'] );

        $data['category'] = explode('|', $data['category'] );
        foreach( $data['category'] as $k=>$v )
        {
            $data['category'][$k] = self::strtolower(trim( preg_replace( '!(\-|\.)+!i', ' ', $v ) ) );
            if( $data['category'][$k] == 'новини' )
            {
                $data['category'][$k] = 'Умань';
            }
        }
        $data['category'] = implode( '|', $data['category'] );

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['keywords'] = false;
        $data['page'] = preg_replace( '!<(script|style|noscript|iframe)(.+?)(\1)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(link)(.+?)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(meta)(.+?)>!is', "\n".'$0'."\n", $data['page'] );

        $data['images'] = array();
        preg_match( '!<meta property=(\"|)og:image(\"|) content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][3])?array( 0 => $data['images'][3] ):array();

        $data['page'] = explode( '<h1', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( 'class=td-post-content', $data['page'], 2 );
        $data['page'] = '<div class="fff'.end( $data['page'] );

        $data['page'] = explode( '<div id=wpdevar_comment', $data['page'], 2 );
        $data['page'] = reset( $data['page'] ).'';

        /*preg_match_all( '!<img(.+?)src=\"(http\S+?\.(jpg|jpeg|png))\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();
        $data['images'] = array_unique( $data['images'] ); */

        $data['page'] = preg_replace( '!(\s+)!is', " ", $data['page'] );
        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );
        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+)(\s+)(.+?|)>!is', '<$1>', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );

        $data['page'] = trim( $data['page'] );

        return $data;
    }
}

trait unian
{
    public final function unian_load( $url, $keyword = array() )
    {
        echo 'START: '.$url."\n" ;
        $this->AGENT = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1';
        $this->REF = $url;
        $page = $this->curl( $url );

        if( $this->HTTP_STATUS != 200 ){ echo "HTTP STATUS ERROR!\n";  return false; }

        $page = preg_split( '!<h1!i', $page, 2 );
            $page = end( $page );
        $page = preg_split( '!scroll-loader!i', $page, 2 );
            $page = reset( $page );

        preg_match_all( '!href=\"(http(.+?)unian.ua\/m\/\S+?.html)\"!i', $page, $links );

        if( isset($links[1]) && is_array($links[1]) && count($links[1]) )
        {
            $I = 0;
            $links = $links[1];

            shuffle( $links );

            foreach( $links as $link )
            {
                $data = array();
                $data['link']       = $link;
                $data['keywords']   = '';
                $data['title']      = '';
                $data['category']   = '';

                $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($link).'%\' ;';
                $count = $this->db->super_query( $SQL );
                if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; continue; }

                $page = $this->curl( $link );
                if( $this->HTTP_STATUS != 200 ){ echo "HTTP STATUS ERROR!\n";  continue; }

                $page = preg_replace( "!<(\w+)!i", "\n".'<$1', $page );
                $page = preg_replace( "!\r!", "", $page );
                $page = preg_replace( '!\"\n!', '" ', $page );
                $page = preg_replace( "!\t!", " ", $page );
                $page = preg_replace( "! {2,}!", " ", $page );
                $page = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $page );
                $page = preg_replace( '!<\!--(.+?)-->!is', '', $page );
                $page = preg_replace( '!\n{2,}!', "\n", trim($page) );

                $data['title'] = array();
                preg_match( '!<(h1)>(.+?)<\/\1>!i', $page, $data['title'] );
                $data['title'] = isset($data['title'][2])?$data['title'][2]:'';
                $data['title'] = explode( '|', $data['title'] );
                $data['title'] = reset( $data['title'] );

                $SQL = 'SELECT count(id) FROM posts WHERE title = \''.$this->db->safesql($data['title']).'\' ;';
                $count = $this->db->super_query( $SQL );
                if( $count['count'] > 0 ){ echo "DUBLICATE [TITLE]!\n"; continue; }

                if( strlen( $data['title'] ) < 10 ){ echo "SKIP [BAD TITLE]!\n";  continue; }

                $data['description'] = array();
                preg_match( '!<meta(.+?)name="description"(.+?)content="(.+?)"!i', $page, $data['description'] );
                $data['description'] = isset($data['description'][3])?$data['description'][3]:'';

                $data['keywords'] = array();
                preg_match( '!<meta(.+?)name="keywords"(.+?)content="(.+?|)"!i', $page, $data['keywords'] );
                $data['category'] = $data['keywords'] = isset($data['keywords'][3])?$data['keywords'][3]:'';
                $data['category'] = $data['keywords'] = implode(', ', $keyword).(count($keyword)?', ':'').$data['category'];

                $page = preg_replace( '!<(\w+?)(.+?)\"(read-also|subscribe)(.+?)\1>!i', '', trim($page) );

                $page = explode( '<div class="article-text">', $page, 2 ); $page = end( $page );
                $page = explode( 'class="like-h2"', $page, 2 ); $page = end( $page ); $page = '<div'.$page;
                $page = explode( 'class="mistake', $page, 2 ); $page = reset( $page );

                $images = array();
                preg_match_all( '!<img(.+?)src=\"(\S+?\.(jpg|jpeg|png))!i', $page, $images );
                if( isset($images[2]) && is_array($images[2]) && count($images[2]) ){ $images = $images[2]; }else{ $images = array(); }
                $data['images'] = $images;

                if( !is_array($data['images']) || !count($data['images']) ){ echo "SKIP [BAD IMAGES]!\n";  continue; }

                $page = preg_replace( '!<(figure|figcaption|noscript)(.+?)(\1)>!is', '', $page );
                $page = preg_replace( '!\r!i', "\n", trim($page) );
                $page = preg_replace( '!\n!i', " ", trim($page) );
                $page = preg_replace( '!\s{2,}!i', ' ', trim($page) );
                $page = preg_replace( '!\<(p|h1|h2)!i', "\n".'<$1', trim($page) );
                $page = strip_tags($page);
                $page = preg_replace( '!\n{2,}!i', "\n", trim($page) );

                $page = explode( "\n", $page );

                foreach( $page as $k => $v )
                {
                    $v = trim( $v );
                    $page[$k] = $v;
                    if( !$page[$k] ){ unset( $page[$k] ); continue; }

                    $page[$k] = '[p]'.$page[$k].'[/p]';
                }
                $page = implode( "\n", $page );
                $data['page'] = $page;

                if( strlen($data['page'] ) < 200 ){ echo "SKIP [BAD TEXT]!\n";  continue; }

                $page = '';


                echo "ADD: ".$data['title']."\n";
                $this->save_unian( $data );
                sleep( 1 );
                $I++;
                if( $I > 50 ){ break; }
            }
        }
        else
        {
            echo "\nPOSTS NOT FOUNDED!\n";
        }
        return false;
    }

    public final function save_unian( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') )
        {
            define( 'NEWS_PARSER_USER_ID', 1 );
        }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }

        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        $raw['keywords'] = preg_replace( '!УНІАН!i', '', $raw['keywords'] );
        $raw['keywords'] = preg_replace( '!УНІАН!i', '', $raw['keywords'] );

        $raw['keywords'] = explode( ',', $raw['keywords'] );
        $raw['keywords'] = common::trim( $raw['keywords'] );
        $raw['keywords'] = array_unique($raw['keywords']);
        $raw['keywords'] = implode( ', ', $raw['keywords'] );

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "unian"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= date('Y-m-d H:i:s', time() + rand(-1382400, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }
}

trait tr_shpola_otg_gov_ua
{
    public final function load_from_shpola_otg_gov_ua()
    {
        $feed = 'http://shpola-otg.gov.ua/feed/';
        $data = $this->curl( $feed );

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_shpola_otg_gov_ua_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!"; return false; }

        echo "\n".$feed."\n".'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_post( $line )."\n";
        }
    }

    private final function _shpola_otg_gov_ua_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['domain'] = explode('//',$data['link'],2);
        $data['domain'] = end( $data['domain'] );
        $data['domain'] = explode( '/', $data['domain'] );
        $data['domain'] = reset( $data['domain'] );
        $data['domain'] = self::strtolower( $data['domain'] );

        $data['category'] = explode('|', $data['category'] );
        foreach( $data['category'] as $k=>$v )
        {
            $data['category'][$k] = self::strtolower(trim( preg_replace( '!(\-|\.)+!i', ' ', $v ) ) );
            if( $data['category'][$k] == 'новини' )
            {
                $data['category'][$k] = 'Шпола';
            }
        }
        $data['category'] = implode( '|', $data['category'] );

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        $data['keywords'] = false;
        $data['page'] = preg_replace( '!<(script|style|noscript|iframe)(.+?)(\1)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(link)(.+?)>!is', '', $data['page'] );

        $data['page'] = explode( '</h1>', $data['page'], 2 );
        $data['page'] = end( $data['page'] );

        $data['page'] = explode( '<div style="float: left;width: 100%;', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = explode( 'class="a2a_kit', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        /*preg_match_all( '!<img(.+?)src=\"(http\S+?\.(jpg|jpeg|png))\"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][2])?$data['images'][2]:array();
        $data['images'] = array_unique( $data['images'] ); */

        $data['page'] = preg_replace( '!<div class=\"(.+?)-ads-(.+?)<\/div>!i', '', $data['page'] );
        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+)(\s+)(.+?|)>!is', '<$1>', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );
        $data['page'] = preg_replace( '!\r!i', "\n", $data['page'] );
        $data['page'] = preg_replace( '!(\n{2,})!i', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\[(p|b)\](\s+| )\[\/\1\]!is', '', $data['page'] );
        $data['page'] = str_replace( '[p] [/p]', '', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );

        if( !$data['page'] || strlen($data['page']) < 200 ){ return false; }

        $data['page'] = trim( $data['page'] );

        return $data;
    }
}

trait tr_pingvin_pro
{
    // https://pingvin.pro/feed
    public final function load_from_pingvin_pro()
    {
        $feed = 'https://pingvin.pro/feed';
        $data = $this->curl( $feed );

        echo "\n".$feed."\n";

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_pingvin_pro_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!\n\n"; return false; }

        echo 'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_post( $line )."\n";
        }
    }

    private final function _pingvin_pro_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['domain'] = explode('//',$data['link'],2);
        $data['domain'] = end( $data['domain'] );
        $data['domain'] = explode( '/', $data['domain'] );
        $data['domain'] = reset( $data['domain'] );
        $data['domain'] = self::strtolower( $data['domain'] );

        $data['category'] = explode('|', $data['category'] );
        foreach( $data['category'] as $k=>$v )
        {
            $data['category'][$k] = self::strtolower(trim( preg_replace( '!(\-|\.)+!i', ' ', $v ) ) );
        }
        $data['category'] = implode( '|', $data['category'] );

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }


        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        $data['keywords'] = false;
        $data['page'] = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(link)(.+?)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+) class=\"(catsbutton|ads|singlebread|buttons)(.+?|)"(.+?)<\/\1>!is', '', $data['page'] );



        $data['page'] = explode( '</h1>', $data['page'], 2 ); $data['page'] = end( $data['page'] );
        $data['page'] = explode( 'class="wrapper', $data['page'], 2 ); $data['page'] = '<div class="wrapper '.end( $data['page'] );

        $data['page'] = explode( 'class="wpf-search-container">', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = explode( 'id="sharing"', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['page'] = explode( 'class="article-footer"', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['youtube'] = array();
        preg_match_all( '!src=\"((\S+)youtube\.com\/(\S+?))(\?\S+|)\"!i', $data['page'], $data['youtube'] );
        $data['youtube'] = isset($data['youtube'][1])?array_values($data['youtube'][1]):array();

        $data['page'] = preg_replace( '!<p(.+?)caption-attachment(.+?)\/p>!i', '', $data['page'] );
        $data['page'] = preg_replace( '!<p(.+?)caption-text(.+?)\/p>!i', '', $data['page'] );
        $data['page'] = preg_replace( '!<(iframe)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+)(\s+)(.+?|)>!is', '<$1>', $data['page'] );
        $data['page'] = preg_replace( '!^(\s+)$!is', '', $data['page'] );

        $data['page'] = preg_replace( '!<h(\d+)>(.+?)<\/h\1>!i', '[h$1]$2[/h$1]', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );

        $data['page'] = preg_replace( '!(\r+)!is', '', $data['page'] );
        $data['page'] = preg_replace( '!(\t+)!is', '', $data['page'] );
        $data['page'] = preg_replace( '!(\n{1,})!is', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\[(p|b)\](\s+| )\[\/\1\]!is', '', $data['page'] );
        $data['page'] = str_replace( '[p] [/p]', '', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );
        $data['page'] = preg_replace( '!\[(p)\]\[(h)(\d+)\](.+?)\[\/\2\3\]\[\/\1\]!i', '[$2$3]$4[/$2$3]', $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        foreach( $data['page'] as $k =>$v )
        {
            if( strlen($v) < 20 && !preg_match('!\[h(\d+?)\](.+?)\[\/h\1\]!',$v) )
            {
                unset( $data['page'][$k] );
            }
        }

        $data['page'] = implode( "\n", $data['page'] );
        if( strlen($data['page']) < 200 ){ return false; }

        $data['page'] = trim( $data['page'] );

        return $data;
    }
}

trait tr_playua_net
{
    // view-source:https://playua.net/feed/
    public final function load_from_playua_net()
    {
        $feed = 'https://playua.net/feed/';
        $data = $this->curl( $feed );

        echo "\n".$feed."\n";

        if( $this->HTTP_STATUS == 200 )
        {
            $data = $this->_get_links( $data );

            if( count($data) )
            {
                foreach( $data as $k => $item )
                {
                    $data[$k] = $this->_playua_net_get_article( $item );
                    if( !$item || !$data[$k] ){ unset( $data[$k] ); continue; }
                }
            }
        }

        if( !is_array($data) || !count($data) ){ echo "POSTS NOT FOUNDED!\n\n"; return false; }

        echo 'FOUNDED '.count($data).' posts'."\n";

        foreach( $data as $line )
        {
            if( strlen($line['title']) < 15 ){ continue; }
            if( strlen($line['page'])  < 15 ){ continue; }

            $SQL = 'SELECT COUNT(id) as count FROM posts WHERE comment LIKE \''.$this->db->safesql($line['link']).'%\' OR title = \''.$this->db->safesql($line['title']).'\';';
            $SQL = $this->db->super_query( $SQL );
            if( intval($SQL['count']) > 0 )
            {
                echo "DUBLICATE: ".$line['title']."\n";
                continue;
            }
            echo "ADDED: ".$this->save_post( $line )."\n";
        }
    }

    private final function _playua_net_get_article( $data )
    {
        $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($data['link']).'%\' OR title =\''.$this->db->safesql( $data['title'] ).'\' ;';
        $count = $this->db->super_query( $SQL );
        if( $count['count'] > 0 ){ echo "DUBLICATE!\n"; return false; }

        $data['domain'] = explode('//',$data['link'],2);
        $data['domain'] = end( $data['domain'] );
        $data['domain'] = explode( '/', $data['domain'] );
        $data['domain'] = reset( $data['domain'] );
        $data['domain'] = self::strtolower( $data['domain'] );

        $data['category'] = explode('|', $data['category'] );
        foreach( $data['category'] as $k=>$v )
        {
            $data['category'][$k] = self::strtolower(trim( preg_replace( '!(\-|\.)+!i', ' ', $v ) ) );
        }
        $data['category'] = implode( '|', $data['category'] );

        $data['page'] = $this->curl( $data['link'] );
        if( $this->HTTP_STATUS != 200 ){ return false; }

        $data['images'] = array();
        preg_match( '!<meta property="og:image" content="(.+?)"!i', $data['page'], $data['images'] );
        $data['images'] = isset($data['images'][1])?array( 0 => $data['images'][1] ):array();

        $data['keywords'] = false;
        $data['page'] = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(link)(.+?)>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+) class=\"(catsbutton|ads|singlebread|buttons)(.+?|)"(.+?)<\/\1>!is', '', $data['page'] );

        $data['page'] = explode( '</h1>', $data['page'], 2 ); $data['page'] = end( $data['page'] );
        $data['page'] = explode( 'article-header', $data['page'], 2 ); $data['page'] = '<div class="article-header '.end( $data['page'] );

        $data['page'] = explode( '<div class="post-tags">', $data['page'], 2 );
        $data['page'] = reset( $data['page'] );

        $data['youtube'] = array();
        preg_match_all( '!src=\"((\S+)youtube\.com\/(\S+?))(\?\S+|)\"!i', $data['page'], $data['youtube'] );
        $data['youtube'] = isset($data['youtube'][1])?array_values($data['youtube'][1]):array();

        $data['page'] = preg_replace( '!<(iframe)(.+?)(\1)>!is', '', $data['page'] );

        $data['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $data['page'] );
        $data['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $data['page'] );
        $data['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $data['page'] );
        $data['page'] = preg_replace( '!<(\w+)(\s+)(.+?|)>!is', '<$1>', $data['page'] );
        $data['page'] = preg_replace( '!^(\s+)$!is', '', $data['page'] );

        $data['page'] = trim( strip_tags( $data['page'] ) );

        $data['page'] = preg_replace( '!(\r+)!is', '', $data['page'] );
        $data['page'] = preg_replace( '!(\t+)!is', '', $data['page'] );
        $data['page'] = preg_replace( '!(\n{1,})!is', "\n", $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        $data['page'] = '[p]'.implode( "[/p]\n[p]", $data['page'] ).'[/p]';

        $data['page'] = self::htmlspecialchars_decode($data['page']);
        $data['page'] = self::html_entity_decode($data['page']);
        $data['page'] = preg_replace( '!(\s{2,})!is', ' ', $data['page'] );
        $data['page'] = preg_replace( '!\[(p|b)\](\s+| )\[\/\1\]!is', '', $data['page'] );
        $data['page'] = str_replace( '[p] [/p]', '', $data['page'] );

        $data['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $data['page'] );

        $data['page'] = explode( "\n", $data['page'] );
        foreach( $data['page'] as $k =>$v ){ if( strlen($v) < 50 ){ unset( $data['page'][$k] ); }}
        $data['page'] = implode( "\n", $data['page'] );
        if( strlen($data['page']) < 200 ){ return false; }

        $data['page'] = trim( $data['page'] );

        return $data;
    }
}

trait dt_ua
{
    // https://dt.ua/tags/%D0%BA%D0%BE%D1%81%D0%BC%D0%BE%D1%81?page=1
    public final function load_from_dt_ua( $url, $pages = array( 1 ) )
    {
        $pages = common::integer( $pages );
        if( !is_array($pages) ){ return false; }

        $articles = array();

        $i = 0;
        foreach( $pages as $page )
        {
            $i++;
            $curr_url = $url.'?page='.$page;
            $data = $this->curl( $curr_url );

            if( $this->HTTP_STATUS != 200 ){ continue; }

            echo "\n".$curr_url."\n";

            // echo $data; exit;

            $data = explode( '<ul class=\'news_list\'>', $data, 2 );
            $data = end( $data );

            $data = explode( '</ul>', $data );
            $data = reset( $data );

            preg_match_all( '!<li(.+?)news_item(.+?)>(.+?)<\/li>!is', $data, $data );
            if( !isset($data[3]) ){ continue; }

            $data = $data[3];

            foreach( $data as $data_line )
            {
                $i++;
                $articles[$i] = array();

                preg_match( '!href=(\'|")(.+?\.html)(\'|")!i', $data_line, $articles[$i]['link'] );
                preg_match( '!news_title\'>(.+?)<\/!is', $data_line, $articles[$i]['title'] );
                preg_match( '!news_date\'>(.+?)<\/!is', $data_line, $articles[$i]['date'] );

                if( !isset($articles[$i]['link'][2]) ) { continue; }
                if( !isset($articles[$i]['title'][1]) ){ continue; }
                if( !isset($articles[$i]['date'][1]) ) { continue; }

                $articles[$i]['link'] = 'https://dt.ua'.$articles[$i]['link'][2];
                $articles[$i]['title'] = common::trim( $articles[$i]['title'][1] );
                $articles[$i]['date'] = strtotime( common::trim( $articles[$i]['date'][1] ) );
                if( $articles[$i]['date'] < (time() - 60*60*24*360) ){ $articles[$i]['date'] = time(); }
                $articles[$i]['date'] = date('Y-m-d H:i:s', $articles[$i]['date']);

                ////////////////////////////////

                $SQL = 'SELECT count(id) FROM posts WHERE comment LIKE \''.$this->db->safesql($articles[$i]['link']).'%\' OR title =\''.$this->db->safesql( $articles[$i]['title'] ).'\' ;';
                $count = $this->db->super_query( $SQL );
                if( $count['count'] > 0 ){ echo "\tDUBLICATE! - ".$articles[$i]['title']."\n"; continue; }

                $articles[$i]['domain'] = explode('//',$articles[$i]['link'],2);
                $articles[$i]['domain'] = end( $articles[$i]['domain'] );
                $articles[$i]['domain'] = explode( '/', $articles[$i]['domain'] );
                $articles[$i]['domain'] = reset( $articles[$i]['domain'] );
                $articles[$i]['domain'] = self::strtolower( $articles[$i]['domain'] );

                $articles[$i]['category'] = '';

                $articles[$i]['page'] = $this->curl( $articles[$i]['link'] );
                if( $this->HTTP_STATUS != 200 ){ continue; }

                $articles[$i]['images'] = array();
                preg_match( '!<meta property="og:image" content="(.+?)"!i', $articles[$i]['page'], $articles[$i]['images'] );
                $articles[$i]['images'] = isset($articles[$i]['images'][1])?array( 0 => $articles[$i]['images'][1] ):array();

                $articles[$i]['keywords'] = false;

                $articles[$i]['page'] = preg_replace( '!<(script|style|noscript)(.+?)(\1)>!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!<(link)(.+?)>!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!<(\w+) class=\"(catsbutton|ads|singlebread|buttons)(.+?|)"(.+?)<\/\1>!is', '', $articles[$i]['page'] );


                $articles[$i]['page'] = explode( '</h1>', $articles[$i]['page'], 2 ); $articles[$i]['page'] = end( $articles[$i]['page'] );
                $articles[$i]['page'] = explode( '<div class=\'article_body\'>', $articles[$i]['page'], 2 ); $articles[$i]['page'] = ''.end( $articles[$i]['page'] );

                $articles[$i]['page'] = explode( '<div class="banner', $articles[$i]['page'], 2 );
                $articles[$i]['page'] = reset( $articles[$i]['page'] );

                $articles[$i]['page'] = explode( '<ul class=\'hashtags\'>', $articles[$i]['page'], 2 );
                $articles[$i]['page'] = reset( $articles[$i]['page'] );

                $articles[$i]['page'] = preg_replace( '!<div class=\'article_attached(.+?)\/div>!is', '', $articles[$i]['page'] );


                $articles[$i]['youtube'] = array();
                preg_match_all( '!src=\"((\S+)youtube\.com\/(\S+?))(\?\S+|)\"!i', $articles[$i]['page'], $articles[$i]['youtube'] );
                $articles[$i]['youtube'] = isset($articles[$i]['youtube'][1])?array_values($articles[$i]['youtube'][1]):array();

                $articles[$i]['page'] = preg_replace( '!<(iframe)(.+?)(\1)>!is', '', $articles[$i]['page'] );

                $articles[$i]['page'] = preg_replace( '!<(strong|b)>(.+?)<\/(\1)>!i', '[b]$2[/b]', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!<(p)>(.+?)<\/(\1)>!i', '$2'."\n", $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!<figure(.+?)figure>!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!<(\w+)(\s+)(.+?|)>!is', '<$1>', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!^(\s+)$!is', '', $articles[$i]['page'] );

                $articles[$i]['page'] = preg_replace( '!<h(\d+)>(.+?)<\/h\1>!i', '[h$1]$2[/h$1]', $articles[$i]['page'] );

                $articles[$i]['page'] = trim( strip_tags( $articles[$i]['page'] ) );

                $articles[$i]['page'] = preg_replace( '!(\r+)!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!(\t+)!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!(\n{1,})!is', "\n", $articles[$i]['page'] );

                $articles[$i]['page'] = explode( "\n", $articles[$i]['page'] );
                $articles[$i]['page'] = '[p]'.implode( "[/p]\n[p]", $articles[$i]['page'] ).'[/p]';

                $articles[$i]['page'] = self::htmlspecialchars_decode($articles[$i]['page']);
                $articles[$i]['page'] = self::html_entity_decode($articles[$i]['page']);
                $articles[$i]['page'] = preg_replace( '!(\s{2,})!is', ' ', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!\[(p|b)\](\s+| )\[\/\1\]!is', '', $articles[$i]['page'] );
                $articles[$i]['page'] = str_replace( '[p] [/p]', '', $articles[$i]['page'] );

                $articles[$i]['page'] = preg_replace( '!\[p\]\[\/p\]!i', '', $articles[$i]['page'] );
                $articles[$i]['page'] = preg_replace( '!\[(p)\]\[(h)(\d+)\](.+?)\[\/\2\3\]\[\/\1\]!i', '[$2$3]$4[/$2$3]', $articles[$i]['page'] );

                $articles[$i]['page'] = explode( "\n", $articles[$i]['page'] );
                foreach( $articles[$i]['page'] as $k =>$v )
                {
                    if( strlen($v) < 20 && !preg_match('!\[h(\d+?)\](.+?)\[\/h\1\]!',$v) )
                    {
                        unset( $articles[$i]['page'][$k] );
                    }
                }

                $articles[$i]['page'] = implode( "\n", $articles[$i]['page'] );
                if( strlen($articles[$i]['page']) < 200 ){ continue; }

                $articles[$i]['page'] = trim( $articles[$i]['page'] );

                echo "ADDED: ".$this->save_post( $articles[$i] )."\n";
            }
        }
    }
}


class parser
{
    use basic,
        db_connect,
            tr_provce_ck_ua,
            dt_ua,
            tr_18000_com_ua,
            tr_vycherpno_ck_ua,
            tr_ridnyi_com_ua,
            unian,
            tr_dzvin_media,
            tr_vch_uman_in_ua,
            tr_pingvin_pro,
            tr_cikavosti,
            tr_playua_net,
            tr_shpola_otg_gov_ua,
            tr_ukr_media,
            tr_zolotonosha_ck_ua;

    const CACHE_VAR_POSTS = 'posts';

    private $HTTP_STATUS = true;
    private $REF = false;
    private $UTF2WIN = true;

    private $AGENT = 'Mozilla/5.0 (Windows NT 6.1; rv:69.0) Gecko/20100101 Firefox/69.0';
    private $TAGS_CATEGORIES = array();

    public function __construct()
    {
        $this->__cconnect_2_db();

        $f = MODS_DIR.DS.'module.'._MOD_.DS.DOMAIN.'.php';
        if( !file_exists($f) ){ echo "NO TAGS!"; exit; }
        $this->TAGS_CATEGORIES = require( $f );
    }

    private final function _save_images( &$raw )
    {
        $files = array();
        if( is_array($raw['images']) && count($raw['images']) )
        {
            foreach( $raw['images'] as $k=>$image_url )
            {
                $image_url = common::win2utf( $image_url );

                if( strpos( $image_url, '?' ) != false ){ continue; }

                $image_url = explode( '?', $image_url );
                $image_url = reset( $image_url );

                $ext = explode( '.', $image_url );
                $ext = end( $ext );
                $ext = strtolower($ext);

                $image = UPL_DIR.DS.'images'.DS.date('Y-m-d').DS.date('Y-m-d_H-i-s').'_'.rand(0,999).'.'.$ext;

                if( !is_dir( dirname($image) ) )
                {
                    mkdir( dirname($image), 0777 );
                    chmod( dirname($image), 0777 );
                }

                if( file_exists($image) ){ unlink($image); }
                $this->UTF2WIN = false;
                file_put_contents( $image, $this->curl( $image_url ) );

                echo "\t\tDownload image: ".$image_url."\n";
                echo "\t\t\tto: ".$image."\n";
                if( $this->HTTP_STATUS != 200 ){ echo "\t\t\t\tWRONG[HTTP]!"."\n"; continue; }
                if( !file_exists($image) )     { echo "\t\t\t\tWRONG!"."\n"; continue; }

                chmod( $image, 0666 );

                $file = array();
                $file['status'] = 1;
                $file['error'] = false;
                $file['filename'] = $image;

                $file = images::_upload_process( $file, config::get() );

                $raw['images'][$k] = str_replace( ROOT_DIR, '', $file['filename'] );
                $files[] = $file;
                unset( $file );
            }
            $raw['images'] = array_values($raw['images']);

            foreach( $raw['images'] as $k => $v )
            {
                if( preg_match( '!http:!i', $v ) )
                {
                    unset( $raw['images'][$k] );
                }
            }

            $raw['images'] = array_values($raw['images']);
        }
        else
        {
            $raw['images'] = array();
        }



        return $files;
    }
    private final function _get_links( $data )
    {
        preg_match_all( '!<(item)>(.+?)<\/\1>!is', $data, $data );
        $data = isset($data[0])?$data[0]:array();

        foreach( $data as $k => $_item )
        {
            $item = array();
            foreach( array( 'link', 'title' ) as $area )
            {
                preg_match_all( '!<('.$area.')>(.+?)<\/\1>!is', $_item, $item[$area] );
                $item[$area] = isset($item[$area][2])?reset( $item[$area][2] ):array();
            }

            $item['title'] = self::htmlspecialchars_decode($item['title']);
            $item['title'] = self::html_entity_decode($item['title']);
            $item['title'] = preg_replace( '!\((.+?)\)!i', '', $item['title'] );

            $item['title'] = trim( $item['title'] );

            preg_match_all( '!<(category)>(.+?)<\/\1>!is', $_item, $item['category'] );
            $item['category'] = isset($item['category'][2])?$item['category'][2]:array();
            $item['category'] = implode( "\n", $item['category'] );
            preg_match_all( '!CDATA\[(.+?)\]!is', $item['category'], $item['category'] );
            $item['category'] = implode( '|', isset($item['category'][1])?$item['category'][1]:array() );

            $data[$k] = $item;
        }

        return $data;
    }

    public final static function filter_utf8( $str )
    {
        if( is_array( $str ) )
        {
            foreach( $str as $k => $v )
            {
                $str[$k] = self::filter_utf8($v);
            }
        }
        else
        {
            $str = iconv("Windows-1251","UTF-8//TRANSLIT",$str);
            $str = iconv("UTF-8","Windows-1251//TRANSLIT",$str);

            $str = iconv("Windows-1251","UTF-8//IGNORE",$str);
            $str = iconv("UTF-8","Windows-1251//IGNORE",$str);
        }

        return $str;
    }



    ///////////////////////////////////////////////
    // Эмулятор браузера :)
    private final function curl( $url )
    {
        $id = curl_init($url);

        // Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1

        if( !$this->REF ){ $this->REF = $url; }

        curl_setopt($id, CURLOPT_HEADER, false );
        curl_setopt($id, CURLOPT_NOBODY, false );
        curl_setopt($id, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($id, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($id, CURLOPT_MAXREDIRS, 5 );
        curl_setopt($id, CURLOPT_TIMEOUT, 3 );
        curl_setopt($id, CURLOPT_AUTOREFERER, true );
        curl_setopt($id, CURLOPT_REFERER, $this->REF );
        curl_setopt($id, CURLOPT_USERAGENT, $this->AGENT );

        $page = curl_exec($id);

        $this->HTTP_STATUS = curl_getinfo($id);

        /*if( $this->HTTP_STATUS['http_code'] != 200 )
        {
            var_export($this->HTTP_STATUS);exit;
        }     */

        /*echo "\n";
        var_export($this->HTTP_STATUS);
        echo "\n"; */

        $this->HTTP_STATUS = $this->HTTP_STATUS['http_code'];
        curl_close($id);

        if( $this->UTF2WIN )
        {
            $page = common::utf2win( $page );
        }else{ $this->UTF2WIN = true; }

        return $page;
    }

    public final function fix_utf()
    {
        $SQL = 'SELECT * FROM posts WHERE id > 0 ORDER by created_time DESC;';
        $SQL = $this->db->query( $SQL );

        while( ($row = $this->db->get_row($SQL)) !== false )
        {
            foreach( $row as $k => $v )
            {
                $row[$k] = iconv("UTF-8","Windows-1251//TRANSLIT",$row[$k]);
                $row[$k] = iconv("Windows-1251","UTF-8//TRANSLIT",$row[$k]);

                $row[$k] = iconv("UTF-8","Windows-1251//IGNORE",$row[$k]);
                $row[$k] = iconv("Windows-1251","UTF-8//IGNORE",$row[$k]);
                $row[$k] = common::stripslashes($row[$k]);
            }


            $Q = 'UPDATE posts SET title=\''.$this->db->safesql($row['title']).'\', descr=\''.$this->db->safesql($row['descr']).'\', short_post=\''.$this->db->safesql($row['short_post']).'\', full_post=\''.$this->db->safesql($row['full_post']).'\', keywords=\''.$this->db->safesql($row['keywords']).'\', comment=\''.$this->db->safesql($row['comment']).'\'  WHERE id='.$row['id'].';';
            $this->db->query( $Q );
            echo $row['title']."\n";
        }

    }

    static public final function htmlspecialchars_decode($data)
    {
        if (!is_scalar($data) && !is_array($data))
        {
            self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
        }
        if (is_array($data))
        {
            return array_map('self::htmlspecialchars_decode', $data);
        }
        return htmlspecialchars_decode($data, ENT_QUOTES | ENT_HTML5);
    }

    static public final function html_entity_decode($data)
    {
        if (!is_scalar($data) && !is_array($data))
        {
            self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
        }
        if (is_array($data))
        {
            return array_map('self::html_entity_decode', $data);
        }
        return html_entity_decode($data, ENT_QUOTES | ENT_HTML5, CHARSET);;
    }

    public final function get_tags( $post_tags = array() )
    {
        $_return = array();
        $post_tags = common::strtolower( $post_tags );

        foreach( $this->TAGS_CATEGORIES as $main => $tags )
        {
            foreach( $post_tags as $tag )
            {
                if( in_array( $tag, $tags ) ){ $_return[] = $main; }
            }
        }

        $_return = array_unique($_return);
        $_return = array_values($_return);

        sort( $_return );

        return $_return;
    }

    public final function load_and_process_tags()
    {
        $SQL = 'SELECT id, comment FROM posts WHERE comment != \'\' AND id != 0 ORDER BY created_time DESC;';
        $SQL = $this->db->query( $SQL );

        $_TAGS_COLLECTION = array();

        while( ( $row = $this->db->get_row($SQL) ) !== false )
        {
            $post_id = intval($row['id']);

            if( !$post_id ){ continue; }

            $row['comment'] = explode( "\n", $row['comment'] );
            $row['comment'] = isset($row['comment'][1])?$row['comment'][1]:false;

            if( !$row['comment'] ){ continue; }

            $row['comment'] = str_replace( ',', '|', $row['comment'] );
            $row['comment'] = explode( '|', $row['comment'] );
            $row['comment'] = common::trim( $row['comment'] );

            $_TAGS_COLLECTION = array_merge( $_TAGS_COLLECTION, $row['comment'] );

            $tags = $this->get_tags( $row['comment'] );

            if( is_array($tags) && count($tags) )
            {
                foreach( $tags as $tag )
                {
                    $SQUERY = 'SELECT id FROM tags WHERE name = \''.$this->db->safesql($tag).'\';';
                    $tag_id = $this->db->super_query( $SQUERY );
                    $tag_id = isset($tag_id['id'])?$tag_id['id']:false;

                    if( !$tag_id )
                    {
                        $SQUERY = 'INSERT INTO tags (name,altname) VALUES (\''.$this->db->safesql($tag).'\', \''.common::totranslit($this->db->safesql($tag)).'\') RETURNING id;';
                        $tag_id = $this->db->super_query( $SQUERY );
                        $tag_id = $tag_id['id'];
                    }

                    $SQUERY = array();
                    $SQUERY[] = 'BEGIN;';
                    $SQUERY[] = 'DELETE FROM posts_tags WHERE post_id = \''.$post_id.'\'::INTEGER AND tag_id = \''.$tag_id.'\'::INTEGER;';
                    $SQUERY[] = 'INSERT INTO posts_tags ( post_id, tag_id ) VALUES (\''.$post_id.'\'::INTEGER, \''.$tag_id.'\'::INTEGER );';
                    $SQUERY[] = 'COMMIT;';

                    $SQUERY = implode( "\n", $SQUERY );

                    $this->db->query( $SQUERY );
                }
            }

            cache::clean( self::CACHE_VAR_POSTS );
        }

        $addict_tagging = array();
        $_CATEGS = array();
        foreach( $this->TAGS_CATEGORIES as $c => $arr )
        {
            $c_id = $this->db->super_query( 'SELECT id FROM tags WHERE name=\''.$this->db->safesql($c).'\';' );
            $c_id = isset($c_id['id'])?$c_id['id']:0;

            if( !$c_id ){ continue; }

            $_CATEGS = array_merge( $_CATEGS, self::strtolower( $arr ) );
            $addict_tagging[$c] = array();

            foreach( self::strtolower( $arr ) as $tgs )
            {
                $addict_tagging[$c][] = 'svector @@ plainto_tsquery(\''.$this->db->safesql($tgs).'\')';
            }
            $addict_tagging[$c] = 'SELECT DISTINCT id FROM posts WHERE '.implode( ' OR ', $addict_tagging[$c] ).';';
            $q = $this->db->query( $addict_tagging[$c] );

            $addict_tagging[$c] = array();
            while( ($row = $this->db->get_row($q)) !== false )
            {
                $addict_tagging[$c][] = '('.$c_id.','.$row['id'].')';
            }

            if( !count($addict_tagging[$c]) ){ unset($addict_tagging[$c]); continue; }

            $addict_tagging[$c] = array
            (
                'BEGIN;',
                'INSERT INTO posts_tags (tag_id, post_id) VALUES '.implode(', ',$addict_tagging[$c]).' ON CONFLICT DO NOTHING;',
                'COMMIT;'
            );

            $addict_tagging[$c] = $this->db->query( implode("\n",$addict_tagging[$c]) );
            $addict_tagging[$c] = null;
            unset( $addict_tagging[$c] );
        }

        cache::clean();

        $_CATEGS = self::strtolower( $_CATEGS );
        $_CATEGS = array_unique($_CATEGS);
        sort( $_CATEGS );

        $_TAGS_COLLECTION = self::strtolower( $_TAGS_COLLECTION );
        $_TAGS_COLLECTION = array_unique($_TAGS_COLLECTION);
        sort( $_TAGS_COLLECTION );

        echo "\n\nTAGS INFO\n\n";

        foreach( $_TAGS_COLLECTION as $tag )
        {
            if( !in_array( $tag, $_CATEGS ) )
            {
                echo 'MISSED TAG: '.$tag."\n";
            }
        }


    }

    public final function process_tags( $raw_tags, $post_id )
    {
        $raw_tags = str_replace( ',', '|', $raw_tags );
        $raw_tags = explode( '|', $raw_tags );
        $raw_tags = common::trim( $raw_tags );

        $tags = $this->get_tags( $raw_tags );

        if( is_array($tags) && count($tags) )
        {
            foreach( $tags as $tag )
            {
                $SQUERY = 'SELECT id FROM tags WHERE name = \''.$this->db->safesql($tag).'\';';
                $tag_id = $this->db->super_query( $SQUERY );
                $tag_id = isset($tag_id['id'])?$tag_id['id']:false;

                if( !$tag_id )
                {
                    $SQUERY = 'INSERT INTO tags (name,altname) VALUES (\''.$this->db->safesql($tag).'\', \''.common::totranslit($this->db->safesql($tag)).'\') RETURNING id;';
                    $tag_id = $this->db->super_query( $SQUERY );
                    $tag_id = $tag_id['id'];
                }

                $SQUERY = array();
                $SQUERY[] = 'BEGIN;';
                $SQUERY[] = 'DELETE FROM posts_tags WHERE post_id = \''.$post_id.'\'::INTEGER AND tag_id = \''.$tag_id.'\'::INTEGER;';
                $SQUERY[] = 'INSERT INTO posts_tags ( post_id, tag_id ) VALUES (\''.$post_id.'\'::INTEGER, \''.$tag_id.'\'::INTEGER );';
                $SQUERY[] = 'COMMIT;';
                echo "\t\tTAG:".$tag."\n";

                $SQUERY = implode( "\n", $SQUERY );

                $this->db->query( $SQUERY );
            }

        }

        cache::clean( self::CACHE_VAR_POSTS );
        return true;
    }

    public final function save_post( $raw )
    {
        if( !defined('NEWS_PARSER_USER_ID') ){ define( 'NEWS_PARSER_USER_ID', 1 ); }

        $raw['page'] = preg_replace( '!\[p\](\s+|)\[\/p\]!is', '', $raw['page'] );


        if( strpos( $raw['page'], 'https:/' ) !== false ){ echo "\tURL IN TEXT! SKIP!\n"; return false; }

        $files = $this->_save_images( $raw );

        if( !count($files) ){ echo "\tNO IMAGES! SKIP!\n"; return false; }


        $raw['page'] = explode( "\n", $raw['page'] );

        $page = array();
        foreach( $raw['page'] as $i => $p )
        {
            $page[] = $p;
            if( is_array($raw['images']) && isset($raw['images'][$i]) && !preg_match( '!(http|https):!', $raw['images'][$i] ) )
            {
                $page[] = '[center][img]'.$raw['images'][$i].'[/img][/center]';
                unset( $raw['images'][$i] );
            }
        }

        if( count($raw['images']) )
        {
            foreach( $raw['images'] as $img )
            {
                if( preg_match( '!(http|https):!', $img ) ){ continue; }
                $page[] = '[center][img]'.$img.'[/img][/center]';
            }
        }
        $raw['page'] = implode("\n", $page);
        $page = null; unset( $page );

        // <iframe width="1280" height="720" src="https://www.youtube.com/embed/B5sZ3q2GfEE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

        if( isset($raw['youtube']) && is_array($raw['youtube']) && count($raw['youtube']) )
        {
            $raw['page'] = explode( "\n", $raw['page'] );
            $raw['youtube'] = array_values($raw['youtube']);

            $page = array();
            $i = 0;
            foreach( $raw['page'] as $line )
            {
                $i++;
                $page[] = $line;

                if( $i > 2 && isset($raw['youtube'][0]) && $raw['youtube'][0] )
                {
                    $page[] = '[youtube]'.$raw['youtube'][0].'[/youtube]';
                    unset( $raw['youtube'][0] );
                }
            }

            if( count($raw['youtube']) )
            {
                $raw['youtube'] = "\n".'[youtube]'.implode('[/youtube]'."\n".'[youtube]',$raw['youtube']).'[/youtube]';
            }
            else
            {
                $raw['youtube'] = '';
            }

            $raw['page'] = $page = implode( "\n", $page ).$raw['youtube'];
            $page = null; unset( $page );
        }

        $_posts = new posts;
        $data = array();
        $data['post:id']          = 0;
        $data['categ:id']         = 1;

        $data['post:posted']      = 1;
        $data['post:fixed']       = 0;
        $data['post:static']      = 0;

        $data['post:alt_title']   = md5($raw['link']);
        $data['post:title']       = $raw['title'];
        $data['post:descr']       = $raw['title'];
        $data['post:short_post']  = preg_replace( '!(\s+)!', ' ', strip_tags( bbcode::bbcode2html( $raw['page'] ) ) );
        $data['post:full_post']   = $raw['page'].'<div class="source">[right]<a rel="nofollow noreferrer" href="'.$raw['link'].'" target="_blank">За матеріалами "'.$raw['domain'].'"</a>[/right]</div>';
        $data['post:keywords']    = $raw['keywords'];
        $data['post:comment']     = $raw['link']."\n".$raw['category']."\n".implode('%%%',$raw['images']);
        $data['post:created_time']= ( isset($raw['date']) && $raw['date'] )?$raw['date']:date('Y-m-d H:i:s', time() + rand(-43200, 43200 ) );

        foreach( $data as $k=>$v ){ $data[$k] = self::filter_utf8( $v ); }
        $post_id = $_posts->save( $data );

        $this->process_tags( $raw['category'], $post_id );

        foreach( $files as $file )
        {
            $SQL = 'UPDATE images SET post_id=' . $post_id . ' WHERE md5=\''.$file['md5'].'\' AND post_id=0;';
            $this->db->query( $SQL );
        }
        return $data['post:title'];
    }


}

?>