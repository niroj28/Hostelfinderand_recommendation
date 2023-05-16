<?php 

include ("config/config.php");


if(isset($_POST['review'])){
	review();
}


function review(){
	global $db;
	$property_id=$_GET['property_id'];
$comment=$_POST['comment'];
$rating=$_POST['rating'];



class Config
{
   

    // (empirically derived mean sentiment intensity rating increase for booster words)
    const B_INCR = 0.293;
    const B_DECR = -0.293;

    // (empirically derived mean sentiment intensity rating increase for using
    // ALLCAPs to emphasize a word)
    const C_INCR = 0.733;

    const N_SCALAR =  -0.74;
    // for removing punctuation
    //const REGEX_REMOVE_PUNCTUATION = re.compile('[%s]' % re.escape(string.punctuation))
             
    const NEGATE = ["aint", "arent", "cannot", "cant", "couldnt", "darent", "didnt", "doesnt",
        "ain't", "aren't", "can't", "couldn't", "daren't", "didn't", "doesn't",
        "dont", "hadnt", "hasnt", "havent", "isnt", "mightnt", "mustnt", "neither",
        "don't", "hadn't", "hasn't", "haven't", "isn't", "mightn't", "mustn't",
        "neednt", "needn't", "never", "none", "nope", "nor", "not", "nothing", "nowhere",
        "oughtnt", "shant", "shouldnt", "uhuh", "wasnt", "werent",
        "oughtn't", "shan't", "shouldn't", "uh-uh", "wasn't", "weren't",
        "without", "wont", "wouldnt", "won't", "wouldn't", "rarely", "seldom", "despite"];

    //booster/dampener 'intensifiers' or 'degree adverbs'
    //http://en.wiktionary.org/wiki/Category:English_degree_adverbs

    const BOOSTER_DICT = ["absolutely"=> self::B_INCR, "amazingly"=> self::B_INCR, "awfully"=> self::B_INCR, "completely"=> self::B_INCR, "considerably"=> self::B_INCR,
     "decidedly"=> self::B_INCR, "deeply"=> self::B_INCR, "effing"=> self::B_INCR,"enormous"=> self::B_INCR, "enormously"=> self::B_INCR,
     "entirely"=> self::B_INCR, "especially"=> self::B_INCR, "exceptionally"=> self::B_INCR, "extremely"=> self::B_INCR,
     "fabulously"=> self::B_INCR, "flipping"=> self::B_INCR, "flippin"=> self::B_INCR,
     "fricking"=> self::B_INCR, "frickin"=> self::B_INCR, "frigging"=> self::B_INCR, "friggin"=> self::B_INCR, "fully"=> self::B_INCR, "fucking"=> self::B_INCR,
     "greatly"=> self::B_INCR, "hella"=> self::B_INCR, "highly"=> self::B_INCR, "hugely"=> self::B_INCR, "incredibly"=> self::B_INCR,
     "intensely"=> self::B_INCR, "majorly"=> self::B_INCR, "more"=> self::B_INCR, "most"=> self::B_INCR, "particularly"=> self::B_INCR,
     "purely"=> self::B_INCR, "quite"=> self::B_INCR, "seemingly" => self::B_INCR, "really"=> self::B_INCR, "remarkably"=> self::B_INCR,
     "so"=> self::B_INCR, "substantially"=> self::B_INCR,
     "thoroughly"=> self::B_INCR, "totally"=> self::B_INCR, "tremendous"=> self::B_INCR, "tremendously"=> self::B_INCR,
     "uber"=> self::B_INCR, "unbelievably"=> self::B_INCR, "unusually"=> self::B_INCR, "utterly"=> self::B_INCR,
     "very"=> self::B_INCR,
     "almost"=> self::B_DECR, "barely"=> self::B_DECR, "hardly"=> self::B_DECR, "just enough"=> self::B_DECR,
     "kind of"=> self::B_DECR, "kinda"=> self::B_DECR, "kindof"=> self::B_DECR, "kind-of"=> self::B_DECR,
     "less"=> self::B_DECR, "little"=> self::B_DECR, "marginally"=> self::B_DECR, "occasional"=> self::B_DECR, "occasionally"=> self::B_DECR, "partly"=> self::B_DECR,
     "scarcely"=> self::B_DECR, "slightly"=> self::B_DECR, "somewhat"=> self::B_DECR,
     "sort of"=> self::B_DECR, "sorta"=> self::B_DECR, "sortof"=> self::B_DECR, "sort-of"=> self::B_DECR];


     # check for sentiment laden idioms that do not contain lexicon words (future work, not yet implemented)
    const SENTIMENT_LADEN_IDIOMS = ["cut the mustard"=> 2, "hand to mouth"=> -2,
                          "back handed"=> -2, "blow smoke"=> -2, "blowing smoke"=> -2,
                          "upper hand"=> 1, "break a leg"=> 2,
                          "cooking with gas"=> 2, "in the black"=> 2, "in the red"=> -2,
                          "on the ball"=> 2, "under the weather"=> -2];

    // check for special case idioms using a sentiment-laden keyword known to SAGE
    const SPECIAL_CASE_IDIOMS = ["the shit"=> 3, "the bomb"=> 3, "bad ass"=> 1.5, "bus stop"=> 0.0, "yeah right"=> -2, "cut the mustard"=> 2, "kiss of death"=> -1.5, "hand to mouth"=> -2, "beating heart"=> 3.1,"broken heart"=> -2.9,  "to die for"=> 3];
    ##Static methods##

    /*
        Normalize the score to be between -1 and 1 using an alpha that
        approximates the max expected value
    */
    public static function normalize($score, $alpha = 15)
    {
        $norm_score = $score/sqrt(($score*$score) + $alpha);
        return $norm_score;
    }
}



class SentiText
{

    private $text = "";
    public $words_and_emoticons = null;
    public $is_cap_diff = null;

    const PUNC_LIST = [".", "!", "?", ",", ";", ":", "-", "'", "\"",
             "!!", "!!!", "??", "???", "?!?", "!?!", "?!?!", "!?!?"];


    function __construct($text)
    {
        //checking that is string
        //if (!isinstance(text, str)){
        //    text = str(text.encode('utf-8'));
        //}
        $this->text = $text;
        $this->words_and_emoticons = $this->_words_and_emoticons();
        // doesn't separate words from\
        // adjacent punctuation (keeps emoticons & contractions)
        $this->is_cap_diff = $this->allcap_differential($this->words_and_emoticons);
    }

    /*
        Remove all punctation from a string
    */
    function strip_punctuation($string)
    {
        //$string = strtolower($string);
        return preg_replace("/[[:punct:]]+/", "", $string);
    }

    function array_count_values_of($haystack, $needle)
    {
        if (!in_array($needle, $haystack, true)) {
            return 0;
        }
        $counts = array_count_values($haystack);
        return $counts[$needle];
    }

    /*
        Check whether just some words in the input are ALL CAPS

        :param list words: The words to inspect
        :returns: `True` if some but not all items in `words` are ALL CAPS
    */
    private function allcap_differential($words)
    {

        $is_different = false;
        $allcap_words = 0;
        foreach ($words as $word) {
            //ctype is affected by the local of the processor see manual for more details
            if (ctype_upper($word)) {
                $allcap_words += 1;
            }
        }
        $cap_differential = count($words) - $allcap_words;
        if ($cap_differential > 0 && $cap_differential < count($words)) {
            $is_different = true;
        }
        return $is_different;
    }

    function _words_only()
    {
        $text_mod = $this->strip_punctuation($this->text);
        // removes punctuation (but loses emoticons & contractions)
        $words_only = preg_split('/\s+/', $text_mod);
        # get rid of empty items or single letter "words" like 'a' and 'I'
        $works_only = array_filter($words_only, function ($word) {
            return strlen($word) > 1;
        });
        return $words_only;
    }

    function _words_and_emoticons()
    {

        $wes = preg_split('/\s+/', $this->text);

        # get rid of residual empty items or single letter words
        $wes = array_filter($wes, function ($word) {
            return strlen($word) > 1;
        });
        //Need to remap the indexes of the array
        $wes = array_values($wes);
        $words_only = $this->_words_only();

        foreach ($words_only as $word) {
            foreach (self::PUNC_LIST as $punct) {
                //replace all punct + word combinations with word
                $pword = $punct .$word;


                $x1 = $this->array_count_values_of($wes, $pword);
                while ($x1 > 0) {
                    $i = array_search($pword, $wes, true);
                    unset($wes[$i]);
                    array_splice($wes, $i, 0, $word);
                    $x1 = $this->array_count_values_of($wes, $pword);
                }
                //Do the same as above but word then punct
                $wordp = $word . $punct;
                $x2 = $this->array_count_values_of($wes, $wordp);
                while ($x2 > 0) {
                    $i = array_search($wordp, $wes, true);
                    unset($wes[$i]);
                    array_splice($wes, $i, 0, $word);
                    $x2 = $this->array_count_values_of($wes, $wordp);
                }
            }
        }

        return $wes;
    }
}


/*
    Give a sentiment intensity score to sentences.
*/

class Analyzer
{
    private $lexicon_file = "";
    private $lexicon = "";

    private $current_sentitext = null;

    public function __construct($lexicon_file = "Lexicons/vader_sentiment_lexicon.txt",$emoji_lexicon='Lexicons/emoji_utf8_lexicon.txt')
    {
        //Not sure about this as it forces lexicon file to be in the same directory as executing script
        $this->lexicon_file = __DIR__ . DIRECTORY_SEPARATOR . $lexicon_file;
        $this->lexicon = $this->make_lex_dict();

        $this->emoji_lexicon = __DIR__ . DIRECTORY_SEPARATOR .$emoji_lexicon;

        $this->emojis = $this->make_emoji_dict();
    }

    /*
        Determine if input contains negation words
    */
    public function IsNegated($wordToTest, $include_nt = true)
    {
        $wordToTest = strtolower($wordToTest);
        if (in_array($wordToTest, Config::NEGATE)) {
            return true;
        }

        if ($include_nt) {
            if (strpos($wordToTest, "n't")) {
                return true;
            }
        }

        return false;
    }

    /*
        Convert lexicon file to a dictionary
    */
    public function make_lex_dict()
    {
        $lex_dict = [];
        $fp = fopen($this->lexicon_file, "r");
        if (!$fp) {
            die("Cannot load lexicon file");
        }

        while (($line = fgets($fp, 4096)) !== false) {
            list($word, $measure) = explode("\t", trim($line));
            //.strip().split('\t')[0:2]
            $lex_dict[$word] = $measure;
            //lex_dict[word] = float(measure)
        }

        return $lex_dict;
    }


    public function make_emoji_dict() {
        $emoji_dict = [];
        $fp = fopen($this->emoji_lexicon, "r");
        if (!$fp) {
            die("Cannot load emoji lexicon file");
        }

        while (($line = fgets($fp, 4096)) !== false) {
            list($emoji, $description) = explode("\t", trim($line));
            //.strip().split('\t')[0:2]
            $emoji_dict[$emoji] = $description;
            //lex_dict[word] = float(measure)
        }
        return $emoji_dict;
    }

    public function updateLexicon($arr)
    {
        if(!is_array($arr)) return [];
        $lexicon = [];
        foreach ($arr as $word => $valence) {
            $this->lexicon[strtolower($word)] = is_numeric($valence)? $valence : 0;
        }
    }

    private function IsKindOf($firstWord, $secondWord)
    {
        return "kind" === strtolower($firstWord) && "of" === strtolower($secondWord);
    }

    private function IsBoosterWord($word)
    {
        return array_key_exists(strtolower($word), Config::BOOSTER_DICT);
    }

    private function getBoosterScaler($word)
    {
        return Config::BOOSTER_DICT[strtolower($word)];
    }

    private function IsInLexicon($word)
    {
        $lowercase = strtolower($word);

        return array_key_exists($lowercase, $this->lexicon);
    }

    private function IsUpperCaseWord($word)
    {
        return ctype_upper($word);
    }

    private function getValenceFromLexicon($word)
    {
        return $this->lexicon[strtolower($word)];
    }

    private function getTargetWordFromContext($wordInContext)
    {
        return $wordInContext[count($wordInContext)-1];
    }

    /*
        Gets the precedding two words to check for emphasis
    */
    private function getWordInContext($wordList, $currentWordPosition)
    {
        $precedingWordList =[];

        //push the actual word on to the context list
        array_unshift($precedingWordList, $wordList[$currentWordPosition]);
        //If the word position is greater than 2 then we know we are not going to overflow
        if (($currentWordPosition-1)>=0) {
            array_unshift($precedingWordList, $wordList[$currentWordPosition-1]);
        } else {
            array_unshift($precedingWordList, "");
        }

        if (($currentWordPosition-2)>=0) {
            array_unshift($precedingWordList, $wordList[$currentWordPosition-2]);
        } else {
            array_unshift($precedingWordList, "");
        }

        if (($currentWordPosition-3)>=0) {
            array_unshift($precedingWordList, $wordList[$currentWordPosition-3]);
        } else {
            array_unshift($precedingWordList, "");
        }

        return $precedingWordList;
    }

    /*
        Return a float for sentiment strength based on the input text.
        Positive values are positive valence, negative value are negative
        valence.
    */
    public function getSentiment($text)
    {

        $text_no_emoji = '';
        $prev_space = true;

        foreach($this->str_split_unicode($text) as $unichr ) {
            if (array_key_exists($unichr, $this->emojis)) {
                $description = $this->emojis[$unichr];
                if (!($prev_space)) {
                    $text_no_emoji .= ' ';
                }
                $text_no_emoji .= $description;
                $prev_space = false;
            }
            else {
                $text_no_emoji .= $unichr;
                $prev_space = ($unichr == ' ');
            }
        }
        $text = trim($text_no_emoji);

        $this->current_sentitext = new SentiText($text);

        $sentiments = [];
        $words_and_emoticons = $this->current_sentitext->words_and_emoticons;

        for ($i=0; $i<=count($words_and_emoticons)-1; $i++) {
            $valence = 0.0;
            $wordBeingTested = $words_and_emoticons[$i];

            //If this is a booster word add a 0 valances then go to next word as it does not express sentiment directly
           /* if ($this->IsBoosterWord($wordBeingTested)){
                echo "\t\tThe word is a booster word: setting sentiment to 0.0\n";
            }*/
 //var_dump($i);
            //If the word is not in the Lexicon then it does not express sentiment. So just ignore it.
            if ($this->IsInLexicon($wordBeingTested)) {

                //Special case because kind is in the lexicon so the modifier kind of needs to be skipped
                if ("kind" !=$words_and_emoticons[$i] && "of" != $words_and_emoticons[$i]) {
                    $valence = $this->getValenceFromLexicon($wordBeingTested);

                    $wordInContext = $this->getWordInContext($words_and_emoticons, $i);
                    //If we are here then we have a word that enhance booster words
                    $valence = $this->adjustBoosterSentiment($wordInContext, $valence);
                }
            }
            array_push($sentiments, $valence);
        }
        //Once we have a sentiment for each word adjust the sentimest if but is present
        $sentiments = $this->_but_check($words_and_emoticons, $sentiments);

        return $this->score_valence($sentiments, $text);
    }


    private function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }


    private function applyValenceCapsBoost($targetWord, $valence)
    {
        if ($this->IsUpperCaseWord($targetWord) && $this->current_sentitext->is_cap_diff) {
            if ($valence > 0) {
                $valence += Config::C_INCR;
            } else {
                $valence -= Config::C_INCR;
            }
        }

        return $valence;
    }

    /*
        Check if the preceding words increase, decrease, or negate/nullify the
        valence
     */
    private function boosterScaleAdjustment($word, $valence)
    {
        $scalar = 0.0;
        if (!$this->IsBoosterWord($word)) {
            return $scalar;
        }

        $scalar = $this->getBoosterScaler($word);

        if ($valence < 0) {
            $scalar *= -1;
        }
        //check if booster/dampener word is in ALLCAPS (while others aren't)
        $scalar = $this->applyValenceCapsBoost($word, $scalar);

        return $scalar;
    }

    // dampen the scalar modifier of preceding words and emoticons
    // (excluding the ones that immediately preceed the item) based
    // on their distance from the current item.
    private function dampendBoosterScalerByPosition($booster, $position)
    {
        if (0===$booster) {
            return $booster;
        }

        if (1==$position) {
            return $booster*0.95;
        }

        if (2==$position) {
            return $booster*0.9;
        }

        return $booster;
    }

    private function adjustBoosterSentiment($wordInContext, $valence)
    {
        //The target word is always the last word
        $targetWord = $this->getTargetWordFromContext($wordInContext);

        //check if sentiment laden word is in ALL CAPS (while others aren't) and apply booster
        $valence = $this->applyValenceCapsBoost($targetWord, $valence);

        $valence = $this->modifyValenceBasedOnContext($wordInContext, $valence);

        return $valence;
    }

    private function modifyValenceBasedOnContext($wordInContext, $valence)
    {
        $wordToTest = $this->getTargetWordFromContext($wordInContext);
            //if($this->IsInLexicon($wordToTest)){
            //  continue;
            //}
        for ($i=0; $i<count($wordInContext)-1; $i++) {
            $scalarValue = $this->boosterScaleAdjustment($wordInContext[$i], $valence);
            $scalarValue = $this->dampendBoosterScalerByPosition($scalarValue, $i);
            $valence = $valence+$scalarValue;
        }

        $valence = $this->_never_check($wordInContext, $valence);

        $valence = $this->_idioms_check($wordInContext, $valence);

        // future work: consider other sentiment-laden idioms
        // other_idioms =
        // {"back handed": -2, "blow smoke": -2, "blowing smoke": -2,
        //  "upper hand": 1, "break a leg": 2,
        //  "cooking with gas": 2, "in the black": 2, "in the red": -2,
        //  "on the ball": 2,"under the weather": -2}

        $valence = $this->_least_check($wordInContext, $valence);

        return $valence;
    }

    public function _least_check($wordInContext, $valence)
    {
        // check for negation case using "least"
        //if the previous word is least"
        if (strtolower($wordInContext[2]) == "least") {
            //but not "at least {word}" "very least {word}"
            if (strtolower($wordInContext[1]) != "at" && strtolower($wordInContext[1]) != "very") {
                $valence = $valence*Config::N_SCALAR;
            }
        }

        return $valence;
    }

    public function _but_check($words_and_emoticons, $sentiments)
    {
        // check for modification in sentiment due to contrastive conjunction 'but'
        $bi = array_search("but", $words_and_emoticons);
        if (!$bi) {
            $bi = array_search("BUT", $words_and_emoticons);
        }
        if ($bi) {
            for ($si=0; $si<count($sentiments); $si++) {
                if ($si<$bi) {
                    $sentiments[$si] = $sentiments[$si]*0.5;
                } else if ($si>$bi) {
                    $sentiments[$si] = $sentiments[$si]*1.5;
                }
            }
        }

        return $sentiments;
    }

    public function _idioms_check($wordInContext, $valence)
    {
        $onezero = sprintf("%s %s", $wordInContext[2], $wordInContext[3]);

        $twoonezero = sprintf("%s %s %s", $wordInContext[1], $wordInContext[2], $wordInContext[3]);

        $twoone = sprintf("%s %s", $wordInContext[1], $wordInContext[2]);

        $threetwoone = sprintf("%s %s %s", $wordInContext[0], $wordInContext[1], $wordInContext[2]);

        $threetwo = sprintf("%s %s", $wordInContext[0], $wordInContext[1]);

        $zeroone = sprintf("%s %s", $wordInContext[3], $wordInContext[2]);

        $zeroonetwo = sprintf("%s %s %s", $wordInContext[3], $wordInContext[2], $wordInContext[1]);

        $sequences = [$onezero, $twoonezero, $twoone, $threetwoone, $threetwo];

        foreach ($sequences as $seq) {
            $key = strtolower($seq);
            if (array_key_exists($key, Config::SPECIAL_CASE_IDIOMS)) {
                $valence = Config::SPECIAL_CASE_IDIOMS[$key];
                break;
            }


            // check for booster/dampener bi-grams such as 'sort of' or 'kind of'
            if ($this->IsBoosterWord($threetwo) || $this->IsBoosterWord($twoone)) {
                $valence = $valence+Config::B_DECR;
            }
        }

        return $valence;
    }

    public function _never_check($wordInContext, $valance)
    {
        //If the sentiment word is preceded by never so/this we apply a modifier
        $neverModifier = 0;
        if ("never" == $wordInContext[0]) {
            $neverModifier = 1.25;
        } else if ("never" == $wordInContext[1]) {
            $neverModifier = 1.5;
        }
        if ("so" == $wordInContext[1] || "so"== $wordInContext[2] || "this" == $wordInContext[1] || "this" == $wordInContext[2]) {
            $valance *= $neverModifier;
        }

        //if any of the words in context are negated words apply negative scaler
        foreach ($wordInContext as $wordToCheck) {
            if ($this->IsNegated($wordToCheck)) {
                $valance *= Config::B_DECR;
            }
        }

        return $valance;
    }

    public function _sentiment_laden_idioms_check($valence, $senti_text_lower){
        # Future Work
        # check for sentiment laden idioms that don't contain a lexicon word
        $idioms_valences = [];
        foreach (Config::SENTIMENT_LADEN_IDIOMS as $idiom) {
             if(in_array($idiom, $senti_text_lower)){
                //print($idiom, $senti_text_lower)
                $valence = Config::SENTIMENT_LADEN_IDIOMS[$idiom];
                $idioms_valences[] = $valence;
            }
        }

        if ((strlen($idioms_valences) > 0)) {
            $valence = ( array_sum( explode( ',', $idioms_valences ) ) / floatval(strlen($idioms_valences)));
        }
        return $valence;
    }

    public function _punctuation_emphasis($sum_s, $text)
    {
        // add emphasis from exclamation points and question marks
        $ep_amplifier = $this->_amplify_ep($text);
        $qm_amplifier = $this->_amplify_qm($text);
        $punct_emph_amplifier = $ep_amplifier+$qm_amplifier;

        return $punct_emph_amplifier;
    }

    public function _amplify_ep($text)
    {
        // check for added emphasis resulting from exclamation points (up to 4 of them)
        $ep_count = substr_count($text, "!");
        if ($ep_count > 4) {
            $ep_count = 4;
        }
        # (empirically derived mean sentiment intensity rating increase for
        # exclamation points)
        $ep_amplifier = $ep_count*0.292;

        return $ep_amplifier;
    }

    public function _amplify_qm($text)
    {
        # check for added emphasis resulting from question marks (2 or 3+)
        $qm_count = substr_count($text, "?");
        $qm_amplifier = 0;
        if ($qm_count > 1) {
            if ($qm_count <= 3) {
                # (empirically derived mean sentiment intensity rating increase for
                # question marks)
                $qm_amplifier = $qm_count*0.18;
            } else {
                $qm_amplifier = 0.96;
            }
        }

        return $qm_amplifier;
    }

    public function _sift_sentiment_scores($sentiments)
    {
        # want separate positive versus negative sentiment scores
        $pos_sum = 0.0;
        $neg_sum = 0.0;
        $neu_count = 0;
        foreach ($sentiments as $sentiment_score) {
            if ($sentiment_score > 0) {
                $pos_sum += $sentiment_score +1; # compensates for neutral words that are counted as 1
            }
            if ($sentiment_score < 0) {
                $neg_sum += $sentiment_score -1; # when used with math.fabs(), compensates for neutrals
            }
            if ($sentiment_score == 0) {
                $neu_count += 1;
            }
        }

        return [$pos_sum, $neg_sum, $neu_count];
    }

    public function score_valence($sentiments, $text)
    {
        if ($sentiments) {
            $sum_s = array_sum($sentiments);
            # compute and add emphasis from punctuation in text
            $punct_emph_amplifier = $this->_punctuation_emphasis($sum_s, $text);
            if ($sum_s > 0) {
                $sum_s += $punct_emph_amplifier;
            } elseif ($sum_s < 0) {
                $sum_s -= $punct_emph_amplifier;
            }

            $compound = Config::normalize($sum_s);
            # discriminate between positive, negative and neutral sentiment scores
            list($pos_sum, $neg_sum, $neu_count) = $this->_sift_sentiment_scores($sentiments);

            if ($pos_sum > abs($neg_sum)) {
                $pos_sum += $punct_emph_amplifier;
            } elseif ($pos_sum < abs($neg_sum)) {
                $neg_sum -= $punct_emph_amplifier;
            }

            $total = $pos_sum + abs($neg_sum) + $neu_count;
            $pos =abs($pos_sum / $total);
            $neg = abs($neg_sum / $total);
            $neu = abs($neu_count / $total);
        } else {
            $compound = 0.0;
            $pos = 0.0;
            $neg = 0.0;
            $neu = 0.0;
        }

        $sentiment_dict =
            ["neg" => round($neg, 2),
             "neu" => round($neu, 2),
             "pos" => round($pos, 2),
             "compound" => round($compound, 2)];

        return $sentiment_dict;
    }
}



$analyzer = new Analyzer(); 

$output = $analyzer->getSentiment($comment);
$pos = $output["pos"]*100;
$neu = $output["neu"]*100;
$neg = $output["neg"]*100;
$compound = $output["compound"]*100;


$sql= "INSERT INTO review(comment,rating,property_id,positive,neutral,negative,compound) VALUES('$comment','$rating','$property_id','$pos','$neu','$neg','$compound')";
$query=mysqli_query($db,$sql);
if(!empty($query)){
	?>

<style>
.alert {
  padding: 20px;
  background-color: #DC143C;
  color: white;
}

.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
<script>
	window.setTimeout(function() {
    $(".alert").fadeTo(1000, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>
<div class="container">
<div class="alert">
  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
  <strong>Your review has been recorded.</strong>
</div></div>


<?php
}

}

 ?>