<?php
/**
 * @version    1.5
 * @package    URL Normalizer (plugin)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2019 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '2.5.0', 'ge')) {
    jimport('joomla.form.formfield');
    class JWElement extends JFormField
    {
        public function getInput()
        {
            return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
        }

        public function getLabel()
        {
            if (method_exists($this, 'fetchTooltip')) {
                return $this->fetchTooltip($this->element['label'], $this->description, $this->element, $this->options['control'], $this->element['name'] = '');
            } else {
                return parent::getLabel();
            }
        }

        public function render()
        {
            return $this->getInput();
        }
    }
} else {
    jimport('joomla.html.parameter.element');
    class JWElement extends JElement
    {
    }
}