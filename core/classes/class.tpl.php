<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

class tpl
{
    use basic;

    private $cache = array();
    private $theme = array();
    private $buffer = array();
    private $current = false;

    public  $head_tags = array
            (
                'title' => '',
                'description' => '',
                'keywords' => '',
                'charset' => '',
            );

    public final function info( $title=false, $message, $level='notice' )
    {
        $this->load( 'info' );
        $this->set( '{info:title}',     self::htmlspecialchars( $title ) );
        $this->set( '{info:message}',   self::htmlspecialchars( $message ) );
        $this->set( '{info:level}',     self::htmlspecialchars( $level ) );
        $this->compile( 'info' );
    }

    public final function load( $skin  = false, $enable_current = true )
    {
        if( !$skin ){ return false; }

        $skin = explode( '/', $skin );
        foreach( $skin as $k=>$v ){ $skin[$k] = self::totranslit($v); }
        $skin = implode( DS, $skin );
        $filename = CURRENT_SKIN.DS.$skin.'.tpl';

        if( !file_exists( $filename ) )
        {
            self::err( 'TEMPLATE NOT FOUND: '.$skin.'.tpl' );
            exit;
        }

        if( !isset($this->cache[$skin]) )
        {
            $this->cache[$skin] = self::read_file( $filename );
        }

        $this->theme[$skin] = $this->parse_global_tags( $this->cache[$skin] );

        if( $enable_current ){ $this->current = $skin; }
        return $this->theme[$skin];
    }

    public function exist( $tag, $skin=false )
    {
        $skin = $skin?$skin:$this->current;
        if( isset($this->theme[$skin]) )
        {
            return ( strpos( $this->theme[$skin], $tag ) !== false )?true:false;    
        }
        return false;
    }

    public function set( $tag, $value, $skin=false )
    {
        $skin = $skin?$skin:$this->current;
        if( isset($this->theme[$skin]) )
        {
            if( is_array($value) )
            {
                self::err( 'tag '.$tag.' have array value! String is needed! File: '.__FILE__ );
                exit;
            }
            else
            {
                $this->theme[$skin] = str_replace( $tag, $value, $this->theme[$skin] );
            }
        }
    }

    public final function set_callback( $mask, $callback = false, $skin = false )
    {
        $skin = $skin?$skin:$this->current;

        if( isset($this->theme[$skin]) )
        {
            if( is_array($mask) )
            {
                self::err( 'mask '.$mask.' have array value! String is needed! File: '.__FILE__ );
                exit;
            }
            $this->theme[$skin] = preg_replace_callback( $mask, $callback, $this->theme[$skin] );
        }
    }

    public final function set_block( $mask, $value, $skin=false )
    {
        $skin = $skin?$skin:$this->current;

        if( isset($this->theme[$skin]) )
        {
            if( is_array($mask) || is_array($value) )
            {
                self::err( 'mask have array value! String is needed! File: '.__FILE__ );
                exit;
            }
            $this->theme[$skin] = preg_replace( $mask, $value, $this->theme[$skin] );
        }
    }

    public final function compile( $skin=false )
    {
        if( !$skin ){ $skin = $this->current; }

        if( !isset($this->buffer[$skin]) ){ $this->buffer[$skin] = ''; }

        $this->buffer[$skin] = $this->buffer[$skin].$this->theme[$skin];
        $this->theme[$skin] = '';
    }

    public final function result( $skin=false )
    {
        if( !$skin ){ $skin = $this->current; }
        if( !isset($this->buffer[$skin]) ){ $this->buffer[$skin] = ''; }

        $data = $this->buffer[$skin];
        $this->clean($skin);

        if( $skin == 'content' )
        {
            foreach( $this->head_tags as $key => $value )
            {
                $data = str_replace( '{'.self::strtolower($key).'}', $value, $data );
            }

            foreach( $this->buffer as $key => $value )
            {
                $data = str_replace( '{global:'.$key.'}', $value, $data );
                $this->clean( $key );
            }

            $data = preg_replace( '!\{global:(\w+?)\}!', '', $data );
        }

        return $data;
    }

    public final function ins( $skin=false, $data )
    {
        if( !$skin ){ $skin = $this->current; }
        if( !isset($this->buffer[$skin]) ){ $this->buffer[$skin] = ''; }

        $this->buffer[$skin] = $this->buffer[$skin].$data;
    }

    public final function clean( $skin=false )
    {
        $skin = $skin?$skin:$this->current;
        $this->theme[$skin] = false;
        $this->cache[$skin] = false;
        $this->buffer[$skin] = false;
        unset( $this->cache[$skin] );
        unset( $this->theme[$skin] );
        unset( $this->buffer[$skin] );
    }

    private final function parse_tag_area( $array = array() )
    {
        if( !is_array($array) || !isset($array[1]) || !isset($array[2]) ){ return false; }

        $tag        = self::strtolower( self::filter( self::trim( $array[1] ) ) );
        $area       = explode( '|', $array[2] );
        $content    = self::trim( $array[3] );

        $area = self::trim( $area );
        $area = self::filter( $area );
        $area = self::strtolower( $area );

        if(  in_array( self::strtolower( _AREA_ ), $area ) && $tag == 'area'    ){ return $content; }
        if( !in_array( self::strtolower( _AREA_ ), $area ) && $tag == 'notarea' ){ return $content; }

        return false;
    }

    private final function parse_global_tags( $data )
    {
        $data = str_replace( '{MOD}',       _MOD_, $data );
        $data = str_replace( '{PAGE_ID}',   (_PAGE_ID==0)?1:_PAGE_ID, $data );
        $data = str_replace( '{CATEG_ID}',  _CATEG_ID, $data );
        $data = str_replace( '{TAG_ID}',    _TAG_ID, $data );
        $data = str_replace( '{AREA}',      _AREA_, $data );

        if( strpos( $data, '{TOP_TAG_ID}' ) !== false )
        {
            $data = str_replace( '{TOP_TAG_ID}',tags::get_top_tag_id(), $data );
        }

        while( strpos($data, '{RAND}' ) !== false )
        {
            $data = preg_replace ( '!\{RAND\}!i', str_shuffle(md5(rand( 1000000, 9999999 ))), $data, 1 );
        }

        $data = preg_replace_callback( '!\[(area|notarea):([a-z\|]+?)\](.+?)\[\/\1\]!is', array( $this, 'parse_tag_area' ), $data );
        $data = preg_replace( '!\[area:('._AREA_.')\](.+?)\[\/area:\1\]!is',        '$2', $data );
        $data = preg_replace( '!\[area:(.+?)\](.+?)\[\/area:\1\]!is',               '', $data );
        $data = preg_replace( '!\[notarea:('._AREA_.')\](.+?)\[\/notarea:\1\]!is',  '',   $data );
        $data = preg_replace( '!\[notarea:(.+?)\](.+?)\[\/notarea:\1\]!is',         '$2', $data );

        if( defined('_PAGE_ID') && _PAGE_ID > 0 )
        {
            $data = preg_replace( '!\[(pages)\](.+?)\[\/\1\]!is',   '$2',   $data );
            $data = preg_replace( '!\[(nopages)\](.+?)\[\/\1\]!is', '',     $data );
        }
        else
        {
            $data = preg_replace( '!\[(pages)\](.+?)\[\/\1\]!is',   '',     $data );
            $data = preg_replace( '!\[(nopages)\](.+?)\[\/\1\]!is', '$2',   $data );
        }

        $data = preg_replace( '!\[mod:('._MOD_.')\](.+?)\[\/mod:\1\]!is', '$2', $data );
        $data = preg_replace( '!\[mod:(.+?)\](.+?)\[\/mod:\1\]!is', '', $data );

        $data = preg_replace( '!\[nomod:('._MOD_.')\](.+?)\[\/nomod:\1\]!is', '', $data );
        $data = preg_replace( '!\[nomod:(.+?)\](.+?)\[\/nomod:\1\]!is', '$2', $data );

        $data = str_replace( '{SKINDIR}', str_replace( ROOT_DIR, '', CURRENT_SKIN ), $data );
        $data = str_replace( '{HOME}', HOMEURL, $data );
        $data = str_replace( '{CHARSET}', CHARSET, $data );
        $data = str_replace( '{DOMAIN}',  DOMAIN, $data );
        $data = str_replace( '%SITENAME%',  DOMAIN, $data );

        while( preg_match( '!\{(\d+?)(\*|\+|\:|\-)(\d+?)\}!i', $data, $arif ) )
        {
            $res = '';
            $arif[1] = common::integer($arif[1]);
            $arif[3] = common::integer($arif[3]);

                if( $arif[2] == '*' )       { $res = $arif[1] * $arif[3]; }
            elseif( $arif[2] == ':' )   { $res = $arif[1] / $arif[3]; }
            elseif( $arif[2] == '+' )   { $res = $arif[1] + $arif[3]; }
            elseif( $arif[2] == '-' )   { $res = $arif[1] - $arif[3]; }

            //echo $arif[0].' : '.$res."\n";
            $data = str_replace( $arif[0], $res, $data );
        }

        $data = $this->parse_tags_include( $data );
        $data = $this->parse_tags_login_nologin( $data );
        $data = $this->parse_tags_curr_user_info( $data );
        $data = $this->parse_tags_group( $data );
        $data = $this->parse_categ_list( $data );
        $data = $this->parse_tags_list( $data );
        $data = $this->parse_tags_info( $data );

        $data = $this->parse_custom_posts( $data );

        return $data;
    }

    private final function parse_custom_posts( $data )
    {
        $POSTS = false;
        while( preg_match( '!\{custom:(\d+?)\-(\d+?):tags:([0-9,]+):(\w+)\}!i', $data, $tag ) )
        {
            //echo $tag[0]."\n";

            $offset = common::integer( isset($tag[1])?$tag[1]:0 );
            $limit  = common::integer( isset($tag[2])?$tag[2]:0 );
            $tags   = explode( ',', isset($tag[3])?$tag[3]:'' );
            $tags   = common::integer( $tags );
            $tags   = array_values(array_unique(is_array( $tags )?$tags:array( $tags )));
            $skin   = isset($tag[4])?$tag[4]:false;

            sort( $tags );
            while( count($tags) && $tags[0] == 0 )
            {
                unset( $tags[0] );
                sort( $tags );
            }

            if( !is_object($POSTS) ){ $POSTS = new posts; }

            $filter['limit']  = $limit;
            $filter['offset'] = $offset;
            $filter['tag.id'] = $tags;
            $filter['post.posted'] = 1;

            //var_export($filter); echo "\n";

            $res = $POSTS->get_custom( $filter, $skin );

            //echo "\n";

            $data = str_replace( $tag[0], $res, $data );

            //echo "\n\n";
        }

        return $data;
    }
    private final function parse_tags_include( $data )
    {
        $tag = false;
        if( preg_match_all( '!\{\@include=([a-z0-9\/]+?)\}!i', $data, $tag ) )
        {
            if( isset($tag[1]) && is_array($tag[1]) && count($tag[1]) )
            {
                foreach( $tag[1] as $key=>$elem )
                {
                    $elem = explode( '/', $elem );
                    $elem  = self::totranslit( $elem );
                    $elem  = implode( DS, $elem );
                    $file = CURRENT_SKIN.DS.$elem.'.tpl';
                    if( !file_exists( $file ) ){ $elem = false; }

                    if( $elem && isset($tag[0][$key]) && $tag[0][$key] )
                    {
                        $data  = str_replace( $tag[0][$key], $this->load( $elem, false ), $data );
                    }
                }
            }
        }
        return $data;
    }

    private final function parse_tags_login_nologin( $data )
    {
        if( preg_match( '!\[(login|nologin)\](.+?)\[\/\1\]!is', $data ) )
        {
            $data = str_replace( (CURRENT_USER_ID?'[login]':'[nologin]'), '', $data );
            $data = str_replace( (CURRENT_USER_ID?'[/login]':'[/nologin]'), '', $data );

            $data = preg_replace( '!\[('.(CURRENT_USER_ID?'nologin':'login').')\](.+?)\[\/\1\]!is', '', $data );
        }
        return $data;
    }

    private final function parse_tags_curr_user_info( $data )
    {
        if( strpos( $data, '{curr.user:' ) )
        {
            if( !isset($GLOBALS['_user']) || !is_object($GLOBALS['_user'])){ self::err( '«м?нну "_user" втрачено!' ); }

            foreach( $GLOBALS['_user']->get_curr_user_info()['user'] as $key => $value )
            {
                $data = str_replace( '{curr.user:'.$key.'}', self::htmlspecialchars(self::stripslashes($value)), $data );
            }
        }
        return $data;
    }

    private final function parse_tags_group( $data )
    {
        if( preg_match( '!\[(group:\d+?)\](.+?)\[\/\1\]!is', $data ) )
        {
          $data = str_replace( '[group:'.CURRENT_GROUP_ID.']', '', $data );
          $data = str_replace( '[/group:'.CURRENT_GROUP_ID.']', '', $data );
          $data = preg_replace( '!\[(group:\d+?)\](.+?)\[\/\1\]!is', '', $data );
        }

        if( preg_match( '!\[(nogroup:\d+?)\](.+?)\[\/\1\]!is', $data ) )
        {
          $data = str_replace( '[nogroup:'.CURRENT_GROUP_ID.']', '', $data );
          $data = str_replace( '[/nogroup:'.CURRENT_GROUP_ID.']', '', $data );
          $data = preg_replace( '!\[(nogroup:\d+?)\](.+?)\[\/\1\]!is', '', $data );
        }

        return $data;
    }

    private final function parse_tags_list( $data )
    {
        if( preg_match_all( '!\{taglist:(\w+?)\}!i', $data, $tag ) )
        {
            $skins = isset($tag[1])?$tag[1]:false;
            if( !$skins ){ return $data; }

            if( !isset($GLOBALS['_TAGS']) || !is_object($GLOBALS['_TAGS']) ){ $GLOBALS['_TAGS'] = new tags; }
            $tags = $GLOBALS['_TAGS']->get_tags();

            foreach( $skins as $skin )
            {
                foreach( $tags as $row )
                {
                    if( !$row['news_count'] ){ continue; }

                    $this->load( $skin );

                    $this->set( '{tag:url}', $GLOBALS['_TAGS']->get_url( $row['name'] ) );

                    foreach( $row as $k => $v )
                    {
                        if( is_array($v) ){ continue; }
                        $this->set( '{tag:'.$k.'}', $v );
                        $this->set( '{tag:'.$k.':html}', common::htmlentities( $v ) );
                    }
                    $this->compile( $skin );
                }
                $data = str_replace( '{taglist:'.$skin.'}', $this->result( $skin ), $data );
            }
        }


        if( preg_match_all( '!\{tagstop:((\w+?):(\d+?))\}!i', $data, $tag ) )
        {
            $skins = isset($tag[1])?$tag[1]:false;
            if( !$skins ){ return $data; }

            if( !isset($GLOBALS['_TAGS']) || !is_object($GLOBALS['_TAGS']) ){ $GLOBALS['_TAGS'] = new tags; }
            $tags = array();
            $tags = $GLOBALS['_TAGS']->get_top_tags();

            foreach( $skins as $skin )
            {
                $skin  = explode( ':', $skin, 2 );

                $count = end( $skin );
                    $count = common::integer( $count );
                $skin  = reset( $skin );
                    $skin = common::totranslit( $skin );

                $i = 0;
                foreach( $tags as $row )
                {
                    if( !$row['news_count'] ){ continue; }

                    $this->load( $skin );

                    $this->set( '{tag:url}', $GLOBALS['_TAGS']->get_url( $row['name'] ) );

                    foreach( $row as $k => $v )
                    {
                        if( is_array($v) ){ continue; }
                        $this->set( '{tag:'.$k.'}', $v );
                        $this->set( '{tag:'.$k.':html}', common::htmlentities( $v ) );
                    }
                    $this->compile( $skin );

                    $i++;
                    if( $i >= $count ){ break; }
                }
                $data = str_replace( '{tagstop:'.$skin.':'.$count.'}', $this->result( $skin ), $data );
            }
        }
        return $data;
    }

    private final function parse_tags_info( $data )
    {
        // {tag:9:name}

        while( preg_match( '!\{tag:(\d+):(\w+)\}!i', $data ) )
        {
            var_export($data);
            exit;
        }

        return $data;
    }

    private final function parse_categ_list( $data )
    {
        if( strpos( $data, '{categ:list}' ) )
        {
          if( !isset($GLOBALS['_CATEG']) || !is_object($GLOBALS['_CATEG']) ){ $GLOBALS['_CATEG'] = new categ; }
          $data = str_replace( '{categ:list}',  $GLOBALS['_CATEG']->get_categ_opts(), $data );
        }

        return $data;
    }


}

?>