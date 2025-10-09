<?php

namespace ScoutEngines\Elasticsearch;

class ElasticsearchNormalizer{
	public static function normalizeTerm($term){
		if(empty($term) || is_numeric($term) || filter_var($term, FILTER_VALIDATE_URL)){
			return trim($term);
		}

		$term = strip_tags($term);
		$term = html_entity_decode($term, ENT_QUOTES, 'UTF-8');
		$term = self::removeAccents($term);
		$term = self::removeSpecialCharacters($term, ['-']);
		$term = self::clearFullString($term, ' ');
		$term = self::removeMultipliedCharacters('-', $term);
		$term = str_replace('-', ' ', $term);
		$term = self::removeMultipliedCharacters(' ', $term);

		return $term;
	}

	public static function normalizeRecursiveTerm(&$array) {
	    foreach ($array as $index => &$term) {
	    	$debug = [
	    		'index' => $index,
	    		'termo' => $term,
	    		'string' => is_string($term) || is_numeric($term),
	    	];

	    	// debug1($debug);

	    	if(in_array($index, ['created_at', 'updated_at', 'url', 'original_name'])){
	    		$term = trim($term);

	    		// debug1($array);
	    		// exit;

	    		continue;
	    	}

			if (is_string($term) || is_numeric($term)) {
				$term = self::normalizeTerm($term);
				continue;
			}

	        if(is_array($term)) {
	            self::normalizeRecursiveTerm($term);
	            continue;
	        }
	    }
	}

	private static function removeAccents($string) {
	    $search = [
	        'À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ',
	        'Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß',
	        'à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ',
	        'ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ',
	        'Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ',
	        'Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ',
	        'Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı',
	        'Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł',
	        'Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő',
	        'Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř',
	        'Ś','ś','Ŝ','ŝ','Ş','ş','Š','š',
	        'Ţ','ţ','Ť','ť','Ŧ','ŧ',
	        'Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų',
	        'Ŵ','ŵ','Ŷ','ŷ','Ÿ',
	        'Ź','ź','Ż','ż','Ž','ž','ſ','ƒ',
	        'Ơ','ơ','Ư','ư',
	        'Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ',
	        'Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ',
	        'Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ',
	        'Ḿ','ḿ','Ṁ','ṁ','Ṅ','ṅ','Ṕ','ṕ','Ṙ','ṙ','Ṡ','ṡ','Ṫ','ṫ',
	        'Ẁ','ẁ','Ẃ','ẃ','Ẅ','ẅ','Ẍ','ẍ','Ẑ','ẑ','ẞ'
	    ];

	    $replace = [
	        'A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N',
	        'O','O','O','O','O','O','U','U','U','U','Y','ss',
	        'a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n',
	        'o','o','o','o','o','o','u','u','u','u','y','y',
	        'A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d',
	        'E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g',
	        'H','h','H','h','I','i','I','i','I','i','I','i','I','i',
	        'IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','L','l',
	        'N','n','N','n','N','n','n','O','o','O','o','O','o',
	        'OE','oe','R','r','R','r','R','r',
	        'S','s','S','s','S','s','S','s',
	        'T','t','T','t','T','t',
	        'U','u','U','u','U','u','U','u','U','u','U','u',
	        'W','w','Y','y','Y',
	        'Z','z','Z','z','Z','z','s','f',
	        'O','o','U','u',
	        'A','a','I','i','O','o','U','u',
	        'U','u','U','u','U','u','U','u',
	        'A','a','AE','ae','O','o',
	        'M','m','M','m','N','n','P','p','R','r','S','s','T','t',
	        'W','w','W','w','W','w','X','x','Z','z','SS'
	    ];

	    return str_replace($search, $replace, $string);
	}

	private static function removeSpecialCharacters($string, $exception = []) {
		$search = [
		    '!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/',
		    ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~',
		    '¡','¿','«','»','¬','±','§','¨','©','ª','®','¯','°','²','³','´','µ','¶','·','¸','¹','º','¼','½','¾',
		    '‘','’','‚','‛','“','”','„','‟','‹','›',
		    '–','—','―','−','∙','·','•','‣',
		    '†','‡','•','‰','…','′','″','‴','※','⁂',
		    '¢','£','¤','¥','₣','₤','₧','€','₱','₵','₡','₭','₦','₨','₩','₪','₫','₭','₮','₯','₰','₲','₳','₴','₵','₶','₷','₸','₹','$'
		];

		foreach ($exception as $rule) {
			$key = array_search($rule, $search);
			if ($key !== false) {
				unset($search[$key]);
			}
		}

		$replace = array_fill(0, count($search), ''); // todos viram vazio

		return str_replace($search, $replace, $string);
	}

	private static function clearFullString($string, $character_replace = '-') {
		return self::removeAccents(str_replace(' ', $character_replace, self::clearString($string)));
	}

	public static function clearString($string) {
		return strtolower(trim(preg_replace('/\s+/', ' ', $string)));
	}

	private static function removeMultipliedCharacters($character, $string) {
		return preg_replace('/' . $character . '+/', $character, $string);
	}
}