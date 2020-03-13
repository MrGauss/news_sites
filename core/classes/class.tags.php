<?php

//////////////////////////////////////////////////////////////////////////////////////////

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

//////////////////////////////////////////////////////////////////////////////////////////

if( !trait_exists( 'basic' ) ){ require( CLASSES_DIR.DS.'trait.basic.php' ); }
if( !trait_exists( 'db_connect' ) ){ require( CLASSES_DIR.DS.'trait.db_connect.php' ); }

//////////////////////////////////////////////////////////////////////////////////////////

class tags
{
    use basic, db_connect;

    const CACHE_VAR_TAGS = 'tags';

    public final static function tag_decode( $tag )
    {
        return self::urldecode( $tag );
    }

    public final function get_url( $tag_name, $category = false )
    {
        $link = HOME.'tag:'.self::urlencode( $tag_name ).'/';
        return $link;
    }

    public final function get_id( $tag )
    {
        foreach( $this->get_tags() as $id => $data )
        {
            if( $data['name'] == $tag )
            {
                return $id;
            }
        }
        return false;
    }

    public final function get_top_tags()
    {
        return $this->get_tags( 'posts_count', 'DESC' );
    }

    static public final function get_top_tag_id()
    {
        $tags = new tags;
        $tags = $tags->get_tags( 'posts_count', 'DESC', 1 );
        $tags = reset( $tags );

        return $tags['id'];
    }

    public final function get_tags( $order = 'name', $order_dest = 'ASC', $limit = 100 )
    {
        $data = cache::get( self::CACHE_VAR_TAGS.'_order_'.$order.$order_dest.$limit );

        if( !$data || !is_array($data) || !count($data) )
        {
            $SQL = '
                SELECT
                    tags.id,
                    tags.name,
                    tags.altname,
                    tags.posts_count as news_count
                FROM
                    tags WHERE id > 0 ORDER BY '.$order.' '.$order_dest.'
                LIMIT '.common::integer($limit).';'.QUERY_CACHABLE;

            $SQL = $this->db->query( $SQL );

            $data = array();
            while( ($row = $this->db->get_row($SQL)) != false )
            {
                $data[$row['id']] = self::stripslashes( $row );
                $data[$row['id']]['name'] = self::htmlspecialchars( $data[$row['id']]['name'] );
                $data[$row['id']]['altname'] = self::totranslit( $data[$row['id']]['altname'] );
                $data[$row['id']]['news_count'] = self::integer( $data[$row['id']]['news_count'] );
            }
            $this->db->free( $SQL );
            cache::set( self::CACHE_VAR_TAGS.'_order_'.$order.$order_dest.$limit, $data );
        }

        return $data;
    }

}

?>