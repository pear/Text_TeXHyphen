<?php
/**
 * This is an online example of the TeXHyphen package.
 *
 * @author Stefan Ohrmann <bshell@gmx.net>
 * @version $Id$
 * @package Text_TeXHyphen
 */
error_reporting(E_ALL);
// An path redirection for my hoster.
//ini_set('include_path', '.:'.$_SERVER['DOCUMENT_ROOT'].'/pear/');

require_once 'HTML/Page.php';
require_once 'HTML/QuickForm.php';

$page = new HTML_Page(array('charset'=>'ISO-8859-1'));
$page->setTitle('TexHyphen example');
$page->setMetaData('author', 'Stefan Ohrmann <bshell@gmx.net>');
$page->setDoctype('XHTML 1.0 Strict');

// Instantiate the HTML_QuickForm object
$form = new HTML_QuickForm('TeXHyphenForm');

// Add some elements to the form
$form->addElement('header', null, 'TeXHyphen example');
$form->addElement('text', 'word', 'Enter a word', array('size' => 50, 'maxlength' => 255));
$form->addElement('select', 'lang', 'Select a language', array('en-US' => 'en-US', 'en-GB' => 'en-GB', 'de' => 'de'));
$sel =& $form->getElement('lang');
$sel->setSelected('en-US');
$form->addElement('submit', null, 'Hyphenate');

// Define filters and validation rules
$form->applyFilter('word', 'trim');
$form->addRule('word', 'Please enter a word', 'required');
if (0 === strcasecmp('de', $form->exportValue('lang'))) {
    $form->addRule('word', 'Please enter a word without ".", "-", "_", "," and whitspaces', 'regex', '/^[a-zäüößA-ZÄÜÖ]+$/');
} else {
    $form->addRule('word', 'Please enter a word without ".", "-", "_", "," and whitspaces', 'lettersonly');
}

// Try to validate a form 
if (!$form->validate()) {
    // Output the form
    $page->addBodyContent($form->toHtml());
    $page->display();
    exit;
}

// Hyphenates the word
//require_once '../../../Text/TeXHyphen.php';
require_once 'Text/TeXHyphen.php';
require_once 'Text/TeXHyphen/PatternDB.php';
require_once 'Text/TeXHyphen/WordCache.php';

/* Creating an pattern source by loading an pattern file an */

// Loading american pattern
$patternFile = 'data/Text_TeXHyphen/'.$form->exportValue('lang').'_pattern.txt';
$patternArr = file($patternFile, 1);
if (false === $patternArr) {
    $page->addBodyContent('<p style="color:red;"><strong>Error:</strong> Pattern file '.$patternFile.' couldn\'t be found</p>');
    $page->display();
    exit;
}


// Removing header line with source information
array_shift($patternArr);

// Setting options for the objecthash
$options = array('mode' => 'build', 'data' => &$patternArr, 'onlyKeys' => true);

$patternDB =& Text_TeXHyphen_PatternDB::factory('objecthash', $options);

if (false === $patternDB) {
    $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
    $e =& $eS->pop();
    $page->addBodyContent('<p style="color:red;"><strong>Error PatternDB:</strong> '.(PEAR_ErrorStack::getErrorMessage($eS, $e)).'</p>');
    $page->display();
    exit;
}

$options = array();

/* Creating an cache for hyphenated words */

// Loading exceptions
$exceptionsFile = 'data/Text_TeXHyphen/'.$form->exportValue('lang').'_exceptions.txt';
if (file_exists($exceptionsFile)) {
    $exceptionsArr = file($exceptionsFile, 1);

    // Removing header line with source information
    array_shift($exceptionsArr);

    $wordCache =& Text_TeXHyphen_WordCache::factory('simplehash');

    if (false === $wordCache) {
        $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
        $e =& $eS->pop();
        $page->addBodyContent('<p style="color:red;"><strong>Error WordCache:</strong> '.(PEAR_ErrorStack::getErrorMessage($eS, $e)).'</p>');
    }

    // Adding exceptions to word cache
    foreach ($exceptionsArr as $hyphWord) {
        $hyphWord = trim($hyphWord);
        $syls = explode("-", $hyphWord);
        $wordCache->add(implode($syls), $syls);
    }
    
    $options['wordcache'] =& $wordCache;
}


/* Creating the TeXHyphen */

$hyphen =& Text_TeXHyphen::factory($patternDB, $options);

if (false === $hyphen) {
    $eS =& PEAR_ErrorStack::singleton('Text_TeXHyphen');
    $e =& $eS->pop();
    $page->addBodyContent('<p style="color:red;"><strong>Error TeXHyphen:</strong> '.(PEAR_ErrorStack::getErrorMessage($eS, $e)).'</p>');
    $page->display();
    exit;   
}

$hyphenChar = '-';
$hword = implode($hyphenChar, $hyphen->getSyllables($form->exportValue('word')));
$page->addBodyContent('<p><strong>Original Word:</strong> '.$form->exportValue('word').'</p>');
$page->addBodyContent('<p><strong>Hyphenated Word:</strong> '.$hword.'</p>');
$page->addBodyContent('<p><a href="form.php">Next Try!</p>');
$page->display();
?>