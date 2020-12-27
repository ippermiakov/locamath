<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function dir_walk($callback, $dir, $types = null, $recursive = false, $baseDir = '', &$modFile) {
    if ($dh = opendir($dir)) {
	while (($file = readdir($dh)) !== false) {
	    if ($file === '.' || $file === '..') {
		continue;
	    }
	    if (is_file($dir . $file)) {
		if (is_array($types)) {
		    if (!in_array(strtolower(pathinfo($dir . $file, PATHINFO_EXTENSION)), $types, true)) {
			continue;
		    }
		}
		$callback($baseDir . $file, $modFile);
	    }
	    elseif($recursive && is_dir($dir . $file)) {
		dir_walk($callback, $dir . $file . DIRECTORY_SEPARATOR, $types, $recursive, $baseDir . $file . DIRECTORY_SEPARATOR, $modFile);
	    }
	}
	closedir($dh);
    }
}

function updateFileModificationTime($filename, &$modFile) {
    if(is_file($filename)) {
	$mTime = filemtime($filename);
	if($mTime > $modFile) {
	    $modFile = $mTime;
	}
    }
}