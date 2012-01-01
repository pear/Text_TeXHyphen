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
 * Implementation of the Text_TeXHyphen_PatternDB
 * @package Text_TeXHyphen
 */

/**
 * The abstract of the pattern database class for the TeX hyphenation
 * algorithm.
 *
 * @author Stefan Ohrmann <bshell@gmx.net>
 * @version $Id$
 * @package Text_TeXHyphen
 */
class Text_TeXHyphen_PatternDB
{
    /**
     * The validator which validates the TeX pattern strings.
     *
     * @see Text_TeXHyphen_PatternValidator
     *
     * @var Text_TeXHyphen_PatternValidator Reference to a
     * Text_TeXHyphen_PatternValidator object.
     *
     * @access private
     */
    var $_validator = null;

    /**
     * Factory for creating a pattern database.
     *
     * @param string $type Name of the pattern database implementation.
     * @param array $options Options for pattern database implementation.
     *
     * @return Text_TeXHyphen_PatternDB|false Reference to an object
     * of type Text_TeXHyphen_PatternDB or a subclass of it, if successful or
     * false on error.
     *
     * @access public
     */
    public static function factory($type, $options = array())
    {
        $type = strtolower($type);

        @include_once 'Text/TeXHyphen/PatternDB/'.$type.'.php';

        $classname = 'Text_TeXHyphen_PatternDB_'.$type;

        if (!class_exists($classname)) {
            throw new InvalidArgumentException("Could not build " . $classname);
        }

        $obj = call_user_func_array(array($classname,'factory'), array($type, $options));

        return $obj;
    }

    /**
     * Gets the Text_TeXHyphen_Pattern object specified by the $key,
     * if it exists in the pattern database.
     *
     * @see Text_TeXHyphen_Pattern
     *
     * @param string $key Key by which the pattern should be
     * identified.
     *
     * @return Text_TeXHyphen_Pattern|false Reference to a
     * Text_TeXHyphen_Pattern object if successful or false
     * if the pattern isn't found.
     *
     * @access public
     */
    function getPattern($key)
    {
        return false;
    } // end of function getPattern

}
