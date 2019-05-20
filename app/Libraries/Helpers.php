<?php
/**
 * Función para extraer el navegador a partir de un user agent
 */
if(!function_exists('parse_user_agent')){
    function parse_user_agent( $u_agent = null ) {
        if( is_null($u_agent) ) {
            if( isset($_SERVER['HTTP_USER_AGENT']) ) {
                $u_agent = $_SERVER['HTTP_USER_AGENT'];
            } else {
                throw new \InvalidArgumentException('parse_user_agent requires a user agent');
            }
        }
        $platform = null;
        $browser  = null;
        $version  = null;
        $empty = array( 'platform' => $platform, 'browser' => $browser, 'version' => $version );
        if( !$u_agent ) {
            return $empty;
        }
        if( preg_match('/\((.*?)\)/im', $u_agent, $parent_matches) ) {
            preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
                    (?:\ [^;]*)?
                    (?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);
            $priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11' );
            $result['platform'] = array_unique($result['platform']);
            if( count($result['platform']) > 1 ) {
                if( $keys = array_intersect($priority, $result['platform']) ) {
                    $platform = reset($keys);
                } else {
                    $platform = $result['platform'][0];
                }
            } elseif( isset($result['platform'][0]) ) {
                $platform = $result['platform'][0];
            }
        }
        if( $platform == 'linux-gnu' || $platform == 'X11' ) {
            $platform = 'Linux';
        } elseif( $platform == 'CrOS' ) {
            $platform = 'Chrome OS';
        }
        preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
                    TizenBrowser|(?:Headless)?Chrome|YaBrowser|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
                    Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
                    Valve\ Steam\ Tenfoot|
                    NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
                    (?:\)?;?)
                    (?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
            $u_agent, $result, PREG_PATTERN_ORDER);
        // If nothing matched, return null (to avoid undefined index errors)
        if( !isset($result['browser'][0]) || !isset($result['version'][0]) ) {
            if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
                return array( 'platform' => $platform ?: null, 'browser' => $result['browser'], 'version' => isset($result['version']) ? $result['version'] ?: null : null );
            }
            return $empty;
        }
        if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result) ) {
            $rv_result = $rv_result['version'];
        }
        $browser = $result['browser'][0];
        $version = $result['version'][0];
        $lowerBrowser = array_map('strtolower', $result['browser']);
        $find = function ( $search, &$key, &$value = null ) use ( $lowerBrowser ) {
            $search = (array)$search;
            foreach( $search as $val ) {
                $xkey = array_search(strtolower($val), $lowerBrowser);
                if( $xkey !== false ) {
                    $value = $val;
                    $key   = $xkey;
                    return true;
                }
            }
            return false;
        };
        $key = 0;
        $val = '';
        if( $browser == 'Iceweasel' || strtolower($browser) == 'icecat' ) {
            $browser = 'Firefox';
        } elseif( $find('Playstation Vita', $key) ) {
            $platform = 'PlayStation Vita';
            $browser  = 'Browser';
        } elseif( $find(array( 'Kindle Fire', 'Silk' ), $key, $val) ) {
            $browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
            $platform = 'Kindle Fire';
            if( !($version = $result['version'][$key]) || !is_numeric($version[0]) ) {
                $version = $result['version'][array_search('Version', $result['browser'])];
            }
        } elseif( $find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
            $browser = 'NintendoBrowser';
            $version = $result['version'][$key];
        } elseif( $find('Kindle', $key, $platform) ) {
            $browser = $result['browser'][$key];
            $version = $result['version'][$key];
        } elseif( $find('OPR', $key) ) {
            $browser = 'Opera';
            $version = $result['version'][$key];
        } elseif( $find('Opera', $key, $browser) ) {
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif( $find('Puffin', $key, $browser) ) {
            $version = $result['version'][$key];
            if( strlen($version) > 3 ) {
                $part = substr($version, -2);
                if( ctype_upper($part) ) {
                    $version = substr($version, 0, -2);
                    $flags = array( 'IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows' );
                    if( isset($flags[$part]) ) {
                        $platform = $flags[$part];
                    }
                }
            }
        } elseif( $find('YaBrowser', $key, $browser) ) {
            $browser = 'Yandex';
            $version = $result['version'][$key];
        } elseif( $find(array( 'IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome' ), $key, $browser) ) {
            $version = $result['version'][$key];
        } elseif( $rv_result && $find('Trident', $key) ) {
            $browser = 'MSIE';
            $version = $rv_result;
        } elseif( $find('UCBrowser', $key) ) {
            $browser = 'UC Browser';
            $version = $result['version'][$key];
        } elseif( $find('CriOS', $key) ) {
            $browser = 'Chrome';
            $version = $result['version'][$key];
        } elseif( $browser == 'AppleWebKit' ) {
            if( $platform == 'Android' ) {
                // $key = 0;
                $browser = 'Android Browser';
            } elseif( strpos($platform, 'BB') === 0 ) {
                $browser  = 'BlackBerry Browser';
                $platform = 'BlackBerry';
            } elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
                $browser = 'BlackBerry Browser';
            } else {
                $find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
            }
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif( $pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser'])) ) {
            $pKey = reset($pKey);
            $platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $pKey);
            $browser  = 'NetFront';
        }
        return array( 'platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null );
    }
}
/**
 * Extraer la ip del cliente
 */
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
/**
 * Extraer ids de un arreglo
 */
if(!function_exists('extract_ids')){
    function extract_ids($cats){
        $res = array();
        foreach($cats as $k=>$v) {
            $res[]= $v->id;
        }
        return $res;
    }
}
/**
 * Extraer el nombre de una tabla dependiendo del sql
 */
if(!function_exists("get_table_name")){

    function get_table_name($query) {

        $query = trim(str_replace(PHP_EOL, ' ', $query));
        $table = '';
        if(strtolower(substr($query, 0, 12)) == 'create table') {
            $start = stripos($query, 'CREATE TABLE') + 12;
            $end = strpos($query, '(');
            $length = $end - $start;
            $table = substr($query, $start, $length);
        }
        elseif(strtolower(substr($query, 0, 6)) == 'update') {
            $end = stripos($query, 'set');
            $table = substr($query, 6, $end);
            $table = str_replace('"', '', $table);
            if(strpos($table, " ") !== false){
                $table = explode(" ",$table);
                $table = @$table[1];
            }
        }
        elseif(strtolower(substr($query, 0, 11)) == 'alter table') {
            $parts = explode(' ', $query);
            $table = $parts[2];
        }
        elseif(strtolower(substr($query, 0, 11)) == 'insert into') {
            $parts = explode(' ', $query);
            $table = $parts[2];
        }
        elseif(strtolower(substr($query, 0, 12)) == 'create index') {
            $parts = explode(' ', $query);
            $table = $parts[4];
        }
        elseif(strtolower(substr($query, 0, 6)) == 'select') {
            $parts = explode(' ', $query);
            foreach($parts as $i => $part) {
                if(trim(strtolower($part)) == 'from') {
                    $table = $parts[$i + 1];
                    break;
                }
            }
        }
        elseif(strtolower(substr($query, 0, 29)) == 'create unique clustered index') {
            $parts = explode(' ', $query);
            $table = $parts[6];
        }
        elseif(strtolower(substr($query, 0, 22)) == 'create clustered index') {
            $parts = explode(' ', $query);
            $table = $parts[5];
        }
        elseif(strtolower(substr($query, 0, 15)) == 'exec sp_columns') {
            $parts = explode(' ', $query);
            $table = str_replace("'", '', $parts[2]);
        }
        elseif(strtolower(substr($query, 0, 11)) == 'delete from') {
            $parts = explode(' ', $query);
            $table = str_replace("'", '', $parts[2]);
        }
        $table = trim(str_replace(['`', '[', ']'], ['', '', ''], $table));
        return str_replace('"', '', $table);
    }
}

/**
 * Permite remover caracteres especiales de un json (PD: se utiliza al visualizar los logs)
 */
if(!function_exists('convert_from_latin1_to_utf8_json')){
    function convert_from_latin1_to_utf8_json($dat) { 
        if (is_string($dat)) { 
        return html_entity_decode(htmlentities($dat)); 
        } elseif (is_array($dat)) { 
        $ret = []; 
        foreach ($dat as $i => $d) $ret[ $i ] = convert_from_latin1_to_utf8_json($d); 

        return $ret; 
        } elseif (is_object($dat)) { 
        foreach ($dat as $i => $d) $dat->$i = convert_from_latin1_to_utf8_json($d); 

        return $dat; 
        } else { 
        return $dat; 
        } 
    } 
}