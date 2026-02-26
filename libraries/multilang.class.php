<?php

//require_once ("libraries/smarty/Smarty.class.php");



/**

 * smarty_prefilter_i18n()

 * This function takes the language file, and rips it into the template

 * $GLOBALS['_NG_LANGUAGE_'] is not unset anymore

 *

 * @param $tpl_source

 * @return

 **/

function smarty_prefilter_i18n($tpl_source, &$smarty)
{

	if (!is_object($GLOBALS['_NG_LANGUAGE_'])) {

		die("Error loading Multilanguage Support");
	}

	// load translations (if needed)

	$GLOBALS['_NG_LANGUAGE_']->loadCurrentTranslationTable();

	// Now replace the matched language strings with the entry in the file

	return preg_replace_callback('/##(.+?)##/', '_compile_lang', $tpl_source);
}



/**

 * _compile_lang

 * Called by smarty_prefilter_i18n function it processes every language

 * identifier, and inserts the language string in its place.

 *

 */

function _compile_lang($key)
{

	return $GLOBALS['_NG_LANGUAGE_']->getTranslation($key[1]);
}


class smartyML extends Smarty
{
	var $language;
	function smartyML($locale = "")
	{

		//$this->Smarty();



		// Multilanguage Support

		// use $smarty->language->setLocale() to change the language of your template

		//     $smarty->loadTranslationTable() to load custom translation tables

		$this->language = new ngLanguage($locale); // create a new language object

		$GLOBALS['_NG_LANGUAGE_'] = &$this->language;

		$this->register_prefilter("smarty_prefilter_i18n");
	}



	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)
	{

		// We need to set the cache id and the compile id so a new script will be

		// compiled for each language. This makes things really fast ;-)

		$_smarty_compile_id = $this->language->getCurrentLanguage() . '-' . $_smarty_compile_id;

		$_smarty_cache_id = $_smarty_compile_id;



		// Now call parent method

		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}



	/**

	 * test to see if valid cache exists for this template

	 *

	 * @param string $tpl_file name of template file

	 * @param string $cache_id

	 * @param string $compile_id

	 * @return string|false results of {@link _read_cache_file()}

	 */

	function is_cached($tpl_file, $cache_id = null, $compile_id = null)

	{

		if (!$this->caching)

			return false;



		if (!isset($compile_id)) {

			$compile_id = $this->language->getCurrentLanguage() . '-' . $this->compile_id;

			$cache_id = $compile_id;
		}



		return parent::is_cached($tpl_file, $cache_id, $compile_id);
	}
}



class ngLanguage
{

	var $_translationTable;        // currently loaded translation table

	var $_supportedLanguages;      // array of all supported languages

	var $_defaultLocale;           // the default language

	var $_currentLocale;           // currently set locale

	var $_currentLanguage;         // currently loaded language

	var $_languageTable;           // array of language to file associations

	var $_loadedTranslationTables; // array of all loaded translation tables



	function ngLanguage($locale = "")
	{

		$this->_languageTable = array(

			"af" => "af",

			"ax" => "ax",

			"al" => "al",

			"dz" => "dz",

			"as" => "as",

			"ad" => "ad",

			"ao" => "ao",

			"ai" => "ai",

			"aq" => "aq",

			"ag" => "ag",

			"ar" => "ar",

			"ti" => "ti",

			"am" => "am",

			"aw" => "aw",

			"au" => "au",

			"at" => "at",

			"az" => "az",

			"bs" => "bs",

			"bh" => "bh",

			"bd" => "bd",

			"bb" => "bb",

			"by" => "by",

			"be" => "be",

			"bz" => "bz",

			"bj" => "bj",

			"bm" => "bm",

			"bt" => "bt",

			"bo" => "bo",

			"ba" => "ba",

			"bw" => "bw",

			"bv" => "bv",

			"br" => "br",

			"bq" => "bq",

			"io" => "io",

			"vg" => "vg",

			"bn" => "bn",

			"bg" => "bg",

			"bf" => "bf",

			"bi" => "bi",

			"kh" => "kh",

			"cm" => "cm",

			"ca" => "ca",

			"ct" => "ct",

			"cv" => "cv",

			"ky" => "ky",

			"cf" => "cf",

			"td" => "td",

			"cl" => "cl",

			"cn" => "cn",

			"cx" => "cx",

			"cc" => "cc",

			"co" => "co",

			"km" => "km",

			"cg" => "cg",

			"cd" => "cd",

			"ck" => "ck",

			"cr" => "cr",

			"hr" => "hr",

			"cu" => "cu",

			"cy" => "cy",

			"cz" => "cz",

			"dk" => "dk",

			"dj" => "dj",

			"dm" => "dm",

			"do" => "do",

			"nq" => "nq",

			"dd" => "dd",

			"tl" => "tl",

			"ec" => "ec",

			"eg" => "eg",

			"sv" => "sv",

			"gq" => "gq",

			"er" => "er",

			"ee" => "ee",

			"et" => "et",

			"qu" => "qu",

			"fk" => "fk",

			"fo" => "fo",

			"fj" => "fj",

			"fi" => "fi",

			"fr" => "fr",

			"gf" => "gf",

			"pf" => "pf",

			"tf" => "tf",

			"fq" => "fq",

			"ga" => "ga",

			"gm" => "gm",

			"ge" => "ge",

			"de" => "de",

			"gh" => "gh",

			"gi" => "gi",

			"gr" => "gr",

			"gl" => "gl",

			"gd" => "gd",

			"gp" => "gp",

			"gu" => "gu",

			"gt" => "gt",

			"gg" => "gg",

			"gn" => "gn",

			"gw" => "gw",

			"gy" => "gy",

			"ht" => "ht",

			"hm" => "hm",

			"hn" => "hn",

			"hk" => "hk",

			"hu" => "hu",

			"is" => "is",

			"in" => "in",

			"id" => "id",

			"ir" => "ir",

			"iq" => "iq",

			"ie" => "ie",

			"im" => "im",

			"il" => "il",

			"it" => "it",

			"ci" => "ci",

			"jm" => "jm",

			"jp" => "jp",

			"je" => "je",

			"jt" => "jt",

			"jo" => "jo",

			"kz" => "kz",

			"ke" => "ke",

			"ki" => "ki",

			"kw" => "kw",

			"kg" => "kg",

			"la" => "la",

			"lv" => "lv",

			"lb" => "lb",

			"ls" => "ls",

			"lr" => "lr",

			"ly" => "ly",

			"li" => "li",

			"lt" => "lt",

			"lu" => "lu",

			"mo" => "mo",

			"mk" => "mk",

			"mg" => "mg",

			"mw" => "mw",

			"my" => "my",

			"mv" => "mv",

			"ml" => "ml",

			"mt" => "mt",

			"mh" => "mh",

			"mq" => "mq",

			"mr" => "mr",

			"mu" => "mu",

			"yt" => "yt",

			"fx" => "fx",

			"mx" => "mx",

			"fm" => "fm",

			"mi" => "mi",

			"md" => "md",

			"mc" => "mc",

			"mn" => "mn",

			"me" => "me",

			"ms" => "ms",

			"ma" => "ma",

			"mz" => "mz",

			"mm" => "mm",

			"na" => "na",

			"nr" => "nr",

			"np" => "np",

			"nl" => "nl",

			"an" => "an",

			"nt" => "nt",

			"nc" => "nc",

			"nz" => "nz",

			"ni" => "ni",

			"ne" => "ne",

			"ng" => "ng",

			"nu" => "nu",

			"nf" => "nf",

			"kp" => "kp",

			"vd" => "vd",

			"mp" => "mp",

			"no" => "no",

			"om" => "om",

			"qo" => "qo",

			"pc" => "pc",

			"pk" => "pk",

			"pw" => "pw",

			"ps" => "ps",

			"pa" => "pa",

			"pz" => "pz",

			"pg" => "pg",

			"py" => "py",

			"yd" => "yd",

			"pe" => "pe",

			"ph" => "ph",

			"pn" => "pn",

			"pl" => "pl",

			"pt" => "pt",

			"pr" => "pr",

			"qa" => "qa",

			"re" => "re",

			"ro" => "ro",

			"ru" => "ru",

			"rw" => "rw",

			"bl" => "bl",

			"sh" => "sh",

			"kn" => "kn",

			"lc" => "lc",

			"mf" => "mf",

			"pm" => "pm",

			"vc" => "vc",

			"ws" => "ws",

			"sm" => "sm",

			"st" => "st",

			"sa" => "sa",

			"sn" => "sn",

			"rs" => "rs",

			"cs" => "cs",

			"sc" => "sc",

			"sl" => "sl",

			"sg" => "sg",

			"sk" => "sk",

			"si" => "si",

			"sb" => "sb",

			"so" => "so",

			"za" => "za",

			"gs" => "gs",

			"kr" => "kr",

			"es" => "es",

			"lk" => "lk",

			"sd" => "sd",

			"sr" => "sr",

			"sj" => "sj",

			"sz" => "sz",

			"se" => "se",

			"ch" => "ch",

			"sy" => "sy",

			"tw" => "tw",

			"tj" => "tj",

			"tz" => "tz",

			"th" => "th",

			"tg" => "tg",

			"tk" => "tk",

			"to" => "to",

			"tt" => "tt",

			"tn" => "tn",

			"tr" => "tr",

			"tm" => "tm",

			"tc" => "tc",

			"tv" => "tv",

			"pu" => "pu",

			"vi" => "vi",

			"ug" => "ug",

			"ua" => "ua",

			"su" => "su",

			"ae" => "ae",

			"gb" => "gb",

			"en" => "en",

			"um" => "um",

			"zz" => "zz",

			"uy" => "uy",

			"uz" => "uz",

			"vu" => "vu",

			"va" => "va",

			"ve" => "ve",

			"vn" => "vn",

			"wk" => "wk",

			"wf" => "wf",

			"eh" => "eh",

			"ye" => "ye",

			"zm" => "zm",

			"zw" => "zw"

		); // to be continued ...

		$this->_translationTable = array();

		$this->_loadedTranslationTables = array();

		foreach ($this->_languageTable as $lang)

			$this->_translationTable[$lang] = array();



		$this->_defaultLocale = 'en';

		if (empty($locale))

			$locale = $this->getHTTPAcceptLanguage();

		$this->setCurrentLocale($locale);
	}



	function getAvailableLocales()
	{

		return array_keys($this->_languageTable);
	}



	function getAvailableLanguages()
	{

		return array_unique(array_values($this->_languageTable));
	}



	function getCurrentLanguage()
	{

		return $this->_currentLanguage;
	}



	function setCurrentLanguage($language)
	{

		$this->_currentLanguage = $language;
	}



	function getCurrentLocale()
	{

		return $this->_currentLocale;
	}



	function setCurrentLocale($locale)
	{

		$language = $this->_languageTable[$locale];

		if (empty($language)) {

			die("LANGUAGE Error: Unsupported locale '$locale'");
		}

		$this->_currentLocale = $locale;

		return $this->setCurrentLanguage($language);
	}



	function getDefaultLocale()
	{

		return $this->_defaultLocale;
	}



	function getHTTPAcceptLanguage()
	{

		$langs = explode(';', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);

		$locales = $this->getAvailableLocales();

		foreach ($langs as $value_and_quality) {

			// Loop through all the languages, to see if any match our supported ones

			$values = explode(',', $value_and_quality);

			foreach ($values as $value) {

				if (in_array($value, $locales)) {

					// If found, return the language

					return $value;
				}
			}
		}

		// If we can't find a supported language, we use the default

		return $this->getDefaultLocale();
	}



	// Warning: parameter positions are changed!

	function _loadTranslationTable($locale, $path = '')
	{

		if (empty($locale))

			$locale = $this->getDefaultLocale();

		$language = $this->_languageTable[$locale];

		if (empty($language)) {

			die("LANGUAGE Error: Unsupported locale '$locale'");
		}

		if (!is_array($this->_translationTable[$language])) {

			die("LANGUAGE Error: Language '$language' not available");
		}

		if (empty($path))

			$path = 'languages/' . $this->_languageTable[$locale] . '/global.lng';

		if (isset($this->_loadedTranslationTables[$language])) {

			if (in_array($path, $this->_loadedTranslationTables[$language])) {

				// Translation table was already loaded

				return true;
			}
		}

		if (file_exists($path)) {

			$entries = file($path);

			$this->_translationTable[$language][$path] = array();

			$this->_loadedTranslationTables[$language][] = $path;

			foreach ($entries as $row) {

				if (substr(ltrim($row), 0, 2) == '//') // ignore comments

					continue;

				$keyValuePair = explode('=', $row);

				// multiline values: the first line with an equal sign '=' will start a new key=value pair

				if (sizeof($keyValuePair) == 1) {

					$this->_translationTable[$language][$path][$key] .= ' ' . chop($keyValuePair[0]);

					continue;
				}

				$key = trim($keyValuePair[0]);

				$value = $keyValuePair[1];

				if (!empty($key)) {

					$this->_translationTable[$language][$path][$key] = chop($value);
				}
			}

			return true;
		}

		return false;
	}



	// Warning: parameter positions are changed!

	function _unloadTranslationTable($locale, $path)
	{

		$language = $this->_languageTable[$locale];

		if (empty($language)) {

			die("LANGUAGE Error: Unsupported locale '$locale'");
		}

		unset($this->_translationTable[$language][$path]);

		foreach ($this->_loadedTranslationTables[$language] as $key => $value) {

			if ($value == $path) {

				unset($this->_loadedTranslationTables[$language][$key]);

				break;
			}
		}

		return true;
	}



	function loadCurrentTranslationTable()
	{

		$this->_loadTranslationTable($this->getCurrentLocale());
	}



	// Warning: parameter positions are changed!

	function loadTranslationTable($locale, $path)
	{

		// This method is only a placeholder and wants to be overwritten by YOU! ;-)

		// Here's a example how it could look:

		if (empty($locale)) {

			// Load default locale of no one has been specified

			$locale = $this->getDefaultLocale();
		}

		// Select corresponding language

		$language = $this->_languageTable[$locale];

		// Set path and filename of the language file

		$path = "languages/$language/$path.lng";

		// _loadTranslationTable() does the rest

		$this->_loadTranslationTable($locale, $path);
	}



	// Warning: parameter positions are changed!

	function unloadTranslationTable($locale, $path)
	{

		// This method is only a placeholder and wants to be overwritten by YOU! ;-)

		$this->_unloadTranslationTable($locale, $path);
	}



	function getTranslation($key)
	{

		$trans = $this->_translationTable[$this->_currentLanguage];

		if (is_array($trans)) {
			if (
				isset($this->_loadedTranslationTables[$this->_currentLanguage]) &&
				is_array($this->_loadedTranslationTables[$this->_currentLanguage])
			)
				foreach ($this->_loadedTranslationTables[$this->_currentLanguage] as $table) {

					if (isset($trans[$table][$key])) {

						return $trans[$table][$key];
					}
				}
		}

		return $key;
	}
}
