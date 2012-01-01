<?php
/**
 * This example show the normal usage of the TeXHyphen package on an 
 * english sample text.
 *
 * @author Stefan Ohrmann <bshell@gmx.net>
 * @version $Id$
 * @package Text_TeXHyphen
 */

header("Content-Type: text/plain");

require_once 'Text/TeXHyphen.php';
require_once 'Text/TeXHyphen/PatternDB.php';
require_once 'Text/TeXHyphen/WordCache.php';


/* Creating an pattern source by loading an pattern file an */

// Loading american pattern
$patternFile = 'data/Text_TeXHyphen/en-US_pattern.txt';
$patternArr = file($patternFile, 1);

// Removing header line with source information
array_shift($patternArr);

// Setting options for the objecthash
$options = array('mode' => 'build', 'data' => &$patternArr, 'onlyKeys' => true);

$patternDB =& Text_TeXHyphen_PatternDB::factory('objecthash', $options);

if (false === $patternDB) {
    $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
    $e =& $eS->pop();
    die ('PatternDB: '.(PEAR_ErrorStack::getErrorMessage($eS, $e)));
}


/* Creating an cache for hyphenated words */

// Loading exceptions
$exceptionsFile = 'data/Text_TeXHyphen/en-US_exceptions.txt';
$exceptionsArr = file($exceptionsFile, 1);

// Removing header line with source information
array_shift($exceptionsArr);

$wordCache =& Text_TeXHyphen_WordCache::factory('simplehash');

if (false === $wordCache) {
    $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
    $e =& $eS->pop();
    die ('WordCache: '.(PEAR_ErrorStack::getErrorMessage($eS, $e)));
}

// Adding exceptions to word cache
foreach ($exceptionsArr as $hyphWord) {
    $hyphWord = trim($hyphWord);
    $syls = explode("-", $hyphWord);
    $wordCache->add(implode($syls), $syls);
}


/* Creating the TeXHyphen */

$hyphen =& Text_TeXHyphen::factory($patternDB, array('wordcache' => &$wordCache));

if (false === $hyphen) {
    $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
    $e =& $eS->pop();
    die ('TeXHyphen: '.(PEAR_ErrorStack::getErrorMessage($eS, $e)));
}


// Hyphenating a text.

$sampleText = 'en_sample.txt';
$lines = file($sampleText, 1);
//$lines = array_splice($lines, 41, 1);
$colWidth = 42;
$hyphenChar = '-';
foreach ($lines as $line) {
    if ($colWidth > strlen($line)) {
        $hyphLines[] = $line;
        continue;
    }
    
    $words = explode(" ", $line);
    $hyphLine = '';    
    while (!is_null($word = array_shift($words))) {
        if ($colWidth > strlen($hyphLine.$word)) {
            $hyphLine .= $word;
            $hyphLine .= ' ';
        } else {
            $syls = $hyphen->getSyllables($word);
            $part = '';
            while (!is_null($syl = array_shift($syls))) {
                if ($colWidth > strlen($hyphLine.$part.$syl.$hyphenChar)) {
                    $part .= $syl;
                } elseif (0 != strlen($part)) {
                    $hyphLine .= $part;
                    $hyphLine .= '-';
                    $word = $syl.(implode("", $syls));
                    break;
                }
            }
            array_unshift($words, $word);
            $hyphLines[] = $hyphLine."\n";
            $hyphLine = '';
        }
    }
    $hyphLines[] = $hyphLine;
}

$cols = 3;
$lineCnt = (int)ceil(count($hyphLines)/$cols);
$colSpace = str_repeat(' ', 5);

for ($i = 0; $i < $lineCnt; $i++) {
    for ($j = 0; $j < $cols; $j++) {
        if (isset($hyphLines[$i+($j*$lineCnt)])) {
            echo str_pad(trim($hyphLines[$i+($j*$lineCnt)]), $colWidth);
            if ($j < $cols - 1) {
                echo $colSpace;
            }
        }
    }
    echo "\n";
}
?>