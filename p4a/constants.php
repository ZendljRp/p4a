<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

// Server Operating System
if (!defined('P4A_OS')) {
	if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
		define('P4A_OS', 'windows');
	} else {
		define('P4A_OS', 'linux');
	}
}

// Directory Separator
if (!defined('_DS_')) {
	define('_DS_', DIRECTORY_SEPARATOR);
}

// System String Separator
if (!defined('_SSS_')) {
	define('_SSS_', PATH_SEPARATOR);
}

if (!defined('P4A_PASSWORD_OBFUSCATOR')) {
	define('P4A_PASSWORD_OBFUSCATOR', '**********');
}


//Server Constants
if (!defined('P4A_SERVER_NAME')) {
	define('P4A_SERVER_NAME', $_SERVER['SERVER_NAME']);
}

if (!defined('P4A_SERVER_URL')) {
	define('P4A_SERVER_URL', 'http://' . $_SERVER['SERVER_NAME']);
}

if (!defined('P4A_SERVER_DIR')) {
	define('P4A_SERVER_DIR', realpath($_SERVER['DOCUMENT_ROOT']));
}

//P4A Root Constants
if (!defined('P4A_ROOT_DIR')) {
 	 define('P4A_ROOT_DIR', dirname(dirname(realpath(__FILE__))));
}

if (!defined('P4A_ROOT_PATH')) {
	if (strpos(P4A_ROOT_DIR, P4A_SERVER_DIR) === false) {
		define('P4A_ROOT_PATH', '/p4a');
	} else {
		define('P4A_ROOT_PATH', str_replace('\\', '/', str_replace(P4A_SERVER_DIR, '', P4A_ROOT_DIR)));
	}
}

if (!defined('P4A_ROOT_URL')) {
	define('P4A_ROOT_URL', P4A_SERVER_URL . P4A_ROOT_PATH);
}

//P4A Plugins Constants
if (!defined('P4A_LIBRARIES_PATH')) {
	define('P4A_LIBRARIES_PATH', P4A_ROOT_PATH . '/libraries');
}

if (!defined('P4A_LIBRARIES_DIR')) {
 	 define('P4A_LIBRARIES_DIR', P4A_ROOT_DIR . '/libraries');
}

if (!defined('P4A_LIBRARIES_URL')) {
	define('P4A_LIBRARIES_URL', P4A_SERVER_URL . P4A_LIBRARIES_PATH);
}

//Applications Constants
if (!defined('P4A_APPLICATION_PATH')) {
	$tmp_dir = dirname($_SERVER["SCRIPT_NAME"]);
	if ($tmp_dir == '/') {
		$tmp_dir = '';
	}

	define("P4A_APPLICATION_PATH", $tmp_dir);
}

if (!defined('P4A_APPLICATION_DIR')) {
	if (P4A_OS == "windows") {
		define('P4A_APPLICATION_DIR', P4A_SERVER_DIR . str_replace('/', '\\', P4A_APPLICATION_PATH));
	} else {
		define('P4A_APPLICATION_DIR', P4A_SERVER_DIR . P4A_APPLICATION_PATH);
	}
}

if (!defined('P4A_APPLICATION_URL')) {
	define('P4A_APPLICATION_URL', P4A_SERVER_URL . P4A_APPLICATION_PATH);
}

if (!defined('P4A_APPLICATION_NAME')) {
	define('P4A_APPLICATION_NAME', str_replace(_DS_,'_',P4A_APPLICATION_PATH));
}

if (!defined('P4A_APPLICATION_SOURCE_DOWNLOAD_URL')) {
	define('P4A_APPLICATION_SOURCE_DOWNLOAD_URL', '.?_p4a_application_download_missing_link');
}

//Applications Libraries Constants
if (!defined('P4A_APPLICATION_LIBRARIES_PATH')) {
	define('P4A_APPLICATION_LIBRARIES_PATH', P4A_APPLICATION_PATH . '/libraries/');
}

if (!defined('P4A_APPLICATION_LIBRARIES_DIR')) {
	define('P4A_APPLICATION_LIBRARIES_DIR', P4A_SERVER_DIR . P4A_APPLICATION_LIBRARIES_PATH);
}

if (!defined('P4A_APPLICATION_LIBRARIES_URL')) {
	define('P4A_APPLICATION_LIBRARIES_URL', P4A_SERVER_URL . P4A_APPLICATION_LIBRARIES_PATH);
}

//Uploads Constants
if (!defined('P4A_UPLOADS_PATH')) {
	define('P4A_UPLOADS_PATH', P4A_APPLICATION_PATH . '/uploads');
}

if (!defined('P4A_UPLOADS_DIR')) {
	define('P4A_UPLOADS_DIR', P4A_SERVER_DIR . P4A_UPLOADS_PATH);
}

if (!defined('P4A_UPLOADS_URL')) {
	define('P4A_UPLOADS_URL', P4A_UPLOADS_PATH);
}

//Temporary Uploads Constants
define('P4A_UPLOADS_TMP_NAME', 'tmp');
define('P4A_UPLOADS_TMP_PATH', P4A_UPLOADS_PATH . '/' . P4A_UPLOADS_TMP_NAME);
define('P4A_UPLOADS_TMP_DIR', P4A_SERVER_DIR . P4A_UPLOADS_TMP_PATH);
define('P4A_UPLOADS_TMP_URL', P4A_SERVER_URL . P4A_UPLOADS_TMP_PATH);

//Current Theme Configuration
if (!defined('P4A_THEME_NAME')) {
	define('P4A_THEME_NAME', 'default');
}

if (!defined('P4A_THEME_PATH')) {
	define('P4A_THEME_PATH', P4A_ROOT_PATH . '/themes/' . P4A_THEME_NAME);
}

if (!defined('P4A_THEME_DIR')) {
	define('P4A_THEME_DIR', P4A_ROOT_DIR . _DS_ . 'themes' . _DS_ . P4A_THEME_NAME);
}

//Image configuration
if (!defined('P4A_TABLE_THUMB_HEIGHT')) {
	define('P4A_TABLE_THUMB_HEIGHT', 40);
}

//Icons configuration
if (!defined('P4A_ICONS_NAME')) {
	define('P4A_ICONS_NAME', 'default');
}

if (!defined('P4A_ICONS_PATH')) {
	define('P4A_ICONS_PATH', P4A_ROOT_PATH . '/icons/' . P4A_ICONS_NAME );
}

if (!defined('P4A_ICONS_DIR')) {
	define('P4A_ICONS_DIR', P4A_ROOT_DIR . _DS_ . 'icons' . _DS_ . P4A_ICONS_NAME);
}

if (!defined('P4A_ICONS_URL')) {
	define('P4A_ICONS_URL', P4A_ROOT_URL . P4A_ICONS_PATH);
}

if (!defined('P4A_ICONS_EXTENSION')) {
	define('P4A_ICONS_EXTENSION', 'png');
}

//I18N
if (!defined('P4A_LOCALE')) {
	define('P4A_LOCALE', 'en_US');
}

if (!defined('P4A_APPLICATION_LOCALES_PATH')) {
	define('P4A_APPLICATION_LOCALES_PATH', P4A_APPLICATION_PATH . '/i18n');
}

if (!defined('P4A_APPLICATION_LOCALES_DIR')) {
	define('P4A_APPLICATION_LOCALES_DIR', P4A_APPLICATION_DIR . '/i18n');
}

if (!defined('P4A_APPLICATION_LOCALES_URL')) {
	define('P4A_APPLICATION_LOCALES_URL', P4A_APPLICATION_URL . '/i18n');
}

//Force handheld rendering
if (!defined('P4A_FORCE_HANDHELD_RENDERING')) {
	define('P4A_FORCE_HANDHELD_RENDERING', false);
}

//P4A SYSTEM CONSTANTS
if (!defined('P4A_ENABLE_AUTO_INCLUSION')) {
	define('P4A_ENABLE_AUTO_INCLUSION', true);
}

if (!defined('P4A_ENABLE_RENDERING')) {
	define('P4A_ENABLE_RENDERING', true);
}

if (!defined('P4A_FIELD_CLASS')) {
	define('P4A_FIELD_CLASS', 'P4A_Field');
}

if (!defined('P4A_EXTENDED_ERRORS')) {
	define('P4A_EXTENDED_ERRORS', false);
}

if (!defined('P4A_DENIED_EXTENSIONS')) {
	define('P4A_DENIED_EXTENSIONS', 'php|php3|php5|phtml|asp|aspx|ascx|jsp|cfm|cfc|pl|bat|exe|dll|reg|cgi');
}

if (!defined('P4A_AUTO_DB_PRIMARY_KEYS')) {
	define('P4A_AUTO_DB_PRIMARY_KEYS', true);
}

if (!defined('P4A_AUTO_DB_SEQUENCES')) {
	define('P4A_AUTO_DB_SEQUENCES', true);
}

if (!defined('P4A_AJAX_ENABLED')) {
	define('P4A_AJAX_ENABLED', true);
}

if (!defined('P4A_AJAX_DEBUG')) {
	define('P4A_AJAX_DEBUG', false);
}

define('P4A_VERSION', '2.99.6');
define('P4A_ORDER_ASCENDING', 'ASC');
define('P4A_ORDER_DESCENDING', 'DESC');
define('P4A_NULL', 'P4A_NULL');
define('PROCEED', 'P4A_PROCEED');
define('ABORT', 'P4A_ABORT');
define('P4A_DATE', '%Y-%m-%d');
define('P4A_TIME', '%H:%M:%S');
define('P4A_DATETIME', '%Y-%m-%d %H:%M:%S');
define('P4A_DEFAULT_ERROR_REPORTING', E_ALL ^ E_NOTICE);
define('P4A_EXTENDED_ERROR_REPORTING', E_ALL);
define('P4A_DEFAULT_MINIMAL_REPORTING', P4A_DEFAULT_ERROR_REPORTING ^ E_WARNING);
define('P4A_FILESYSTEM_ERROR', 1);

if (!defined('P4A_GD') and function_exists('ImageJPEG') and
	function_exists('ImagePNG') and function_exists('ImageGIF')) {
	define('P4A_GD', true);
} else {
	define('P4A_GD', false);
}