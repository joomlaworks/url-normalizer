<?php
/**
 * @version    1.4
 * @package    URL Normalizer (plugin)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2019 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).'/base.php');

class JWElementHeader extends JWElement
{
    public function fetchElement($name, $value, &$node, $control_name)
    {
        $document = JFactory::getDocument();

        if (version_compare(JVERSION, '1.6.0', 'ge')) {
            $document->addStyleSheet(JURI::root(true).'/plugins/system/urlnormalizer/urlnormalizer/elements/header.css?v=1.4');
            return '<div class="jwHeaderContainer"><div class="jwHeaderContent">'.JText::_($value).'</div><div class="jwHeaderClr"></div></div>';
        } else {
            $document->addStyleSheet(JURI::root(true).'/plugins/system/urlnormalizer/elements/header.css?v=1.4');
            return '<div class="jwHeaderContainer15"><div class="jwHeaderContent">'.JText::_($value).'</div><div class="jwHeaderClr"></div></div>';
        }
    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name)
    {
        return null;
    }
}

class JFormFieldHeader extends JWElementHeader
{
    public $type = 'header';
}

class JElementHeader extends JWElementHeader
{
    public $_name = 'header';
}
