<?
/*

  minimalnfc.php - Fast and limited Unicode normalizer written in pure PHP.

  Use utf8_normalize() instead of Normalizer::normalize when you do not need
  full Unicode normalization and prefer something tiny with less dependancies.

  The function operates directly on UTF-8 and replaces decomposed unicode
  characters found in regular latin text (basically ISO8859-1) with their
  precomposed counterpart using Normalization Form C.

  Written in 2014 by Johannes Lundberg.
  Released to the public domain.

*/

$latin_nfc = array(
  "A\xcc\x80" => "\xc3\x80",
  "A\xcc\x81" => "\xc3\x81",
  "A\xcc\x82" => "\xc3\x82",
  "A\xcc\x83" => "\xc3\x83",
  "A\xcc\x88" => "\xc3\x84",
  "A\xcc\x8a" => "\xc3\x85",
  "C\xcc\xa7" => "\xc3\x87",
  "E\xcc\x80" => "\xc3\x88",
  "E\xcc\x81" => "\xc3\x89",
  "E\xcc\x82" => "\xc3\x8a",
  "E\xcc\x88" => "\xc3\x8b",
  "I\xcc\x80" => "\xc3\x8c",
  "I\xcc\x81" => "\xc3\x8d",
  "I\xcc\x82" => "\xc3\x8e",
  "I\xcc\x88" => "\xc3\x8f",
  "N\xcc\x83" => "\xc3\x91",
  "O\xcc\x80" => "\xc3\x92",
  "O\xcc\x81" => "\xc3\x93",
  "O\xcc\x82" => "\xc3\x94",
  "O\xcc\x83" => "\xc3\x95",
  "O\xcc\x88" => "\xc3\x96",
  "U\xcc\x80" => "\xc3\x99",
  "U\xcc\x81" => "\xc3\x9a",
  "U\xcc\x82" => "\xc3\x9b",
  "U\xcc\x88" => "\xc3\x9c",
  "Y\xcc\x81" => "\xc3\x9d",
  "a\xcc\x80" => "\xc3\xa0",
  "a\xcc\x81" => "\xc3\xa1",
  "a\xcc\x82" => "\xc3\xa2",
  "a\xcc\x83" => "\xc3\xa3",
  "a\xcc\x88" => "\xc3\xa4",
  "a\xcc\x8a" => "\xc3\xa5",
  "c\xcc\xa7" => "\xc3\xa7",
  "e\xcc\x80" => "\xc3\xa8",
  "e\xcc\x81" => "\xc3\xa9",
  "e\xcc\x82" => "\xc3\xaa",
  "e\xcc\x88" => "\xc3\xab",
  "i\xcc\x80" => "\xc3\xac",
  "i\xcc\x81" => "\xc3\xad",
  "i\xcc\x82" => "\xc3\xae",
  "i\xcc\x88" => "\xc3\xaf",
  "n\xcc\x83" => "\xc3\xb1",
  "o\xcc\x80" => "\xc3\xb2",
  "o\xcc\x81" => "\xc3\xb3",
  "o\xcc\x82" => "\xc3\xb4",
  "o\xcc\x83" => "\xc3\xb5",
  "o\xcc\x88" => "\xc3\xb6",
  "u\xcc\x80" => "\xc3\xb9",
  "u\xcc\x81" => "\xc3\xba",
  "u\xcc\x82" => "\xc3\xbb",
  "u\xcc\x88" => "\xc3\xbc",
  "y\xcc\x81" => "\xc3\xbd",
  "y\xcc\x88" => "\xc3\xbf"
);

function utf8_normalize ($s) {
  global $latin_nfc;
  
  $out = array();

  /* Every interesting character is at least 3 bytes long. */
  $lastpos = 0;
  for ($pos = 0; $pos < strlen($s)-2; $pos++) {
    if ($s[$pos+1] == "\xCC") {
      $letter = substr($s,$pos,3);
      if (isset($latin_nfc[$letter])) {
        $out[] = substr($s,$lastpos,$pos-$lastpos);
        $out[] = $latin_nfc[$letter];
        $pos += 2;
        $lastpos = $pos + 1;
      }
    }
  }
  $out[] = substr($s,$lastpos,$pos-$lastpos+2);

  return implode('',$out);
}

?>
