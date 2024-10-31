<?php
    /**
    * This is the default stemmer
	* Only used when there is no stemmer for the current language
    */

    class Picosearch_Stemmer {
        /**
        * Stems a word. No OP
        *
        * @param  string $word Word to stem
        * @return string $word
        */
        public static function Stem($word) {
			return $word;
		}
    }
?>
