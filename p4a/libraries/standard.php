<?php
/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

/**
 * Stops program execution with an error
 * @param string $error Error identifier
 * @param string $message Error description or other message
 */
function P4A_Error($error, $message = '')
{
	if (strlen($message)) {
		$error .= ": $message";
	}
	die($error);
}

/**
 * Converts a "file" value into an array
 * @param string $file
 * @return array
 */
function P4A_File2array($file)
{
	//name, path, size, type, width, height
	//{image 96.gif,image 96.gif,7170,image/gif,96,70}
	$file = substr($file, 1, -1);
	$aFileTmp = explode(',', $file);
	$aFile['name'] = $aFileTmp[0];
	$aFile['path'] = $aFileTmp[1];
	$aFile['size'] = $aFileTmp[2];
	$aFile['type'] = $aFileTmp[3];
	$aFile['width'] = $aFileTmp[4];
	$aFile['height'] = $aFileTmp[5];
	//$aFile['url'] = rawurlencode($aFileTmp[1]);
	$aFile['url'] = $aFileTmp[1];
	return $aFile;
}

/**
 * Converts an array into a "file" value
 * @param array $aFile
 * @return string
 */
function P4A_Array2file($aFile)
{
	//name, path, size, type, width, height
	//{image 96.gif,image 96.gif,7170,image/gif,96,70}
	$aFileNew[] = $aFile['name'];
	$aFileNew[] = $aFile['path'];
	$aFileNew[] = $aFile['size'];
	$aFileNew[] = $aFile['type'];
	$aFileNew[] = $aFile['width'];
	$aFileNew[] = $aFile['height'];
	$sFile = '{' . join(',' , $aFileNew ) . '}';
	return $sFile;
}

/**
 * Converts a file path into a "file" format value
 * @param string $filename
 * @param string $uploads_dir
 * @return string
 */
function P4A_Filename2File($filename, $uploads_dir)
{
	if (!is_file($filename)) return false;
	
	$aFile['name'] = basename($filename);
	$aFile['path'] = str_replace($uploads_dir,'',$filename);
	$aFile['size'] = filesize($filename);
	$aFile['type'] = mime_content_type($filename);
	list($type,$subtype) = explode('/',$aFile['type']);
	if ($type == 'image') {
		list($aFile['width'],$aFile['height']) = getimagesize($filename);
	} else {
		$aFile['width'] = null;
		$aFile['height'] = null;
	}
	return P4A_Array2File($aFile);
}

/**
 * Takes page number, records number, page limit and returns the necessary  offset for a query
 * @param integer $page_number
 * @param integer $records_number
 * @param integer $page_limit
 * @return integer
 */
function P4A_Get_Offset($page_number, $records_number, $page_limit)
{
	$offset = $page_limit * ($page_number -1);
	if ($offset > $records_number) {
		$offset = $page_limit * (P4A_Get_Num_Pages($records_number, $page_limit) -1);
	}
	return $offset;
}

/**
 * Takes records number, page limit and returns the num of pages
 * @param integer $records_number
 * @param integer $page_limit
 * @return integer
 */
function P4A_Get_Num_Pages($records_number, $page_limit)
{
	if ($records_number % $page_limit == 0) {
		return $records_number / $page_limit ;
	} else {
		return intval(($records_number / $page_limit)) + 1;
	}
}

/**
 * Tests if a file with the same name exists and return the correct file name.
 * Appends _1 (_2, _3) at the end fo the file name.
 * @param string $filename The filename without path
 * @param string $directory The directory (absolute)
 * @return string
 */
function P4A_Get_Unique_File_Name($filename, $directory)
{
	$aParts = explode('.', $filename);
	$base = '' ;
	$ext = '' ;

	if (sizeof($aParts) > 1) {
		$ext = '.' . array_pop( $aParts );
		$base = join( $aParts, '.' );
	} else {
		$base = $filename;
	}

	$i = 1 ;
	while (file_exists("$directory/$filename")) {
		$filename = $base . '_' . $i . $ext;
		$i++;
	}

	return $filename;
}

/**
 * Strips "strange" chars from filename
 * @param string $filename
 * @return string
 */
function P4A_Get_Valid_File_Name($filename)
{
	$filename = str_replace(" ","_",$filename);
	$filename = preg_replace("/[^A-Za-z0-9_\-\.]/","",$filename);
	return $filename;
}

/**
 * @return integer
 */
function P4A_Get_Microtime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Includes all p4a objects for the application
 * @param string $dir
 */
function P4A_Include_Objects($dir)
{
	if (is_dir($dir)) {
		$files = array();
		$dirs  = array();

		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if ($filename != '.' and $filename != '..' and $filename != 'CVS' and $filename != '.svn') {
				$files[] = $filename;
			}
		}
		closedir($dh);

		for ($i=0;$i<count($files);$i++) {
			if(is_dir($dir .'/' . $files[$i])) {
				$dirs[]	= $dir .'/' . $files[$i];
			} elseif (is_file($dir .'/' . $files[$i]) and (substr($files[$i], -4) == '.php')) {
				require_once $dir .'/' .$files[$i];
			}
		}

		foreach ($dirs as $subdir) {
			P4A_Include_Objects($subdir);
		}
	}
}

/**
 * Returns the extension of the passed file path/url
 * @param string $url
 * @return string
 */
function P4A_Get_File_Extension($url)
{
	return substr(strrchr($url, '.'), 1);
}

/**
 * Check if the extension is allowed to be uploaded
 * @param string $extension
 * @return boolean
 */
function P4A_Is_Extension_Allowed($extension)
{
	$allow = explode('|', P4A_DENIED_EXTENSIONS);
	return !in_array(strtolower($extension), $allow);
}

/**
 * Returns an i18n translated string (like gettext)
 * @param string $string string to be translated
 * @return string
 */
function __($string)
{
	return p4a::singleton()->i18n->translate($string);
}

/**
 * @param string $mime_type
 * @return boolean
 */
function P4A_Is_Mime_Type_Embeddable($mime_type)
{
	list($type, $application) = explode('/', $mime_type);
	if ($type == 'audio' or $type == 'video') return true;

	$embeddables = array();
	$embeddables[] = 'application/x-shockwave-flash';
	$embeddables[] = 'application/vnd.rn-realmedia';
	return in_array($mime_type, $embeddables);
}

/**
 * Return HTML tag containig embedded audio/video player
 * @param string $src File path
 * @param string $mime_type
 * @param string $width
 * @param string $height
 * @return string
 */
function P4A_Embedded_Player($src, $mime_type, $width=300, $height=200)
{
	$src = P4A_Strip_Double_Slashes($src);
	if (!$width) $width=300;
	if (!$height) $height=200;

	switch ($mime_type) {
		case 'audio/vnd.rn-realaudio';
		case 'audio/x-pn-realaudio':
		case 'audio/x-pn-realaudio-plugin':
		case 'video/vnd.rn-realvideo':
		case 'application/vnd.rn-realmedia':
			$player_type = 'real';
			break;
		case 'video/quicktime':
			$player_type = 'quicktime';
			break;
		case 'application/x-shockwave-flash':
			$player_type = 'flash';
			break;
		default:
			$player_type = 'wmedia';
	}

	return '<a id="p4a_media_player" href="' . $src . '">' . $src . '</a><script type="text/javascript">$("#p4a_media_player").jmedia({},{type:"' . $player_type . '", width:' . $width . ', height:' . $height . '});</script>';
}

/**
 * Used for internal debugging (within session browser)
 * @param mixed $v Variable to print
 * @return string
 */
function _P4A_Debug_Print_Variable($v)
{
	if ($v === null) $v = '<span style="color:green">NULL</span>';
	if ($v === false) $v = '<span style="color:green">FALSE</span>';
	if ($v === true) $v = '<span style="color:green">TRUE</span>';
	if ($v === '') $v = '<span style="color:green">Empty String</span>';

	if (is_array($v)) {
		if (empty($v)) {
			$v = '<span style="color:green">Empty Array</span>';
		} else {
			$todebug = $v;
			$v = '<table border="1">';
			$v .= "<tr><th colspan='2'>Array</th></tr>";
			$v .= "<tr><th>key</th><th>value</th></tr>";
			foreach ($todebug as $k2=>$v2) {
				$v2 = _P4A_Debug_Print_Variable($v2);
				$v .= "<tr><td>$k2</td><td>$v2</td></tr>";
			}
			$v .= '</table>';
		}
	}

	if (is_object($v)) {
		if (is_a($v, 'p4a_object')) {
			$v = '<a href=".?_p4a_session_browser=' . $v->getId() . '">' . $v->getName() . ' (' . get_class($v) . ')</a>';
		} else {
			$v = '<pre>' . print_r($v, true) . '</pre>';
		}
	}

	return $v;
}

/*
 * @param string $string
 * @return string
 */
function P4A_Strip_Double_Slashes($string)
{
	$string = str_replace('//', '/', $string);
	if (strpos($string, '//') !== false) {
		$string = P4A_Strip_Double_Slashes($string);
	}
	return $string;
}
	
/**
 * Can the event be managed by the browser?
 * eg: P4A_Is_Browser_Event('onclick') will return true
 *
 * @param string $event
 * @return boolean
 */
function P4A_Is_Browser_Event($event)
{
	return in_array(strtolower($event), array(
		'onblur', 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onkeydown', 
		'onkeypress', 'onkeyup', 'onmousedown', 'onmousemove', 'onmouseout', 
		'onmouseover', 'onmouseup', 'onscroll', 'onreturnpress'
	));
}

/**
 * @param string $string
 * @return string
 */
function P4A_Generate_Default_Label($string)
{
	return ucfirst(str_replace('_', ' ', strtolower($string)));
}

/**
 * @param string $string
 * @param string $accesskey
 * @return string
 */
function P4A_Highlight_AccessKey($string, $accesskey)
{
	if (strlen($accesskey) == 0) return $string;
	return preg_replace("/($accesskey)/i", "<span class=\"accesskey\">$1</span>", $string);
}

/**
 * @param string $column1
 * @param string $column2
 * @param string $additional_css_classes
 * @param string $html_attributes
 * @return string
 */
function P4A_Generate_Widget_Layout_Table($column1 = null, $column2 = null, $additional_css_classes = null, $html_attributes = null)
{
	if (!strlen($html_attributes) and !strlen($additional_css_classes)) {
		if (!strlen($column1)) return $column2;
		if (!strlen($column2)) return $column1;
	}
	return "<table class='p4a_widget_layout_table $additional_css_classes' $html_attributes><tr><td class='c1'>$column1</td><td class='c2'>$column2</td></tr></table>";
}

/**
 * @param string $value
 * @return string
 */
function P4A_Quote_SQL_Value($value)
{
	return str_replace("'", "''", $value);
}

/**
 * @param string $dir
 * @return boolean
 */
function P4A_Mkdir_Recursive($dir)
{
	$parts = explode(_DS_, $dir);
	$dir = '';
	foreach ($parts as $part) {
		$dir .= _DS_ . $part;
		if (@is_dir($dir)) continue;
		if (!@mkdir($dir)) return false;
	}
	return true;
}