<?php
//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){      require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !trait_exists( 'db_connect' ) ){ require( CLASSES_DIR.DS.'trait.db_connect.php' ); }
if( !class_exists( 'bbcode' ) ){     require( CLASSES_DIR.DS.'class.bbcode.php' ); }

//////////////////////////////////////////////////////////////////////////////////////////

class posts
{
    use basic, db_connect;

    const CACHE_VAR_POSTS = 'posts';

    public final function delete( $post_id = 0, $hash = false )
    {
        $post_id = self::integer( $post_id );
        if( !$post_id){ ajax::set_error( 1, '������� ��������� ���������!' ); }
        if( self::md5( date('Ymd') . $post_id ) != $hash ){ ajax::set_error( 1, '������� ��������� ���������!' ); }

        $SQL = 'DELETE FROM posts WHERE id=\''.$post_id.'\';';
        $this->db->query( $SQL );

        cache::clean( self::CACHE_VAR_POSTS );
    }

    public function get_custom( $filter, $template )
    {
        $posts_count = 0;
        $tpl = new tpl;
        foreach( $this->get( $filter, $posts_count ) as $id => $row )
        {
            $this->html( $row, $tpl, $template );
        }
        return $tpl->result($template);
    }

    public final function save( $data = false )
    {
      if( !$data || !is_array($data) || !count($data) ){ return 0; }
      $data = self::stripslashes( $data );

      $data['post:id']              = isset($data['post:id'])?          self::integer($data['post:id']):false;
      $data['categ:id']             = isset($data['categ:id'])?         self::integer($data['categ:id']):false;

      $data['post:posted']          = isset($data['post:posted'])?      self::integer($data['post:posted']):0;
      $data['post:fixed']           = isset($data['post:fixed'])?       self::integer($data['post:fixed']):0;
      $data['post:static']          = isset($data['post:static'])?      self::integer($data['post:static']):0;

      $data['post:alt_title']       = isset($data['post:alt_title'])?   self::totranslit($data['post:alt_title']):false;
      $data['post:title']           = isset($data['post:title'])?       self::trim($data['post:title']):false;
      $data['post:descr']           = isset($data['post:descr'])?       self::trim($data['post:descr']):false;
      $data['post:short_post']      = isset($data['post:short_post'])?  self::trim($data['post:short_post']):false;
      $data['post:full_post']       = isset($data['post:full_post'])?   self::trim($data['post:full_post']):false;
      $data['post:keywords']        = isset($data['post:keywords'])?    self::trim($data['post:keywords']):false;
      $data['post:comment']         = isset($data['post:comment'])?     self::trim($data['post:comment']):false;
      $data['post:created_time']    = isset($data['post:created_time'])?self::trim($data['post:created_time']):date('Y-m-d H:i:s');

      $_ID = self::integer( $data['post:id'] );

      if( !$data['post:alt_title'] || strlen($data['post:alt_title']) < 1 )
      {
          $data['post:alt_title'] = self::totranslit($data['post:title']);
      }

      $data['post:short_post'] = bbcode::bbcode2html( $data['post:short_post'] );
      $data['post:full_post']  = bbcode::bbcode2html( $data['post:full_post'] );

      $_2db = array();
      $_2db['title']            = $data['post:title'];
      $_2db['alt_title']        = $data['post:alt_title'];
      $_2db['descr']            = $data['post:descr'];
      $_2db['short_post']       = $data['post:short_post'];
      $_2db['full_post']        = $data['post:full_post'];
      $_2db['comment']          = $data['post:comment'];
      //$_2db['svector']          = self::strip_tags( self::stripslashes( self::strip_tags($data['post:full_post']) ) );
      $_2db['svector']          = false;
      $_2db['keywords']         = $data['post:keywords'];
      $_2db['posted']           = $data['post:posted'];
      $_2db['fixed']            = $data['post:fixed'];
      $_2db['static']           = $data['post:static'];
      $_2db['category']         = $data['categ:id'];

      if( !$_ID ){ $_2db['author_id'] = CURRENT_USER_ID; }
      if( !$_ID && ( !defined('CURRENT_USER_ID') || !CURRENT_USER_ID ) && defined('NEWS_PARSER_USER_ID') && NEWS_PARSER_USER_ID ){ $_2db['author_id'] = NEWS_PARSER_USER_ID; }

      $_2db = array_map( array( &$this->db, 'safesql' ), $_2db );

      $SQL = '';
      $new_post = false;
      if( $_ID )
      {
        $new_post = false;
        foreach( $_2db as $k=>$v ){ $_2db[$k] = '"'.$k.'" = \''.$v.'\''; }
        $SQL = 'UPDATE posts SET '.implode( ', ', $_2db ).' WHERE id=\''.$_ID.'\' RETURNING id;';
      }
      else
      {
        $new_post = true;
        $SQL = 'INSERT INTO posts ("'.implode('", "', array_keys($_2db)).'") VALUES (\''.implode('\', \'', array_values($_2db)).'\') RETURNING id;';
      }

      // file_put_contents(ROOT_DIR.DS.'SQL.txt', $SQL);

      $_ID = $this->db->super_query( $SQL );
      $_ID = isset($_ID['id'])?self::integer($_ID['id']):0;

      cache::clean( self::CACHE_VAR_POSTS );

      if( $new_post )
      {
          images::update( $_ID );
          files::update( $_ID );
      }

      $_curr_post = $this->get( array( 'post.id' => $_ID ) );
      if( isset($_curr_post[$_ID]) )
      {
          $_curr_post = $_curr_post[$_ID];
          preg_match( '!src=\"\/(\S+(jpeg|jpg|png))\"!i', $_curr_post['post']['full_post'], $tphoto );
      }
      $tphoto = isset($tphoto[1]) ? $tphoto[1] : false;

      if( $tphoto && isset($_curr_post['post']) && file_exists( ROOT_DIR.DS.$tphoto ) && !intval( $_curr_post['post']['repost_tg'] ) )
      {
          $_config    = config::get();
          $_curr_post['post']['short_post'] = strip_tags(common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( common::stripslashes( common::html_entity_decode( common::stripslashes( $_curr_post['post']['short_post']) ) )) ) ));
          $_curr_post['post']['title']      = common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( common::stripslashes( common::html_entity_decode( common::stripslashes( strip_tags( $_curr_post['post']['title'] ) ) ) )) ) );

          $post = $_curr_post['post']['short_post'];
          if( strlen($post) > 600 )
          {
              $post = substr( $post, 0, 600 ).'...';
          }

          if( strlen($_curr_post['post']['short_post']) > 800 )
          {
              $_curr_post['post']['short_post'] = substr( $_curr_post['post']['short_post'], 0, 800 ).'...';
          }

          if( strlen($_curr_post['post']['title']) > 200 )
          {
              $_curr_post['post']['title'] = substr( $_curr_post['post']['title'], 0, 200 ).'...';
          }

          //common::telegram_send_photo( ROOT_DIR.DS.$tphoto, $_curr_post['post']['title'] );

          $link = SCHEME.'://'.DOMAIN.$this->get_url( $_curr_post );

          $tme = 'https://t.me/iv?url='.trim($link).'&rhash=a2ccd40756e9eb';

          $tphoto = $tphoto?'<a href="'.HOMEURL.$tphoto.'">&#8205;</a>':'';

          $telegram =
            $tphoto
            .'<b>'.$_curr_post['post']['title'].'</b>'
            ."\n\n".$_curr_post['post']['short_post']
            ."\n\n".'<a href="'.$link.'">'.$_config['title'].' - '.$_config['site_descr'].'</a>';

          common::telegram_send( $telegram );

          $this->db->query( 'UPDATE posts SET repost_tg=1 WHERE id=\''.$_ID.'\';' );
          cache::clean( self::CACHE_VAR_POSTS );
      }

      return $_ID;
    }

    static public final function resend2telegram( $count )
    {
        $count = common::integer( $count );

        if( !$count ){ return false; }

        $_posts = new posts;

        $filter = array();
        $filter['limit'] = $count;
        $filter['post.posted'] = true;
        $filter['post.tg_posted'] = false;
        $filter['order'] = array();
        $filter['order']['posts.created_time'] = 'posts.created_time ASC';
        $filter['order']['posts.fixed']        = 'posts.fixed ASC';
        $filter['order']['posts.posted']       = 'posts.posted DESC';

        foreach( $_posts->get( $filter ) as $_curr_post )
        {
            $_ID = common::integer( $_curr_post['post']['id'] );

            if( !preg_match( '!src=\"\/(\S+(jpeg|jpg|png))\"!i', $_curr_post['post']['full_post'], $tphoto ) ){ continue; }
            $tphoto = isset($tphoto[1]) ? $tphoto[1] : false;

              if( $tphoto && isset($_curr_post['post']) && file_exists( ROOT_DIR.DS.$tphoto ) && !intval( $_curr_post['post']['repost_tg'] ) )
              {
                  $_config    = config::get();
                  $_curr_post['post']['short_post'] = strip_tags(common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( common::stripslashes( common::html_entity_decode( common::stripslashes( $_curr_post['post']['short_post']) ) )) ) ));
                  $_curr_post['post']['title']      = common::stripslashes( common::htmlspecialchars_decode( common::stripslashes( common::stripslashes( common::html_entity_decode( common::stripslashes( strip_tags( $_curr_post['post']['title'] ) ) ) )) ) );

                  $post = $_curr_post['post']['short_post'];
                  if( strlen($post) > 600 )
                  {
                      $post = substr( $post, 0, 600 ).'...';
                  }

                  if( strlen($_curr_post['post']['short_post']) > 800 )
                  {
                      $_curr_post['post']['short_post'] = substr( $_curr_post['post']['short_post'], 0, 800 ).'...';
                  }

                  if( strlen($_curr_post['post']['title']) > 200 )
                  {
                      $_curr_post['post']['title'] = substr( $_curr_post['post']['title'], 0, 200 ).'...';
                  }

                  //common::telegram_send_photo( ROOT_DIR.DS.$tphoto, $_curr_post['post']['title'] );

                  $link = SCHEME.'://'.DOMAIN.$_posts->get_url( $_curr_post );

                  $tme = 'https://t.me/iv?url='.trim($link).'&rhash=a2ccd40756e9eb';

                  $tphoto = $tphoto?'<a href="'.HOMEURL.$tphoto.'">&#8205;</a>':'';

                  $telegram =
                    $tphoto
                    .'<b>'.$_curr_post['post']['title'].'</b>'
                    ."\n\n".$_curr_post['post']['short_post']
                    ."\n\n".'<a href="'.$link.'">'.$_config['title'].'</a>';

                  common::telegram_send( $telegram );

                  $_posts->db->query( 'UPDATE posts SET repost_tg=1 WHERE id=\''.$_ID.'\';' );

                  echo $_ID." - ".$_curr_post['post']['title']."\n";
              }
        }

        cache::clean( self::CACHE_VAR_POSTS );
        return true;
    }

    public final function listposts_html( $data = array(), &$tpl = false /*OBJECT*/, $skin = 'post_list' )
    {
      foreach( $data as $post_id => $value )
      {
        $tpl->load( $skin );

        $edit_url = '/index.php?mod='._MOD_.'&submod='._SUBMOD_.'&post_id='.$post_id;
        $tpl->set( '{edit_url}', $edit_url );

        foreach( array( 'post', 'categ', 'usr' ) as $_tag_group )
        {
          $_inf = &$value[$_tag_group];
          foreach( $_inf as $tag => $val )
          {
            $val = self::stripslashes($val);
            $val = self::htmlspecialchars( $val );
            $tpl->set( '{'.$_tag_group.':'.$tag.'}', $val );
          }
        }
        $tpl->compile( $skin );
      }
      return false;
    }

    public final static function get_tag_url( $tag )
    {
        if( !isset($GLOBALS['_TAGS']) || !is_object($GLOBALS['_TAGS']) )
        {
            $GLOBALS['_TAGS'] = new tags;
        }
        $_TAGS = &$GLOBALS['_TAGS'];

        return $_TAGS->get_url($tag);
    }

    public final static function get_url( &$data = array() )
    {
        if( !isset($GLOBALS['_CATEG']) || !is_object($GLOBALS['_CATEG']) )
        {
            $GLOBALS['_CATEG'] = new categ;
        }
        $_CATEG = &$GLOBALS['_CATEG'];

        $post_id = self::integer( $data['post']['id'] );
        $link = $_CATEG->get_url( self::integer( $data['categ']['id'] ) ).$data['post']['id'].'-'.self::totranslit( $data['post']['alt_title'] ).'.html';
        return $link;
    }

    public final function get( $filters = array(), &$count = false )
    {
        if( !is_array($filters) ){ $filters = array(); }

        $_config    = config::get();
        $_categories = categ::get();

        if( isset($filters['offset']) )
        {
            $filters['offset'] = self::integer( $filters['offset'] );
            if( $filters['offset'] > 0 ){ $filters['offset'] = $filters['offset'] - 1; }
        }
        else
        {
            $filters['offset'] = 0;
        }

        $filters['nullpost']    = self::integer( (isset($filters['nullpost'])?$filters['nullpost']:0) ) ? true : false;
        $filters['offset']      = self::integer( (isset($filters['offset'])?$filters['offset']:0) );
        $filters['limit']       = self::integer( (isset($filters['limit'])?$filters['limit']:$_config['posts_limit']) );
        $filters['uncache']     = self::integer( (isset($filters['uncache'])?$filters['uncache']:false) )? true : false;
        $filters['post.categ']  = self::integer( (isset($filters['post.categ'])?$filters['post.categ']:false) );
        $filters['post.id']     = self::integer( (isset($filters['post.id'])?$filters['post.id']:false) );
        $filters['tag.id']      = self::integer( (isset($filters['tag.id'])?$filters['tag.id']:false) );
        $filters['post.posted'] = self::integer( (isset($filters['post.posted'])?$filters['post.posted']:false) );
        $filters['post.fixed']  = self::integer( (isset($filters['post.fixed'])?$filters['post.fixed']:false) );
        $filters['post.static'] = self::integer( (isset($filters['post.static'])?$filters['post.static']:false) );
        $filters['order']       = self::filter( ( ( isset($filters['order']) && is_array($filters['order']) && count($filters['order']) ) ?$filters['order']:false) );
        $filters['searchTerm']  = isset($filters['searchTerm'])?self::filter(self::trim($filters['searchTerm'])):false;

        if( isset($filters['post.tg_posted']) )
        {
            $filters['post.tg_posted'] = self::integer( $filters['post.tg_posted'] )?true:false;
        }


        /////////////////
        if( $filters['nullpost'] )
        {
          $filters['limit'] = 1;
          $filters['offset'] = 0;
          $filters['post.categ'] = false;
          $filters['post.id'] = false;
          $filters['post.posted'] = false;
          $filters['post.fixed'] = false;
          $filters['post.static'] = false;
          $filters['tag.id'] = false;
          unset($filters['post.tg_posted']);
        }
        /////////////////

        $SELECT = array();
        $FROM   = array();
        $WHERE  = array();
        $ORDER  = array();

        $SELECT['posts.id']              = 'post.id';
        $SELECT['posts.title']           = 'post.title';
        $SELECT['posts.alt_title']       = 'post.alt_title';
        $SELECT['posts.descr']           = 'post.descr';
        $SELECT['posts.short_post']      = 'post.short_post';
        $SELECT['posts.author_id']       = 'post.author_id';
        $SELECT['posts.created_time']    = 'post.created_time';
        $SELECT['posts.keywords']        = 'post.keywords';
        $SELECT['posts.posted']          = 'post.posted';
        $SELECT['posts.fixed']           = 'post.fixed';
        $SELECT['posts.static']          = 'post.static';
        $SELECT['posts.full_post']       = 'post.full_post';
        $SELECT['posts.repost_tg']       = 'post.repost_tg';

        $SELECT['posts.category']        = 'posts.category';

        $SELECT['usr.login']             = 'usr.login';
        $SELECT['usr.email']             = 'usr.email';

        $FROM['posts']       = 'posts';
        // $FROM['categories']  = 'LEFT JOIN categories as categ ON ( categ.id = posts.category ) -- JOIN';
        // $FROM['posts_tags']  = 'LEFT JOIN posts_tags as ptags ON ( ptags.post_id = posts.id AND ptags.tag_id > 0 ) -- JOIN';
        $FROM['users']       = 'LEFT JOIN users as usr ON ( usr.id = posts.author_id ) -- JOIN';

        if( strlen($filters['searchTerm']) >=3 )
        {
            $WHERE['svector'] = 'posts.svector @@ plainto_tsquery(\''.$this->db->safesql($filters['searchTerm']).'\')';
        }

        if( !$filters['nullpost'] )
        {
          $WHERE['posts.id']            = 'posts.id > 0';
          $WHERE['posts.category']      = 'posts.category > 0';
          $WHERE['posts.created_time']  = 'posts.created_time <= ( NOW() + interval \'1 year\' )';

          if( $filters['post.posted'] ){ $WHERE['post.posted'] = 'posts.posted = '.$filters['post.posted']; }
        }
        else
        {
          $WHERE['posts.id'] = 'posts.id = 0';
        }

        if( $filters['post.categ'] > 0 )
        {
            $WHERE['posts.category'] = 'posts.category = '.$filters['post.categ'];
        }

        if( isset($filters['post.tg_posted']) )
        {
            $WHERE['posts.repost_tg'] = 'posts.repost_tg = \''.common::integer($filters['post.tg_posted']).'\'::int2';
        }

        if( $filters['post.id'] > 0 )
        {
            $WHERE['post.id'] = 'posts.id = '.$filters['post.id'];
        }

        if( !is_array($filters['tag.id']) && $filters['tag.id'] > 0 )
        {
            $WHERE['tags.id'] = 'ARRAY[\''.$filters['tag.id'].'\'::int8] && posts.tag_id';
        }

        if( is_array($filters['tag.id']) && count($filters['tag.id']) )
        {
            $WHERE['tags.id'] = 'posts.tag_id && ARRAY[ \''.implode( '\'::int8, \'', common::integer( $filters['tag.id'] ) ).'\'::int8 ]';
        }

        if( isset($WHERE['post.id']) && $WHERE['post.id'] )
        {
            $WHERE = array( 'post.id' => $WHERE['post.id'] );
            unset( $filters['offset'] );
            unset( $filters['limit'] );
        }
        else
        {
            if( !$filters['order'] )
            {
                $ORDER['posts.created_time'] = 'posts.created_time DESC';
                $ORDER['posts.fixed']        = 'posts.fixed DESC';
                $ORDER['posts.posted']       = 'posts.posted ASC';
            }
            else
            {
                $ORDER = $this->db->safesql( $filters['order'] );
            }

        }

        $filters['limit']   = isset($filters['limit'])?$filters['limit']:$_config['posts_limit'];
        $filters['offset']  = isset($filters['offset'])?$filters['offset']:'0';

        if( $SELECT && is_array($SELECT) && count($SELECT) )
        {
            foreach( $SELECT as $key => $name )
            {
                $SELECT[$key] = "\n\t".''.$key.' as "'.$name.'"';
            }
            $SELECT = implode( ', ', array_values($SELECT) );
        }
        else
        { common::err( 'Error with $SELECT directive!' ); }

        if( $FROM && is_array($FROM) && count($FROM) )
        {
            foreach( $FROM as $key => $name )
            {
                $FROM[$key] = "\n\t".''.$name;
            }
            $FROM = implode( '', array_values($FROM) );
        }
        else
        { common::err( 'Error with $FROM directive!' ); }

        $SQL =  'SELECT '."\n-- SELECT ".$SELECT."\n-- SELECT\n".
                'FROM '.$FROM."\n".
                'WHERE '."\n\t".implode( ' AND'."\n\t", $WHERE )." \n".
                "-- ORDER\n".
                ( count($ORDER)?'ORDER BY '.implode( ', ', array_values($ORDER) )." \n":'' ).
                "-- ORDER\n".
                "-- OFFSET\n".
                ( isset($filters['offset']) && $filters['limit'] ?'OFFSET '.$filters['offset'].' LIMIT '.$filters['limit'].';'."\n":'').
                "-- OFFSET\n".
                ($filters['uncache']?'':self::trim(QUERY_CACHABLE))."\n-- USER_ID: ".abs(intval(CURRENT_USER_ID))."\n\n";

        //echo $SQL;

        $countSQL = preg_replace( '!-- SELECT(.+?)-- SELECT!is', ' count( posts.id ) as count ', $SQL );
        $countSQL = preg_replace( '!-- OFFSET(.+?)-- OFFSET!is', '', $countSQL );
        $countSQL = preg_replace( '!(OFFSET|LIMIT)(\s+?)(\d+)!is', '', $countSQL );
        $countSQL = preg_replace( '!-- ORDER(.+?)-- ORDER!is', '', $countSQL );

        $countSQL = explode( "\n", $countSQL );
        foreach($countSQL as $n => $l)
        {
            if( isset($WHERE['post.id']) && $WHERE['post.id'] )
            {
                $countSQL[$n] = preg_replace( '!^(.+?)-- JOIN(.+?|)$!i', '', $countSQL[$n] );
            }
        }
        $countSQL = implode( "\r\n", $countSQL );


        $_var = self::CACHE_VAR_POSTS.'-'.self::md5($SQL);
        $data = cache::get( $_var );

        if( !$data )
        {
            $data = array();
            $data['count'] = $this->db->get_count( $countSQL );
            $data['rows'] = array();

            $SQL  =  $this->db->query( $SQL );

            while( $row = $this->db->get_row($SQL) )
            {
                // $_categories
                $row['posts.category']  = common::integer( $row['posts.category'] );
                $row['categ.id']        = common::integer( $row['posts.category'] );
                $row['categ.altname']   = common::trim( $_categories[$row['categ.id']]['altname'] );
                $row['categ.name']      = common::trim( $_categories[$row['categ.id']]['name'] );

                // var_export($_categories);exit;

                $row['post.created_time'] = self::en_date( $row['post.created_time'], 'Y-m-d H:i:s' );
                $data['rows'][$row['post.id']] = array();
                foreach( $row as $k => $v )
                {
                    $k = explode( '.', $k, 2 );
                    if( !isset($data['rows'][$row['post.id']][$k[0]]) ){ $data['rows'][$row['post.id']][$k[0]] = array(); }

                    $data['rows'][$row['post.id']][$k[0]][$k[1]] = $v;
                }

                $data['rows'][$row['post.id']]['tags'] = array();
            }
            $this->db->free( $SQL );

            if( is_array($data['rows']) && count( $data['rows'] ) )
            {
                $tagSQL =   'SELECT tg.*, ptags.post_id '.
                            'FROM tags as tg '.
                            'LEFT JOIN posts_tags as ptags ON (tg.id = ptags.tag_id) '.
                            'WHERE tg.id > 0 AND ptags.post_id IN ('.implode(',',array_keys($data['rows'])).') '.
                            'ORDER by tg.name; '.($filters['uncache']?'':self::trim(QUERY_CACHABLE));

                $tagSQL = $this->db->query( $tagSQL );

                while( ($tag = $this->db->get_row($tagSQL))!=false )
                {
                    if( !isset($data['rows'][$tag['post_id']]['tags'][$tag['id']]) )
                    {
                        $data['rows'][$tag['post_id']]['tags'][$tag['id']] = array();
                    }
                    $data['rows'][$tag['post_id']]['tags'][$tag['id']] = $tag;
                }
                $this->db->free( $tagSQL );
            }

            cache::set( $_var, $data );
        }

        $count = $data['count'];
        return $data['rows'];
    }

    public static final function parse_attach( array $array )
    {
        if( !is_array($array) || !isset($array['3']) ){ return false; }

        $md5 = self::filter( $array['3'] );
        if( !$md5 ){ return false; }

        $new_name = self::filter( $array['2'] );

        $data = files::get_info( $md5 );

        if( !$data || !is_array($data) || !count($data) ){ return false; }

        $new_name = $new_name?$new_name:$data['orig_name'];
        $data['orig_name'] = $new_name;

        $url = files::make_url( $md5, $new_name );

        $attach = new tpl;
        $attach->load( 'attach' );
        $attach->set( '{tag:url}', $url );

        foreach( $data as $k=>$v )
        {
            $attach->set( '{tag:'.$k.'}', $v );
        }


        $attach->compile( 'attach' );
        return $attach->result( 'attach' );
    }

    private final static function parse_images2tags( string $text, object &$tpl, $area = 'short' )
    {
        preg_match_all( '!<img(.+?)src="(\/.+?)"!i', $text, $text );

        if( is_array( $text ) && isset($text[2]) && is_array($text[2]) && count($text[2]) )
        {
            foreach( array_values($text[2]) as $i => $img )
            {
                $tpl->set( '{'.$area.'-image:'.$i.'}', $img );

                $img_mini = $img;
                if( basename( dirname($img_mini) ) !== 'mini' && file_exists(ROOT_DIR.dirname($img).DS.'mini'.DS.basename($img)) )
                {
                    $img_mini = dirname($img).DS.'mini'.DS.basename($img);
                }
                $tpl->set( '{'.$area.'-image:'.$i.':force-mini}', $img_mini );

                $tpl->set_block( '!\[('.$area.'-image):'.$i.'\](.+?)\[\/\1\]!is', '$2');
                $tpl->set_block( '!\[('.$area.'-noimage):'.$i.'\](.+?)\[\/\1\]!is', '' );
            }
        }

        $tpl->set_block( '!\[('.$area.'-image):(\d+?)\](.+?|)\[\/\1\]!is', '' );
        $tpl->set_block( '!\[('.$area.'-noimage):(\d+?)\](.+?)\[\/\1\]!is', '$3' );
        $tpl->set_block( '!\{'.$area.'-image:(\d+?)\}!is', '' );

        return false;
    }

    public final function html( $data = array(), &$tpl, $skin = 'postshort' )
    {
        if( !isset($GLOBALS['_CATEG']) || !is_object($GLOBALS['_CATEG']) ){ $GLOBALS['_CATEG'] = new categ; }
        $_CATEG = &$GLOBALS['_CATEG'];

        $tpl->load( $skin );

        $data['post']['short_post'] = str_replace( '%SITENAME%',  DOMAIN, $data['post']['short_post'] );
        $data['post']['full_post']  = str_replace( '%SITENAME%',  DOMAIN, $data['post']['full_post'] );

        $data = self::stripslashes( $data );

        if( isset($data['post']['short_post']) )
        {
            $data['post']['short_post'] = preg_replace_callback( '!\[attach(|\|(.+?))\](\w+?)\[\/attach\]!i', array( $this, 'parse_attach' ), $data['post']['short_post'] );
            self::parse_images2tags( $data['post']['short_post'], $tpl, 'short' );
        }

        if( isset($data['post']['full_post']) )
        {
            $data['post']['full_post'] = preg_replace_callback( '!\[attach(|\|(.+?))\](\w+?)\[\/attach\]!i', array( $this, 'parse_attach' ), $data['post']['full_post'] );
            self::parse_images2tags( $data['post']['full_post'], $tpl, 'full' );
        }

        $tpl->set( '{hash:key}',            self::md5( date('Ymd').self::integer($data['post']['id']) ) );
        $tpl->set( '{post:id}',             self::integer($data['post']['id']));
        $tpl->set( '{post:url}',            self::get_url( $data ) );
        $tpl->set( '{post:author_id}',      self::integer($data['post']['author_id']));
        $tpl->set( '{post:short_post}',     self::trim( $data['post']['short_post'] ) );
        $tpl->set( '{post:short_post:230}', self::trim( mb_substr(strip_tags($data['post']['short_post']),0,230) ) );
        $tpl->set( '{post:created_time}',   $data['post']['created_time'] );
        $tpl->set( '{categ:id}',            self::integer( $data['categ']['id'] ) );
        $tpl->set( '{categ:url}',           $_CATEG->get_url( self::integer($data['categ']['id']) ) );

        if( $tpl->exist( '{related}' ) )
        {
            $tpl->set( '{related}',             $this->get_related( $data['post']['id'] ) );
        }

        $tpl->set( '{post:created_time:','{post:'.self::strtotime($data['post']['created_time']).':' );
        $tpl->set_callback( '!\{post:(\d+?):(.+?)\}!i', __CLASS__.'::parse_date' );

        if( isset($data['post']['full_post']) ){ $tpl->set( '{post:full_post}',   self::trim( $data['post']['full_post'] ) ); }

        foreach( $data as $key => $value )
        {
            if( in_array( $key, array('post' , 'categ') ) && is_array($value) )
            {
                foreach( $value as $k=>$v )
                {
                    $tpl->set( '{'.$key.':'.$k.'}',         self::htmlspecialchars( self::html_entity_decode( self::stripslashes($v) ) ) );
                    $tpl->set( '{'.$key.':'.$k.':strip}',   self::htmlspecialchars( self::strip_tags( self::html_entity_decode( self::stripslashes($v) ) ) ) );
                }
            }
        }

        $tags = '';
        if( is_array($data['tags']) && count($data['tags']) )
        {
            $tags = array();
            foreach($data['tags'] as $val)
            {
                $val = self::stripslashes( $val );
                $val = self::trim( $val );
                $val = self::htmlspecialchars( $val );
                $tags[] = '<a rel="tag" href="'.self::get_tag_url($val['name']).'" title="'.$val['name'].'">'.$val['name'].'</a>';
            }
            $tags = implode( '', $tags );
        }

        $tpl->set( '{taglist}', $tags );

        $tpl->compile( $skin );
        return false;
    }

    public final static function parse_date( $array = array() )
    {
        $date = isset($array[1])?self::integer($array[1]):false;
        $mask = isset($array[2])?self::filter($array[2]):false;

        if( !$date || !$mask ){ return false; }

        return date( $mask, $date );
    }

    public final function editpost_html( $data = array(), &$tpl = false /*OBJECT*/, $skin = 'post_edit' )
    {
      foreach( $data as $post_id => $value )
      {
        $tpl->load( $skin );

        $tpl->set( '{hash:key}',        self::md5( date('Ymd').self::integer($value['post']['id']) ) );
        $tpl->set( '{post:short_post}', bbcode::html2bbcode( self::stripslashes($value['post']['short_post']) ) );
        $tpl->set( '{post:full_post}',  bbcode::html2bbcode( self::stripslashes($value['post']['full_post']) ) );

        foreach( $value as $_tag_group => $_inf )
        {
          foreach( $_inf as $tag => $val )
          {
            $val = self::stripslashes($val);
            $val = self::htmlspecialchars( $val );

            if( is_array($val) ){ continue; }

            $tpl->set( '{'.$_tag_group.':'.$tag.'}', $val );
          }
        }
        $tpl->compile( $skin );
      }


      return false;
    }

    public final function get_related( $post_id, $count = 5 )
    {
        $_var = self::CACHE_VAR_POSTS.'-related-'.$post_id.'-'.$count;
        $_RETURN = cache::get( $_var );
        if( $_RETURN ){ return $_RETURN; }

        $_RETURN = false;

        $SQL = '
            SELECT
                posts.id,
                posts.title,
                posts.created_time,
                array_to_string(posts.tag_id,\',\', \'0\') as agg_tags
            FROM
                posts
            WHERE
                posts.id = '.intval( $post_id ).'
            GROUP by posts.id;'.self::trim(QUERY_CACHABLE);

        $row = $this->db->super_query( $SQL );
        if( is_array($row) && count($row) )
        {
            $row['agg_tags'] = explode( ',', $row['agg_tags'] );
            $row['agg_tags'] = array_map( 'intval', $row['agg_tags'] );
            if( !count($row['agg_tags']) ){ return false; }

            $RELATED = array();

            $row['agg_tags'] = '\''.implode('\'::int8, \'',$row['agg_tags']).'\'::int8';

            foreach( preg_split( '!(\s+)!is', $row['title'] ) as $k => $v )
            {
                if( strlen($v) < 6 ){ continue; }
                $v = 'websearch_to_tsquery(\''.$this->db->safesql($v).'\')';
                $chSQL = '
                    -- '.$row['title'].'
                    SELECT
                        posts.id,
                        rank
                    FROM
                        posts,
                        ts_rank(posts.svector,'.$v.') as rank
                    WHERE
                        rank > 0
                        AND posts.id != '.$row['id'].'
                        AND posts.tag_id && ARRAY['.$row['agg_tags'].']
                    ORDER by rank DESC
                    OFFSET 0 LIMIT 10;
                '.self::trim(QUERY_CACHABLE);
                $chSQL = $this->db->query( $chSQL );
                while( $sr = $this->db->get_row($chSQL) )
                {
                    $RELATED[$sr['id']] = $sr['rank'];
                }
            }

            arsort( $RELATED );
            if( !count($RELATED) ){ return false; }
            if( count( $RELATED ) > 10 ){ $RELATED = array_slice( $RELATED, 0, $count, true ); }

            $tpl = new tpl;

            foreach( $RELATED as $post_id => $post_rank )
            {
                $post_data = $this->get( array( 'post.id' => $post_id ) );
                $post_data = isset($post_data[$post_id])?$post_data[$post_id]:false;

                if( !$post_data || !is_array($post_data) || !isset($post_data['post']['id']) ){ continue; }

                $tpl->load( 'post_related' );

                $tpl->set( '{hash:key}',            self::md5( date('Ymd').self::integer($post_data['post']['id']) ) );
                $tpl->set( '{post:id}',             self::integer($post_data['post']['id']));
                $tpl->set( '{post:url}',            self::get_url( $post_data ) );
                $tpl->set( '{post:author_id}',      self::integer($post_data['post']['author_id']));
                $tpl->set( '{post:short_post}',     self::trim( $post_data['post']['short_post'] ) );
                $tpl->set( '{post:created_time}',   $post_data['post']['created_time'] );

                foreach( $post_data as $key => $value )
                {
                    if( in_array( $key, array('post' , 'categ') ) && is_array($value) )
                    {
                        foreach( $value as $k=>$v )
                        {
                            $tpl->set( '{'.$key.':'.$k.'}',         self::htmlspecialchars( self::html_entity_decode( self::stripslashes($v) ) ) );
                            $tpl->set( '{'.$key.':'.$k.':strip}',   self::htmlspecialchars( self::strip_tags( self::html_entity_decode( self::stripslashes($v) ) ) ) );
                        }
                    }
                }

                $tpl->compile( 'post_related' );
            }

            $_RETURN = $tpl->result( 'post_related' );
        }

        cache::set( $_var, $_RETURN );

        return $_RETURN;
    }

}

?>