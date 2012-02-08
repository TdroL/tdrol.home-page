<?php defined('SYSPATH') or die('No direct script access.');

class View_Layout extends Kostache_Layout {

	public function assets()
	{
		return Yassets::factory()
			// css
			->set('head.css.style', 'assets/css/style.css')
			// js
			->set('head.js.modernizr', 'assets/js/modernizr-2.5.2.min.js')
			->set('body.js.plugins', 'assets/js/plugins.js')

			->set('jquery-cdn', '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
			->set('jquery', 'assets/js/jquery-1.7.1.min.js');
	}

	public function env()
	{
		return array(
			'production'  => Kohana::$environment == Kohana::PRODUCTION,
			'development' => Kohana::$environment == Kohana::DEVELOPMENT,
		);
	}

	public function head()
	{
		return array(
			'lang' => I18n::lang(),
			'title' => '{tdroL}',
			'description' => 'tdroL (/te-drol/): a web developer with too much spare time.',
			'author' => 'tdroL',
			'noindex' => FALSE,
			'canonical' => NULL
		);
	}

	public function profiler()
	{
		if (Kohana::$profiling)
		{
			return View::factory('profiler/stats')->render();
		}

		return NULL;
	}

	public function promotejs()
	{
		$wide = true;
		// show image

		$global_objects = array(
			'Array' => array('isArray', 'constructor', 'index', 'input', 'length', 'pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift', 'concat', 'join', 'slice', 'toString', 'indexOf', 'lastIndexOf', 'forEach', 'map', 'some', 'every', 'filter', 'Creating an Array', 'Example: Creating a Two-dimensional Array'),
			'String' => array('prototype', 'fromCharCode', 'constructor', 'length', 'charAt', 'concat', 'indexOf', 'lastIndexOf', 'localeCompare', 'match', 'replace', 'search', 'slice', 'split', 'substr', 'substring', 'toLocaleLowerCase', 'toLocaleUpperCase','toLowerCase', 'toString', 'toUpperCase', 'valueOf'),
			'Number' => array('toExponential', 'toFixed', 'toLocaleString', 'toPrecision', 'toString', 'valueOf', 'Example: Using the Number object to assign values to numeric variables', 'Example: Using Number to convert a Date object'),
			'RegExp' => array('constructor', 'global', 'ignoreCase', 'lastIndex', 'multiline', 'source', 'exec', 'test', 'toString', 'Example: Using a regular expression to change data format', 'Example: Using a regular expression with the sticky flag'),
			'Function' => array('prototype', 'arguments', 'arity', 'constructor', 'length', 'apply', 'call', 'toString', 'Example: Specifying arguments with the Function constructor')
		);

		$combinations = array();

		foreach ($global_objects as $i => $attrs) {
			foreach ($attrs as $idx => $val) {
				$seo_string = array("JS ",$i," ",$val,", JavaScript ", $i, " ", $val);
				if (stripos($val, " ") < 0) {
					$seo_string = array_merge($seo_string, array(", JS ",$i, " .",$val,", JavaScript ", $i, " .", $val));
				}

				$parts = explode(",",implode("", $seo_string));
				foreach ($parts as $elem) {
				array_push($combinations, array(trim($elem), "https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/".$i));
				}
			}
		}

		$images = array(
			"http://static.jsconf.us/promotejsv.gif",
			"http://static.jsconf.us/promotejsh.gif"
		);

		array_push($combinations, array("JS Screencasts, Learn JS, JS Videos, JavaScript Screencasts, JS Education, JS Training, Proper JS", "http://learnjs.org"));
		array_push($combinations, array("Learning JavaScript with Object Graphs", "http://howtonode.org/object-graphs"));
		array_push($combinations, array("In Search of JavaScript Developers: A Gist", "http://blog.rebeccamurphey.com/in-search-of-javascript-developers-a-gist"));
		array_push($combinations, array("On Rolling Your Own, Enterprise jQuery, Enterprise JavaScript, Enterprise JS", "http://blog.rebeccamurphey.com/on-rolling-your-own"));
		array_push($combinations, array("Proper JS, Proper JavaScript Training, JS Tutorial, Learning JS, Eloquent JavaScript, Eloquent JS, JS Data Structures, JS DOM", "http://eloquentjavascript.net"));
		array_push($combinations, array("jQuery, jQuery Fundamantals, JS Fundamentals, JS jQuery, Learn jQuery, jQuery done right, Best jQuery Tutorial, best jQuery training", "http://jqfundamentals.com/book/book.html"));

		$tutorial_options = array("JS Tutorial", "JavaScript Tutorial", "JavaScript Guide", "Learn JavaScript JS", "How To Learn JS", "Learning JavaScript");
		$reference_options = array("JavaScript Reference", "JavaScript Guide", "JavaScript API", "JS API", "JS Guide", "JS Reference", "Learn JS", "JS Documentation");

		$counter = rand(1, 10);
		$alt = $tutorial_options[rand(0, count($tutorial_options)-1)];
		$href = "https://developer.mozilla.org/en/JavaScript/Guide";

		if ($counter % 10 == 0)
		{
			$alt = $reference_options[rand(0, count($reference_options) -1)];
			$href = "https://developer.mozilla.org/en/JavaScript";
		}
		else if ($counter % 5 != 0)
		{
			$i = rand(0, count($combinations)-1);
			$combo = $combinations[$i];
			$alt = $combo[0];
			$href = $combo[1];
		}

		$src = $images[$wide == true];

		return '<a href="'.$href.'" title="'.$alt.'"><img src="'.$src.'" alt="'.$alt.'"/></a>';
	}

	public function quote()
	{
		$quotes = Kohana::$config->load('quotes')->as_array();

		if (empty($quotes))
		{
			return NULL;
		}

		$i = round(time() / (60*60*24)) % (count($quotes) - 1);

		return trim($quotes[$i]);
	}

	public function security()
	{
		return array(
			'csfr' => Form::hidden('csfr', Security::token())
		);
	}

	public function url()
	{
		return array(
			'base' => Url::base(),
			'current' => Request::initial()->url(),
		);
	}

}

