<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Text :: TeXHyphen                                              |
// +------------------------------------------------------------------------+
// | Copyright (c) 2004 Stefan Ohrmann <bshell@gmx.net>.                    |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
// $Id$
//

/**
 * Implementation of the Text_TeXHyphen_WordCache
 * @package Text_TeXHyphen
 */


/**
 * The abstract of the word cache class for the TeX hyphenation
 * algorithm.
 *
 * @author Stefan Ohrmann <bshell@gmx.net>
 * @version $Id$
 * @package Text_TeXHyphen
 */
class Text_TeXHyphen_WordCache
{

    /**
     * Factory for creating a word.
     *
     * @param string $type Name of the word cache implementation.
     * @param array $options Options for word cache implementation.
     *
     * @return Text_TeXHyphen_WordCache|false Reference to an object
     * of type Text_TeXHyphen_WordCache or a subclass of it, if successful or
     * false on error.
     *
     * @access public
     */
    function factory($type, $options = array())
    {
        $type = strtolower($type);

        @include_once 'Text/TeXHyphen/WordCache/'.$type.'.php';

        $classname = 'Text_TeXHyphen_WordCache_'.$type;

        if (!class_exists($classname)) {
            throw new InvalidArgumentException("Unable to build " $classname);
        }

        $obj = call_user_func_array(array($classname,'factory'), array($type, $options));

        return $obj;
    } // end of function factory

    /**
     * Gets the syllables of a word, if found in cache.
     *
     * @param string $word Word of which the syllables should got.
     *
     * @return array|false Array of string or false, if $word isn't
     * found.
     *
     * @access public
     */
    function getSyllables($word)
    {
        return false;
    } // end of function lookUp

    /**
     * Adds a word and its syllables to the cache.
     *
     * @param string $word Word, which syllables should stored.
     * @param array $syls Array of strings, which contains of the
     * syllables of the $word.
     *
     * @return boolean true, if the $word could added to the cache
     * otherwise false.
     */
    function add($word, $syls)
    {
        return true;
    } // end of function add

}
