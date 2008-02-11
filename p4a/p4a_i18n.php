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

require_once "Zend/Date.php";
require_once "Zend/Translate.php";
require_once "Zend/Translate/Adapter/Array.php";

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_I18N
{
	/**
	 * @var string
	 */
	private $locale = null;

	/**
	 * @var string
	 */
	private $language = null;

	/**
	 * @var string
	 */
	private $region = null;
	
	/**
	 * @var integer
	 */
	private $first_day_of_the_week = 0;
	
	/**
	 * @var Zend_Locale
	 */
	protected $_locale_engine = null;
	
	/**
	 * @var Zend_Translate_Adapter_Array
	 */
	protected $_translation_engine = null;

	/**
	 * @param string $locale
	 */
	public function __construct($locale = P4A_LOCALE)
	{
		$this->setLocale($locale);
	}

	/**
	 * Sets the desidered locale (it_IT|en_UK|en_US).
	 * @param string
	 */
	public function setLocale($locale = P4A_LOCALE)
	{
		$this->language = strtolower(substr($locale, 0, 2));
		$this->region = strtoupper(substr($locale, 3, 2));
		$this->locale = "{$this->language}_{$this->region}";
		
		$this->_locale_engine = new Zend_Locale($this->locale);
		$this->setFirstDayOfTheWeek();
		
		$messages = array();
		$this->mergeTranslationFile(dirname(__FILE__) . "/i18n/{$this->language}/LC_MESSAGES/p4a.mo", $messages);
		$this->mergeTranslationFile(dirname(__FILE__) . "/i18n/{$this->locale}/LC_MESSAGES/p4a.mo", $messages);
		
		$this->_translation_engine = new Zend_Translate(Zend_Translate::AN_ARRAY, $messages, $this->locale);
		//TODO: load application level translation
	}
	
	/**
	 * Reads a translation file (gettext) and merges it to the messages array
	 *
	 * @param string $file
	 * @param array $messages
	 */
	private function mergeTranslationFile($file, &$messages)
	{
		if (file_exists($file)) {
			$translate = new Zend_Translate('gettext', $file, $this->locale);
			$new_messages = $translate->getMessages();
			if (is_array($new_messages)) {
				$messages = array_merge($messages, $new_messages);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getRegion()
	{
		return $this->region;
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	public function translate($string)
	{
		return $this->_translation_engine->translate($string, $this->locale);
	}

	/**
	 * Reads a normalized value, localizes and returns it
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|currency)
	 * @param integer $num_of_decimals used only if type is float or decimal
	 * @return mixed
	 */
	public function format($value, $type, $num_of_decimals = null)
	{
		switch($type) {
			case 'boolean':
				$value = ($value == 1) ? 'yes' : 'no';
				$yes_no = $this->_locale_engine->getQuestion();
				return $yes_no[$value];
			case 'date':
				$date = new Zend_Date($value, Zend_Date::DATES, $this->_locale_engine);
				return $date->get(Zend_Date::DATES, $this->_locale_engine);
			case 'time':
				$date = new Zend_Date($value, Zend_Date::TIME_SHORT, $this->_locale_engine);
				return $date->get(Zend_Date::TIME_SHORT, $this->_locale_engine);
			case 'integer':
				return Zend_Locale_Format::toNumber($value, array('precision'=>0, 'locale'=>$this->_locale_engine));
			case 'float':
				if ($num_of_decimals === null) $num_of_decimals = 3;
				return Zend_Locale_Format::toNumber($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
			case 'decimal':
				if ($num_of_decimals === null) $num_of_decimals = 2;
				return Zend_Locale_Format::toNumber($value, array('precision'=>$num_of_decimals, 'locale'=>$this->_locale_engine));
		}

		return $value;
	}

	/**
	 * Reads a localized value, normalizes and returns it
	 * @param mixed $value
	 * @param string $type (boolean|date|time|integer|float|decimal|currency)
	 * @return mixed
	 */
	public function normalize($value, $type)
	{
		switch($type) {
			case 'boolean':
				$yes_no = Zend_Locale_Data::getContent($this->_locale_engine, 'questionstrings');
				$yes_regexp = '/^(' . str_replace(':', '|', $yes_no['yes']) . ')$/i';
				if (preg_match($yes_regexp, $value)) return 1;
				return 0;
			case 'date':
				$date =  Zend_Locale_Format::getDate($value, array('locale'=>$this->_locale_engine));
				return "{$date['year']}-{$date['month']}-{$date['day']}";
			case 'time':
				$date_format = Zend_Locale_Format::getTimeFormat($this->_locale_engine);
				$date =  Zend_Locale_Format::getDate($value, array('date_format'=>$date_format, 'locale'=>$this->_locale_engine));
				if (!isset($date['hour'])) $date['hour'] = '00';
				if (!isset($date['minute'])) $date['minute'] = '00';
				if (!isset($date['second'])) $date['second'] = '00';
				return "{$date['hour']}:{$date['minute']}:{$date['second']}";
			case 'integer':
				return Zend_Locale_Format::getInteger($value, array('locale'=>$this->_locale_engine));
			case 'float':
				return Zend_Locale_Format::getFloat($value, array('precision'=>3, 'locale'=>$this->_locale_engine));
			case 'decimal':
				return Zend_Locale_Format::getFloat($value, array('precision'=>2, 'locale'=>$this->_locale_engine));
		}

		return $value;
	}
	
	/**
	 * Clones and return the Zend_Locale engine
	 * @return Zend_Locale
	 */
	public function getLocaleEngine()
	{
		return clone $this->_locale_engine;
	}
	
	private function setFirstDayOfTheWeek()
	{
		$supplemental_data = simplexml_load_file(dirname(__FILE__) . '/libraries/Zend/Locale/Data/supplementalData.xml');
		
		$dayname = 'mon';
		foreach ($supplemental_data->xpath("//firstDay") as $data) {
			list($tmp_dayname, $territories) = $data->attributes();
			foreach (explode(' ', $territories) as $territory) {
				if ($territory == $this->region) {
					$dayname = $tmp_dayname;
					break 2;
				}
			}
		}
		
		switch ($dayname) {
			case 'mon':
				$daynumber = 1;
				break;
			case 'tue':
				$daynumber = 2;
				break;
			case 'wed':
				$daynumber = 3;
				break;
			case 'thu':
				$daynumber = 4;
				break;
			case 'fri':
				$daynumber = 5;
				break;
			case 'sat':
				$daynumber = 6;
				break;
			default:
				$daynumber = 0;
		}
		
		$this->first_day_of_the_week = $daynumber;
	}
	
	/**
	 * @return integer
	 */
	function getFirstDayOfTheWeek()
	{
		return $this->first_day_of_the_week;
	}
}