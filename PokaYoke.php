<?php
/**
 * @category   DrSlump
 * @package    Zend_View
 * @copyright  Copyright (c) 2010 Iván -DrSlump- Montes <drslump@pollinimini.net>
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * Abstract master class for extension.
 */
require_once 'Zend/View.php';

/**
 * Utility class to escape template data
 */
require_once 'DrSlump/Zend/View/PokaYoke/Proxy.php';

/**
 * Poka Yoke style template engine
 *
 * @category   DrSlump
 * @package    Zend_View
 * @copyright  Copyright (c) 2010 Iván -DrSlump- Montes <drslump@pollinimini.net>
 * @license    New BSD License
 */
class DrSlump_Zend_View_PokaYoke extends Zend_View
{
    protected $_pokaVarName = 'poka';

    /**
     * Gets the name used for the poka yoke variable in the templates
     * 
     * @return string
     */
    public function getPokaYokeName()
    {
        return $this->_pokaVarName;
    }

    /**
     * Sets a new name for the poka yoke variable in the templates
     *
     * @param string $varName
     */
    public function setPokaYokeName($varName)
    {
        $this->_pokaVarName = $varName;
    }

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        $varname = $this->_pokaVarName;

        // Create a local variable holding the escaped template data
        $$varname = new Tid_Zend_View_PokaYoke_Proxy($this, $this);

        if ($this->_useViewStream && $this->useStreamWrapper()) {
            include 'zend.view://' . func_get_arg(0);
        } else {
            include func_get_arg(0);
        }
    }
}
