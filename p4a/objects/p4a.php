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
	 * THE APPLICATION.
	 * Stands for the currently running istance of the application.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A extends P4A_Object
	{
		/**
		 * All P4A objects are stored here.
		 * @var array
		 * @access public
		 */
		var $objects = array();

		/**
		 * The currently active object.
		 * This means that here is the pointer to
		 * the last object that has triggered an event/action.
		 * @var object object
		 * @access public
		 */
		var $active_object = NULL;

		/**
		 * Pointer to the currently active mask.
		 * @var object object
		 * @access public
		 */
		var $active_mask = NULL;

		/**
		 * History of opened masks
		 * @var object object
		 * @access public
		 */
		var $masks_history = array();

		/**
		 * Opened masks are stored here.
		 * @var object object
		 * @access public
		 */
		var $masks = null;

		/**
		 * I18n objects and methods.
		 * @var i18n
		 * @access public
		 * @see P4A_I18N
		 */
		var $i18n = array();

       	/**
		 * Application's title.
		 * @var string
		 * @access private
		 */
		var $title = NULL;

       	/**
		 * Loaded libraries registry.
		 * A library is a file in P4A_APPLICATION_LIBRARIES_DIR wich is included every time.
		 * @var array
		 * @access private
		 */
		var $libraries = array();

		/**
		 * Timers container.
		 * @var array
		 * @access private
		 */
		var $timer = array();

		/**
		 * CSS container.
		 * @var array
		 * @access private
		 */
		var $_css = array();

		/**
		 * javascript container.
		 * @var array
		 * @access private
		 */
		var $_javascript = array();

		/**
		 * Is the browser a handheld?
		 * @var boolean
		 * @access private
		 */
		var $handheld = false;

		/**
		 * Is the browser Internet Explorer?
		 * @var boolean
		 * @access private
		 */
		var $internet_explorer = false;

		/**
		 * Find wich browser is the user using
		 * @var array
		 * @access public
		 */
		var $browser_identification = array();

		/**
		 * Counter to avoid browser's back/forward
		 * @var integer
		 * @access private
		 */
		var $_action_history_id = 0;

		var $_to_redesign = array();
		var $_redesign_popup = FALSE;
		var $_ajax_enabled = P4A_AJAX_ENABLED;

		var $_popup = NULL;

		/**
		 * Class constructor.
		 * @access private
		 */
		function p4a()
		{
			//do not call parent constructor
			$_SESSION["p4a"] =& $this;
			$this->i18n =& new p4a_i18n(P4A_LOCALE);
			$this->i18n->setSystemLocale();

			$this->build("P4A_Collection", "masks");
			$this->build("P4A_Collection", "listeners");

			$this->browser_identification = $this->detectClient();

			$this->addJavascript(P4A_THEME_PATH . "/scriptaculous/lib/prototype.js");
			$this->addJavascript(P4A_THEME_PATH . "/p4a.js");
			$this->addJavascript(P4A_THEME_PATH . "/popup.js");
			if (!$this->isHandheld()) {
				$this->addJavascript(P4A_THEME_PATH . "/widgets/date_calendar/calendar_stripped.js");

				$calendar_language = P4A_I18N_DATE_CALENDAR_LANGUAGE;
				if (@file_exists(P4A_THEME_DIR . "/widgets/date_calendar/lang/calendar-{$calendar_language}.js")) {
					$this->addJavascript(P4A_THEME_PATH . "/widgets/date_calendar/lang/calendar-{$calendar_language}.js");
				} else {
					$this->addJavascript(P4A_THEME_PATH . "/widgets/date_calendar/lang/calendar-en.js");
				}

				$this->addJavascript(P4A_THEME_PATH . "/widgets/rich_textarea/fckeditor.js");
				$this->addJavascript(P4A_THEME_PATH . "/widgets/date_calendar/p4a.js");
				$this->addJavascript(P4A_THEME_PATH . "/scriptaculous/src/scriptaculous.js");
				$this->addCss(P4A_THEME_PATH . "/widgets/date_calendar/calendar.css", "screen");
			}

			$this->addCss(P4A_THEME_PATH . "/screen.css", "all");
			$this->addCss(P4A_THEME_PATH . "/screen.css", "print");
			$this->addCss(P4A_THEME_PATH . "/print.css", "print");
			$this->addCss(P4A_THEME_PATH . "/handheld.css", "handheld");

			if ($this->isInternetExplorer()) {
				$this->addCss(P4A_THEME_PATH . "/iehacks.css");
			}

			if ($this->isHandheld()) {
				$this->css = array();
				$this->addCss(P4A_THEME_PATH . "/handheld.css");
			}

			if ($this->isInternetExplorer() and !$this->browser_identification['ie7up'] and !$this->isHandheld()) {
				$this->addJavascript(P4A_THEME_PATH . "/iefixes.js");
			}
		}

		function detectClient()
		{
			require_once 'Net/UserAgent/Detect.php';
			Net_UserAgent_Detect::detect();

			$this->internet_explorer = Net_UserAgent_Detect::isIE();
			$this->_ajax_enabled = (Net_UserAgent_Detect::hasFeature('ajax') and P4A_AJAX_ENABLED);

			if (!Net_UserAgent_Detect::hasFeature('ajax') or P4A_FORCE_HANDHELD_RENDERING) {
				$this->handheld = true;
			}

			return Net_UserAgent_Detect::_getStaticProperty('browser');
		}

		function isInternetExplorer()
		{
			return $this->internet_explorer;
		}

		function isHandheld()
		{
			if (P4A_FORCE_HANDHELD_RENDERING) {
				return true;
			}

			return $this->handheld;
		}

		function isAjaxEnabled()
		{
			return $this->_ajax_enabled;
		}

		function &singleton($class_name = "p4a")
		{
			if (!isset($_SESSION["p4a"])) {
				$a =& new $class_name();
				return $a;
			} else {
				return $_SESSION["p4a"];
			}
		}

		/**
		 * Destroys P4A data.
		 * @access public
		 */
		function close()
		{
			$id = session_id();
			session_destroy();
			session_id(substr($id, 0, -3));
			session_start();
			session_destroy();
		}

		/**
		 * Calls close() and then restart the application.
		 */
		function restart()
		{
			$this->close();
			header('Location: ' . P4A_APPLICATION_PATH );
		}

		/**
		 * Inits the timer.
		 * @access public
		 */
		function initTimer()
		{
			$this->timer = array();
			$this->timer[0]['description'] = 'START';
			$this->timer[0]['value'] = P4A_Get_Microtime();
			$this->timer[0]['diff'] = 0;
		}

		/**
		 * Takes a time snapshot with a given description.
		 * @access public
		 * @param string		The description
		 */
		function timer($description = 'TIMER')
		{
			$num_record = count($this->timer);
			$this->timer[$num_record]['description'] = $description;
			$this->timer[$num_record]['value'] = P4A_Get_Microtime();
			$this->timer[$num_record]['diff'] = $this->timer[$num_record - 1]['diff'] + (P4A_Get_Microtime() - $this->timer[$num_record - 1]['value']);
		}

		/**
		 * Prints out all timer values.
		 * @access public
		 */
		function dumpTimer()
		{
			foreach($this->timer as $time){
				print $time['diff'] .':' . $time['description'] . "\n";
			}
		}

		/**
		 * Executes the main cicle.
		 * @access public
		 */
		function main()
		{
			$this->i18n->setSystemLocale();
			$this->actionHandler('main');

			// Processing get and post.
			if (array_key_exists('_object', $_REQUEST) and
				array_key_exists('_action', $_REQUEST) and
				array_key_exists('_action_id', $_REQUEST) and
				$_REQUEST['_object'] and
				$_REQUEST['_action'] and
				$_REQUEST['_action_id'] and
				$_REQUEST['_action_id'] == $this->getActionHistoryId() and
				isset($this->objects[$_REQUEST['_object']]))
			{
				$object = $_REQUEST['_object'];
				$action = $_REQUEST['_action'];

				$aParams = array();
				// Removing files from request...
				// workaround for windows servers
				foreach ($_FILES as $key=>$value) {
					unset($_REQUEST[$key]);
				}

				foreach ($_REQUEST as $key=>$value) {
					if (substr($key, 0, 3) == 'fld') {
						if (gettype($value) == 'string') {
							$this->objects[$key]->setNewValue(stripslashes($value));
						} else {
							$this->objects[$key]->setNewValue($value);
						}
					} elseif (substr($key, 0, 5) == 'param' and strlen($value) > 0) {
						$aParams[] = $value;
					}
				}

				foreach ($_FILES as $key=>$value) {
					$extension = P4A_Get_File_Extension($value['name']);
					if (P4A_Is_Extension_Allowed($extension)) {
						$value['name'] = str_replace( ',', ';', $value['name'] );
						$value['name'] = P4A_Get_Unique_File_Name($value['name'], P4A_UPLOADS_TMP_DIR);
						move_uploaded_file($value['tmp_name'], P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
						$value['tmp_name'] = '/' . P4A_UPLOADS_TMP_NAME . '/' . $value['name'];

						if ((substr($key, 0, 3) == 'fld') and ($value['error'] == 0)) {
							$new_value = $value['name'] . ',' . $value['tmp_name'] . ',' . $value['size'] . ',' . $value['type'] . ',' ;

							if (substr($value['type'], 0, 5) == 'image') {
								$image_data = getimagesize(P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
								$new_value .= $image_data[0] . ',' . $image_data[1];
							} elseif ($value['type'] == 'application/x-shockwave-flash') {
								$file = P4A_UPLOADS_TMP_DIR . '/' . $value['name'];
								$swf = new File_SWF($file);
								if ($swf->is_valid()) {
									$swf_data = $swf->getMovieSize();
									$new_value .= $swf_data['width'] . ',' . $swf_data['height'];
								}
							} else {
								$new_value .= ',';
							}

							$this->objects[$key]->setNewValue('{' . $new_value . '}');
							if ($this->objects[$key]->actionHandler('afterUpload') == ABORT) return ABORT;
						}
					} else {
						$e = new P4A_Error("Uploading $extension files is denied", $this);
						if ($this->errorHandler('onUploadDeniedExtension', $e) !== PROCEED) {
							die();
						}
					}
				}

				$this->setActiveObject($this->objects[$object]);
				$action_return = $this->objects[$object]->$action($aParams);
			}

			if (isset($_REQUEST['_ajax']) and $_REQUEST['_ajax']) {
				$this->_action_history_id++;
				$this->raiseXMLReponse();
			} elseif (isset($_REQUEST['_rte_file_manager']) and isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
				require P4A_THEME_DIR . '/widgets/rich_textarea/editor/filemanager/browser/default/connectors/php/connector.php';
			} elseif (isset($_REQUEST['_upload_path'])) {
				$path = P4A_UPLOADS_PATH;
				if (isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
					$object =& $this->objects[$_REQUEST['_object_id']];
					if (is_object($object) and method_exists($object, 'getUploadSubpath')) {
						$path .= '/' . $object->getUploadSubpath();
					}
				}
				print preg_replace(array("~/+~", "~/$~"), array('/', ''), $path);
			} elseif (P4A_ENABLE_RENDERING and is_object($this->active_mask)) {
				$this->_action_history_id++;
				$this->active_mask->main();
			}

			$this->_to_redesign = array();
			$this->_redesign_popup = false;

			session_write_close();
			session_id(substr(session_id(), 0, -6));
			flush();
		}

		function raiseXMLReponse()
		{
			ob_start();
			$script_detector = '<script.*?>(.*?)<\/script>';

			header('Content-Type: text/xml');
			print '<?xml version="1.0" encoding="utf-8" ?><ajax-response action_id="' . $this->getActionHistoryId() . '">';
			while (list( ,$id) = each($this->_to_redesign)) {
				$object =& $this->getObject($id);
				$display = $object->isVisible() ? 'block' : 'none';
				$as_string = $object->getAsString();
				$javascript_codes = array();
				$javascript = '';
				$html = preg_replace("/{$script_detector}/si", '', $as_string);
				preg_match_all("/{$script_detector}/si", $as_string, $javascript_codes);
				$javascript_codes = $javascript_codes[1];
				foreach ($javascript_codes as $code) {
					$javascript .= "$code\n\n";
				}

				print "\n<widget id='$id' display='$display'>\n";
				print "<html><![CDATA[{$html}]]></html>\n";
				print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
				print "</widget>\n";

			}
			if ($this->_redesign_popup) {
				if ($this->_popup) {
					$popup =& p4a_mask::singleton($this->_popup);
					$html = $popup->getAsString();
					$javascript = 'showPopup();';
				} else {
					$html = '';
					$javascript = 'hidePopup();';
				}
				print "<widget id='popup' display='inherit'>\n";
				print "<html><![CDATA[<div id='popup' style='display:none'>{$html}</div>]]></html>\n";
				print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
				print "</widget>";
			}
			print "</ajax-response>";

			if (P4A_AJAX_DEBUG) {
				$fp = @fopen(P4A_COMPILE_DIR . '/p4a_ajax_debug.txt', 'w');
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}

			ob_end_flush();
		}

		/**
		 * Sets the desidered mask as active.
		 * @param string		The name of the mask.
		 * @access private
		 */
		function setActiveMask($mask_name)
		{
			$mask =& P4A_Mask::singleton($mask_name);
			$this->active_mask =& $mask;
		}

		/**
		 * Sets the desidered object as active.
		 * @param object object		The object
		 * @access private
		 * @see $active_object
		 */
		function setActiveObject(&$object)
		{
			unset($this->active_object);
			$this->active_object =& $object;
		}

		 /**
		 * Opens a mask ed sets it active.
		 * @access public
		 */
		function &openMask($mask_name)
		{
			if ($this->actionHandler('beforeOpenMask') == ABORT) return ABORT;

			if ($this->isActionTriggered('onOpenMask')) {
				if ($this->actionHandler('onOpenMask') == ABORT) return ABORT;
			} else {
				P4A_Mask::singleton($mask_name);

				//Update masks history
				if (is_object($this->active_mask) and $this->active_mask->getName() != $mask_name) {
					array_push($this->masks_history, $this->active_mask->getName());
					//50 max history
					$this->masks_history = array_slice($this->masks_history, -50);
				}

				$this->setActiveMask($mask_name);
			}
			$this->actionHandler('afterOpenMask');
			return $this->active_mask;
		}


		function openPopup($name)
		{
			//Close opened popup
			if ($this->_popup) {
				$this->closePopup();
			}

			$this->_popup = $name;
			$mask =& p4a_mask::singleton($this->_popup);
			$mask->isPopup(TRUE);

			$this->_redesign_popup = TRUE;
		}

		function closePopup($destroy = FALSE)
		{

			if ($destroy) {
				$mask =& p4a_mask::singleton($this->_popup);
				$mask->destroy();
			} else {
				$mask =& p4a_mask::singleton($this->_popup);
				$mask->isPopup(FALSE);
			}
			$this->_popup = NULL;
			$this->_redesign_popup = TRUE;
		}

		 /**
		 * Sets the previous mask the active mask
		 * @access public
		 */
	     function showPrevMask()
	     {
			//Close opened popup
			if ($this->_popup) {
				$this->closePopup();
				return;
			}

	     	if (sizeof($this->masks_history) > 0){
				$mask_name = array_pop($this->masks_history);
				$this->setActiveMask($mask_name);
	     	}
	     }

		 /**
		 * Gets an instance of the previous mask
		 * @access public
		 */
	     function &getPrevMask()
	     {
		 	$num_masks = sizeof($this->masks_history);
	     	if ($num_masks > 0){
				$mask_name = $this->masks_history[$num_masks-1];
				return $this->masks->$mask_name;
	     	}
	     }


		/**
		 * Checks if the desidered mask is in the masks collection.
		 * @param string		The mask's name.
		 * @access private
		 */
		function maskExists($mask_name)
		{
			if (array_key_exists($mask_name, $this->masks)){
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Adds an object to the objects collection.
		 * @param object object		The object.
		 * @access private
		 */
		function store(&$object)
		{
			$object_id = $object->getId();
			if (array_key_exists($object_id, $this->objects)){
				ERROR('DUPLICATE OBJECT');
			} else {
				$this->objects[$object_id] = &$object;
			}
		}

		//todo
		function &getObject($object_id)
		{
			if (array_key_exists($object_id, $this->objects)){
				return $this->objects[$object_id];
			} else {
				$return = null;
				return $return;
			}
		}

		/**
		 * Sets the title for the application.
		 * @param string	Mask title.
		 * @access public
		 */
		function setTitle($title)
		{
			$this->title = $title ;
		}

		/**
		 * Returns the title for the application.
		 * @return string
		 * @access public
		 */
		function getTitle()
		{
			return $this->title ;
		}

		/**
		 * Include CSS
		 * @param string		The URI of CSS.
		 * @param string		The CSS media.
		 * @access public
		 */
		function addCss($uri, $media = "screen")
		{
			if (!isset($this->_css[$uri])) {
				$this->_css[$uri] = array();
			}
			$this->_css[$uri][$media] = null;
		}

		/**
		 * Drop inclusion of CSS file
		 * @param string		The URI of CSS.
		 * @param string		The CSS media.
		 * @access public
		 */
		function dropCss($uri, $media = "screen")
		{
			if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])){
				unset($this->_css[$uri][$media]);
				if (empty($this->_css[$uri])) {
					unset($this->_css);
				}
			}
		}

		/**
		 * Include a javascript file
		 * @param string		The URI of file.
		 * @access public
		 */
		function addJavascript($uri)
		{
			$this->_javascript[$uri] = null;
		}

		/**
		 * Drop inclusion of javascript file
		 * @param string		The URI of CSS.
		 * @access public
		 */
		function dropJavascript($uri)
		{
			if(isset($this->_javascript[$uri])){
				unset($this->_javascript[$uri]);
			}
		}

		/**
		 * Action history ID is used to avoid browser's back/forward
		 * @access public
		 * @return integer
		 */
		function getActionHistoryId()
		{
			return $this->_action_history_id;
		}

		function redesign($id)
		{
			$this->_to_redesign[] = $id;
		}

		/**
		 * Gets P4A version
		 * @return string p4a version
		 * @access public
		 */
		function getVersion()
		{
			return P4A_VERSION;
		}
	}