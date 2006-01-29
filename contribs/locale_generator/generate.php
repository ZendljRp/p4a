<?php

// generated on ubuntu linux with I18Nv2 0.11.3

error_reporting(E_ALL ^ E_NOTICE);
ini_set("include_path", ".:../../p4a/libraries/pear:I18Nv2");
require "I18Nv2/I18Nv2.php";
require "System.php";

$locales = file('locales');
$clean_locales = array();

// cleaning duplicated locales, first pass
foreach ($locales as $locale) {
	list($locale, $encoding) = explode(' ', trim($locale));
	if (strpos($locale, '_') === false) {
		continue;
	}
	
	$locale = explode('.', $locale);
	$locale = $locale[0];

	$locale = explode('@', $locale);
	$locale = $locale[0];	
	
	$clean_locales[trim($locale)] = $encoding;
}

// cleaning duplicated locales, second pass
foreach ($clean_locales as $locale=>$encoding) {
	$encoding = str_replace('-', '', strtolower($encoding));
	if (!I18Nv2::setLocale("$locale.$encoding")) {
		unset($clean_locales[$locale]);
	}
}

// writing it all
$template = file_get_contents('template.php');
foreach ($clean_locales as $locale=>$encoding) {
	//if ($locale != 'it_IT') continue;
	$encoding = str_replace('-', '', strtolower($encoding));
	list($dir, $file) = explode('_', $locale);
	system::mkdir("-p output/{$dir}");
	
	I18Nv2::setLocale("$locale.$encoding");
	$i = I18Nv2::getInfo();

	$l = I18Nv2::createLocale($locale);

	$towrite = $template;
	$towrite = str_replace('[DS]', addslashes($i['mon_decimal_point']), $towrite);
	$towrite = str_replace('[TS]', addslashes($i['mon_thousands_sep']), $towrite);
	
	if (empty($l->dateFormats)) {
		$towrite = mb_ereg_replace('\$datetime_formats(.*?);', '// we need date and time formats', $towrite, 'm');
	} else {
		$towrite = str_replace('[DATE_DEFAULT]', str_replace('%y', '%Y', $l->dateFormats['short']), $towrite);
		$towrite = str_replace('[DATE_MEDIUM]', $l->dateFormats['default'], $towrite);
		$towrite = str_replace('[DATE_LONG]', $l->dateFormats['long'], $towrite);
		$towrite = str_replace('[DATE_FULL]', $l->dateFormats['full'], $towrite);
		
		$towrite = str_replace('[TIME_DEFAULT]', $l->timeFormats['short'], $towrite);
		$towrite = str_replace('[TIME_LONG]', $l->timeFormats['default'], $towrite);
	}
	
	$towrite = str_replace('[LOCAL_CURRENCY_DECIMAILS]', $l->currencyFormats['local'][1], $towrite);
	$towrite = str_replace('[INTERNATIONAL_CURRENCY_DECIMAILS]', $l->currencyFormats['international'][1], $towrite);
	
	if ($i['p_sep_by_space']) {
		$i['currency_symbol'] = ' ' . $i['currency_symbol'] . ' ';
		$i['int_curr_symbol'] = ' ' . $i['int_curr_symbol'] . ' ';
	}
	
	$local_currency_print = '%';
	if ($i['p_cs_precedes']) {
		$local_currency_print = $i['currency_symbol'] . $local_currency_print;
	} else {
		$local_currency_print .= $i['currency_symbol'];
	}
	$towrite = str_replace('[LOCAL_CURRENCY_PRINT]', trim($local_currency_print), $towrite);
	
	$international_currency_print = '%';
	if ($i['p_cs_precedes']) {
		$international_currency_print = $i['int_curr_symbol'] . $international_currency_print;
	} else {
		$international_currency_print .= $i['int_curr_symbol'];
	}
	$towrite = str_replace('[INTERNATIONAL_CURRENCY_PRINT]', trim($international_currency_print), $towrite);
	
	$fp = fopen("output/{$dir}/{$file}.php", 'w');
	fwrite($fp, iconv($encoding, 'UTF-8', $towrite));
	fclose($fp);
}

?>
