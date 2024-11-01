<?php

/**
* YQL Tags
* Last updated 1/23/2010
* Copyright (c) 2010 Roger Stringer
* http://www.rogerstringer.com
* 
* Please see http://www.rogerstringer.com/yql-tags
* for documentation and license information.
*/
	define("MINIMUM_TAG_LENGTH","1"); 
	class YQLTagException extends Exception {}

	class YQLTag {
		const APIURL = "'http://query.yahooapis.com/v1/public/yql?q='";

		public function YQLTag($apikey = null) {
			return true;
		}
		
		public function getTags($content,$title=null) {
			$response = $this->callYQL($content,$title);
			$x = json_decode($response);
			$tags = array();
			if($x->query->results){
				foreach($x->query->results->Result as $row){
					$tags[] = $row;
				}
			}
			if( is_array($tags) && count($tags) > 0 ){
				array_walk($tags,create_function('&$value','$value = tagger_proper_case(trim($value));'));
				if(in_array('',$tags)) unset($tags[array_search('',$tags)]); // remove blanks
				$tags = array_unique_compact($tags);
				$i = 0;
				foreach( $tags as $key=>$val ) {
					if( mb_strlen(trim($val)) < MINIMUM_TAG_LENGTH || is_numeric(trim($val))) {
						unset($tags[$key]);
					}else{
						if ($i > 5) unset($tags[$key]);
						$i++;
					}
				}
			}
			return $tags;
		}
		
		private function callYQL($content, $title = null) {
			$content = html2txt($content);
			$title = html2txt($title);
			$content=preg_replace('|<[^<>]*>|',' ',"$title\n$content");
			$content=preg_replace('|\s{2,}|',' ',$content);
			$content=cleanup($content);
			$text=cleanup($title)." ".$content;
			$yql = 'SELECT * FROM search.termextract WHERE context = "'.$text.'"';
			$root = 'http://query.yahooapis.com/v1/public/yql?q=';
			$url = $root . urlencode($yql) . '&format=json';
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($curl_handle);
			curl_close($curl_handle);
			if (empty($response)){
				return 'Error retrieving data, please try later.';
			} else {
				return $response;
			}
		}
		private function urlencodeArray($array) {
			foreach ($array as $key => $val) {
				if (!isset($string)) {
					$string = $key . "=" . urlencode($val);
				} else {
					$string .= "&" . $key . "=" . urlencode($val);
				}
			}
			return $string;
		}
	}
	function slugs_stop_words () {
	    return array ("a", "ice", "able", "Tb ","teaspoon","taste","about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "c's", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t's", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "z", "zero");
	}	
	function cleanup($slug){
		$slug = preg_replace('/&.+?;\'"/', '', $slug); // kill HTML entities
		$slug = preg_replace ("/[^a-zA-Z ]/", "", $slug);
#		$slug_array = array_diff (split(" ", $slug), slugs_stop_words());
#		return implode(" ",$slug_array);
		return $slug;
	}	
	function tagger_proper_case($input) {
		return preg_replace_callback('|\b[a-z]|',create_function('$matches','return strtoupper($matches[0]);'),$input);
	}		
	function array_unique_compact($a){
		$tmparr = array_unique($a);
		$i=0;
		foreach ($tmparr as $v) {
			$newarr[$i] = $v;
			$i++;
		}
		return $newarr;
	}
	function html2txt($document){
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);
		return $text;
	} 	
?>