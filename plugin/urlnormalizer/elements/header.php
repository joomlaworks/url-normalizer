<?php
/**
 * @version    1.10
 * @package    URL Normalizer (plugin)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2021 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/base.php';

class JWElementHeader extends JWElement
{
    public function fetchElement($name, $value, &$node, $control_name)
    {
        $plg_name = "urlnormalizer";
        if (version_compare(JVERSION, '2.5.0', 'ge')) {
            $pluginLivePath = JURI::root(true).'/plugins/system/'.$plg_name.'/'.$plg_name;
        } else {
            $pluginLivePath = JURI::root(true).'/plugins/system/'.$plg_name;
        }
        $document = JFactory::getDocument();
        $document->addStyleSheet($pluginLivePath.'/elements/header.css?v=1.10');

        $cssClass = '';
        if (version_compare(JVERSION, '1.6.0', 'lt')) {
            $cssClass = '15';
        }
        return '<div class="jwHeaderContainer'.$cssClass.'"><div class="jwHeaderContent">'.JText::_($value).'</div><div class="jwHeaderClr"></div></div>';
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
