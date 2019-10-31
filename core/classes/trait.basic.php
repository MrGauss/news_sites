<?php

	//////////////////////////////////////////////////////////////////////////////////////////

	if (!defined('GAUSS_CMS'))
	{
		echo basename(__FILE__);
		exit;
	}

	//////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Trait basic
	 */
	trait basic
	{
		/**
		 * @param $name
		 * @param $arguments
		 */
		public final function __call($name, $arguments)
		{
			echo self::err('Method "' . $name . '" don\'t exist! ' . "\n");
			exit;
		}

		/**
		 * @param $name
		 * @param $arguments
		 */
		public static function __callStatic($name, $arguments)
		{
			echo self::err('Âûçîâ ñòàòè÷åñêîãî ìåòîäà ' . $name . ' ' . implode(', ', $arguments) . "\n");
			exit;
		}

		/**
		 * @param $text
		 */
		static public final function err($text)
		{
			trigger_error(self::htmlentities($text), E_USER_ERROR);
			exit;
		}

		/**
		 * @param $data
		 * @return array|mixed|null|string|string[]
		 */
		static public final function fileExt($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}

			$data = explode('.', $data);
			$data = end($data);
			$data = self::strtolower($data);
			$data = self::filter($data);

			$data = preg_replace('!\W*!', '', $data);

			return $data;
		}

		/**
		 * @param $data
		 * @return array|float|int
		 */
		static public final function iniBytes2normalBytes($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::iniBytes2normalBytes', $data);
			}

			if (strpos($data, 'G') !== false)
			{
				$data = intval(str_replace('G', '', $data)) * 1024 * 1024 * 1024;
			}
			if (strpos($data, 'M') !== false)
			{
				$data = intval(str_replace('M', '', $data)) * 1024 * 1024;
			}
			if (strpos($data, 'K') !== false)
			{
				$data = intval(str_replace('K', '', $data)) * 1024;
			}
			return $data;
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function filter($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::filter', $data);
			}
			return self::trim(filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_BACKTICK | FILTER_FLAG_ENCODE_AMP));
		}

		/**
		 * @param $data
		 * @return array|string
		 */
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

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function md5($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}
			return md5($data . CMS_KEY);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function md5_file($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}
			if (!file_exists($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' -> file not found!');
			}
			return md5_file($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function sha1($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}
			return sha1($data . CMS_KEY);
		}

		/**
		 * @param $data
		 * @return array|float|int
		 */
		static public final function integer($data)
		{
			if (!is_numeric($data) && !is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::integer', $data);
			}
			return abs(intval($data));
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function strip_tags($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::strip_tags', $data);
			}
			return strip_tags($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function trim($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::trim', $data);
			}
			return trim($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function stripslashes($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::stripslashes', $data);
			}
			return stripslashes($data);
		}

		/**
		 * @param $data
		 * @return int
		 */
		static public final function strlen($data)
		{
			if (!is_scalar($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string only!');
			}
			return mb_strlen($data, CHARSET);;
		}

		/**
		 * @param $data
		 * @return array|string
		 */
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


		static public final function r_n_2_space( $data )
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::'.__METHOD__, $data);
			}
			return preg_replace( '!([\r\n]+)!is', ' ', $data );
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function htmlspecialchars($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::htmlspecialchars', $data);
			}
			return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5 | ENT_COMPAT, CHARSET, true);
		}

		/**
		 * @param string $data
		 * @return array|string
		 */
		static public final function htmlentities($data = '')
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::htmlentities', $data);
			}
			return htmlentities($data, ENT_QUOTES | ENT_HTML5, CHARSET, true);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function strtoupper($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::strtoupper', $data);
			}
			return mb_strtoupper($data, CHARSET);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function strtolower($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::strtolower', $data);
			}
			return mb_strtolower($data, CHARSET);
		}

		/**
		 * @param $data
		 * @return array|false|int
		 */
		static public final function strtotime($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}
			return strtotime($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function urlencode($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::urlencode', $data);
			}
			return urlencode($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function fileinfo($data, $param = FILEINFO_MIME_TYPE)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::' . __METHOD__, $data);
			}
			$finfo = new finfo($param );
			$imtype = $finfo->file($data);
			unset($finfo);
			return $imtype;
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function urldecode($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::urldecode', $data);
			}
			return urldecode($data);
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function utf2win($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::utf2win', $data);
			}
			return mb_convert_encoding($data, 'cp1251', 'utf-8');
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static public final function win2utf($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::win2utf', $data);
			}
			return mb_convert_encoding($data, 'utf-8', 'cp1251');
		}

		/**
		 * @param $data
		 * @return array|string
		 */
		static final public function encode_string($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::encode_string', $data);
			}
			return self::urlencode(base64_encode(strrev(base64_encode($data))));
		}

		/**
		 * @param $data
		 * @return array|bool|string
		 */
		static final public function decode_string($data)
		{
			if (!is_scalar($data) && !is_array($data))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($data))
			{
				return array_map('self::decode_string', $data);
			}
			return base64_decode(strrev(base64_decode(self::urldecode($data))));;
		}

		/**
		 * @param        $date
		 * @param string $format
		 * @return array|false|int|string
		 */
		static public final function en_date($date, $format = 'd.m.Y H:i:s')
		{
			if (!is_scalar($date))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string only!');
			}
			$date = self::strtotime($date);
			$date = intval($date);
			$date = date($format, $date);
			return $date;
		}

		/**
		 * @param $str
		 * @return array|string
		 */
		static public final function db2html($str)
		{
			if (!is_scalar($str) && !is_array($str))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($str))
			{
				return array_map('self::db2html', $str);
			}

			$str = self::stripslashes($str);
			$str = self::htmlentities($str);

			return $str;
		}

		/**
		 * @param $str
		 * @return array|mixed|string
		 */
		static public final function totranslit($str)
		{
			if (!is_scalar($str) && !is_array($str))
			{
				self::err('' . __CLASS__ . '::' . __METHOD__ . ' accepts string or array only!');
			}
			if (is_array($str))
			{
				return array_map('self::totranslit', $str);
			}



			$str = self::strtolower($str);
			$rp = array('àáâãäå¸çèéêëìíîïðñòóôõöüûý³ ', 'abvgdeezijklmnoprstufõc_yei_');


			for( $i = 0; $i < mb_strlen($rp[0],CHARSET); $i++ )
			{
			    $from = mb_substr( $rp[0], $i, 1, CHARSET );
			    $to   = mb_substr( $rp[1], $i, 1, CHARSET );
                $str  = str_replace($from, $to, $str);
			}

			$str = str_replace('æ', 'zh', $str);
			$str = str_replace('÷', 'ch', $str);
			$str = str_replace('ø', 'sh', $str);
			$str = str_replace('ù', 'shh', $str);
			$str = str_replace('ú', '\'', $str);
			$str = str_replace('þ', 'yu', $str);
			$str = str_replace('ÿ', 'ya', $str);
			$str = str_replace('º', 'ye', $str);
			$str = str_replace('Æ', 'ZH', $str);
			$str = str_replace('×', 'CH', $str);
			$str = str_replace('Ø', 'SH', $str);
			$str = str_replace('Ù', 'SHH', $str);
			$str = str_replace('Ú', '`', $str);
			$str = str_replace('Þ', 'YU', $str);
			$str = str_replace('ß', 'YA', $str);
			$str = str_replace('ª', 'YE', $str);

			$str = self::strtolower($str);

			$str = self::trim(self::strip_tags($str));
			$str = preg_replace('![^a-z0-9\_\-\.]+!mi', '', $str);
			$str = preg_replace('![.]+!i', '.', $str);
			$str = self::strtolower($str);

			return $str;
		}

		/**
		 * @param $filename
		 * @return bool|string
		 */
		static protected final function read_file($filename)
		{
			if (!file_exists($filename))
			{
				return false;
			}
			if (!filesize($filename))
			{
				return false;
			}

			$fop = fopen($filename, 'rb');
			$data = fread($fop, filesize($filename));
			fclose($fop);
			return $data;
		}

		/**
		 * @param      $filename
		 * @param bool $data
		 * @param bool $log
		 * @return bool
		 */
		static public final function write_file($filename, $data = false, $log = false)
		{
			if (!file_exists($filename))
			{
				fclose(fopen($filename, 'a'));
			}

			if ($log == true)
			{
				$fop = fopen($filename, 'a');
			}
			else
			{
				$fop = fopen($filename, 'w');
			}

			if (flock($fop, LOCK_EX))
			{
				fwrite($fop, $data);
				fflush($fop);
				flock($fop, LOCK_UN);
			}

			fclose($fop);

			return true;
		}

        static public final function integer2size( $int = 0 )
        {
            $suff = 'b';
            if( $int > 1000 ){ $int = $int / 1024; $suff = 'kb'; }
            if( $int > 1000 ){ $int = $int / 1024; $suff = 'Mb'; }
            if( $int > 1000 ){ $int = $int / 1024; $suff = 'Gb'; }
            if( $int > 1000 ){ $int = $int / 1024; $suff = 'Tb'; }
            return round( $int, 1 ).' '.$suff;
        }

        static public final function check_folder( $folder = false )
        {
            if( !$folder ){ return false; }
            if( is_dir( $folder ) ){ return true; }
            return mkdir( $folder );
        }

        // Function written and tested December, 2018
        static public final function get_browser_name($user_agent)
        {
                // Make case insensitive.
                $t = strtolower($user_agent);

                // If the string *starts* with the string, strpos returns 0 (i.e., FALSE). Do a ghetto hack and start with a space.
                // "[strpos()] may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE."
                //        http://php.net/manual/en/function.strpos.php
                $t = " " . $t;

                // Humans / Regular Users
                if     (strpos($t, 'opera'     ) || strpos($t, 'opr/')     ) return 'Opera'            ;
                elseif (strpos($t, 'edge'      )                           ) return 'Edge'             ;
                elseif (strpos($t, 'chrome'    )                           ) return 'Chrome'           ;
                elseif (strpos($t, 'safari'    )                           ) return 'Safari'           ;
                elseif (strpos($t, 'firefox'   )                           ) return 'Firefox'          ;
                elseif (strpos($t, 'msie'      ) || strpos($t, 'trident/7')) return 'Internet Explorer';

                // Search Engines
                elseif (strpos($t, 'google'    )                           ) return '[Bot] Googlebot'   ;
                elseif (strpos($t, 'bing'      )                           ) return '[Bot] Bingbot'     ;
                elseif (strpos($t, 'slurp'     )                           ) return '[Bot] Yahoo! Slurp';
                elseif (strpos($t, 'duckduckgo')                           ) return '[Bot] DuckDuckBot' ;
                elseif (strpos($t, 'baidu'     )                           ) return '[Bot] Baidu'       ;
                elseif (strpos($t, 'yandex'    )                           ) return '[Bot] Yandex'      ;
                elseif (strpos($t, 'sogou'     )                           ) return '[Bot] Sogou'       ;
                elseif (strpos($t, 'exabot'    )                           ) return '[Bot] Exabot'      ;
                elseif (strpos($t, 'msn'       )                           ) return '[Bot] MSN'         ;

                // Common Tools and Bots
                elseif (strpos($t, 'mj12bot'   )                           ) return '[Bot] Majestic'     ;
                elseif (strpos($t, 'ahrefs'    )                           ) return '[Bot] Ahrefs'       ;
                elseif (strpos($t, 'semrush'   )                           ) return '[Bot] SEMRush'      ;
                elseif (strpos($t, 'rogerbot'  ) || strpos($t, 'dotbot')   ) return '[Bot] Moz or OpenSiteExplorer';
                elseif (strpos($t, 'frog'      ) || strpos($t, 'screaming')) return '[Bot] Screaming Frog';
                elseif (strpos($t, 'blex'      )                           ) return '[Bot] BLEXBot'       ;

                // Miscellaneous
                elseif (strpos($t, 'facebook'  )                           ) return '[Bot] Facebook'     ;
                elseif (strpos($t, 'pinterest' )                           ) return '[Bot] Pinterest'    ;

                // Check for strings commonly used in bot user agents
                elseif (strpos($t, 'crawler' ) || strpos($t, 'api'    ) ||
                        strpos($t, 'spider'  ) || strpos($t, 'http'   ) ||
                        strpos($t, 'bot'     ) || strpos($t, 'archive') ||
                        strpos($t, 'info'    ) || strpos($t, 'data'   )    ) return '[Bot] Other'   ;

                return 'Other (Unknown)';
        }

        static public final function telegram_make_mesage( $message )
        {
            $_config    = config::get();
/*
$tphoto = $tphoto?'<a href="'.$tphoto.'">&#8205;</a>':'';

$telegram =
    $tphoto
    .'<b>'.$params['title'].'</b>'
    ."\n\n".$params['message']
    ."\n\n".'<a href="'.$params['link'].'">×åðêàñüêèé ÍÄÅÍÊÖ ÌÂÑ Óêðà¿íè</a>';
*/
        }

        static public final function telegram_send( $message )
        {
            $_config    = config::get();

            if( !isset($_config['telegram_token'])   || !$_config['telegram_token'] )  { return false; }
            if( !isset($_config['telegram_chat_id']) || !$_config['telegram_chat_id'] ){ return false; }

            if( !$message || strlen($message) < 5 ){ return false; }
            $api = 'https://api.telegram.org/bot' . $_config['telegram_token'];

            $message = mb_convert_encoding($message, 'utf-8', 'cp1251');
            $message = urlencode( $message );

            $URL = $api . '/sendMessage?'
                            .'chat_id=' . $_config['telegram_chat_id']
                            .'&text=' . $message
                            .'&parse_mode=html'
                            .'&disable_web_page_preview=0'
                            .'&disable_notification=0';

            /*$h = 0;
            $c = 0;
            $t = 5;
            $id = curl_init( $URL );
            curl_setopt($id, CURLOPT_HEADER, $h);
            curl_setopt($id, CURLOPT_NOBODY, $c);
            curl_setopt($id, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($id, CURLOPT_TIMEOUT, $t);
            curl_setopt($id, CURLOPT_REFERER, $URL);
            curl_setopt($id, CURLOPT_USERAGENT, 'TELEGRAM BOT');
            $page = curl_exec($id);
            curl_close($id);  */
            return true;
        }

        static public final function telegram_send_photo( $image, $caption = false )
        {
            $_config    = config::get();

            if( !isset($_config['telegram_token'])   || !$_config['telegram_token'] )  { return false; }
            if( !isset($_config['telegram_chat_id']) || !$_config['telegram_chat_id'] ){ return false; }

            $caption = mb_convert_encoding($caption, 'utf-8', 'cp1251');

            $api = 'https://api.telegram.org/bot' . $_config['telegram_token'];
            $url = $api . '/sendPhoto?chat_id='.$_config['telegram_chat_id'].'&disable_notification=1&caption=' . $caption;

            $post_fields =
            array(
                'photo'     => new CURLFile($image)
            );

            $h = 0;
            $c = 0;
            $t = 5;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, $h);
            curl_setopt($ch, CURLOPT_NOBODY, $c);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $t);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'TELEGRAM BOT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            $output = curl_exec($ch);

            return $output;
        }

	}

?>