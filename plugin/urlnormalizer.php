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

if (version_compare(JVERSION, '1.6.0', 'lt')) {
    jimport('joomla.plugin.plugin');
    jimport('joomla.environment.response');
}

class PlgSystemUrlnormalizer extends JPlugin
{
    public function onAfterInitialise()
    {
        // API
        $app = JFactory::getApplication();
        $document = JFactory::getDocument();

        // Prevent unwanted execution
        if ($app->isAdmin() || JDEBUG) {
            return;
        }

        // Define desktop or mobile device by checking for ?m & ?force in the URL
        if (isset($_GET['m']) && !isset($_GET['force'])) {
            define("SITE_VIEW", "mobile");
        } else {
            define("SITE_VIEW", "desktop");
        }

        // Params
        $targetDomain = $this->params->get('cdnDomain');
        $jsRedirectProtocol = $this->params->get('redirectWithJS', 0);
        $jsRedirectDomain = $this->params->get('jsRedirectDomain', 0);

        // Force redirect protocol
        if ($jsRedirectProtocol) {
            if ($jsRedirectProtocol=='https') {
                $document->addScriptDeclaration('
                /* URL Normalizer: Force redirect to HTTPS */
                if(!(window.location.host.startsWith("127.0.0.1") || window.location.host.startsWith("localhost")) && (window.location.protocol != "https:")) window.location.protocol = "https:";
                ');
            } else {
                $document->addScriptDeclaration('
                /* URL Normalizer: Force redirect to HTTP */
                if(!(window.location.host.startsWith("127.0.0.1") || window.location.host.startsWith("localhost")) && (window.location.protocol != "http:")) window.location.protocol = "http:";
                ');
            }
        }

        // Force redirect to target domain
        if ($jsRedirectDomain && $targetDomain) {
            $targetDomainWithNoProtocol = explode('://', $targetDomain);
            $targetDomainWithNoProtocol = $targetDomainWithNoProtocol[1];
            $document->addScriptDeclaration('
                /* URL Normalizer: Redirect to target domain */
                if(window.location.host != "'.$targetDomainWithNoProtocol.'") window.location.host = "'.$targetDomainWithNoProtocol.'";
            ');
        }
    }

    public function onAfterRender()
    {
        // API
        $app = JFactory::getApplication();

        // Prevent unwanted execution
        if ($app->isAdmin() || JDEBUG) {
            return;
        }

        // Process the output
        $buffer = JResponse::getBody();
        $buffer = $this->replacements($buffer);
        JResponse::setBody($buffer);
    }

    public function replacements($buffer)
    {
        // API
        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        // Requests
        $option = JRequest::getCmd('option');

        // Params
        $originDomains = @explode(PHP_EOL, $this->params->get('originDomain'));
        $originDomains = array_map('trim', $originDomains);
        $targetDomain = $this->params->get('cdnDomain');
        $clientSideCaching = $this->params->get('browsercache', false);
        $cacheTTLHomePage = $this->params->get('cachetime', 15) * 60; // In seconds
        $cacheTTLInnerPages = $this->params->get('cachetime_inner', $cacheTTLHomePage) * 60; // In seconds
        $excludedComponents = @explode(PHP_EOL, $this->params->get('excludedComponents'));
        $excludedComponents = array_map('trim', $excludedComponents);

        // Tidy
        $tidyState      = $this->params->get('tidyState', 0);
        $indent         = $this->params->get('indent', 1);
        $wrap           = (int) $this->params->get('wrap', 0);
        $altText        = $this->params->get('altText', 'Image');
        $hideComments   = $this->params->get('hideComments', 0);
        $cdataIndent    = $this->params->get('cdataIndent', 0);
        $breakBeforeBr  = $this->params->get('breakBeforeBr', 1);
        $tagsNotToStrip = $this->params->get('tagsNotToStrip', '');
        if ($tagsNotToStrip) {
            $tagsNotToStrip = ', '.$tagsNotToStrip;
        }

        // Process Tidy
        if (class_exists('tidy') && $tidyState && !JRequest::getCmd('notidy') && (JRequest::getCmd('format')=='html' || JRequest::getCmd('format')=='')) {

            // Tidy Configuration Options
            $tidyConfig = array(
                'output-xhtml'                  => true,
                'doctype'                       => 'transitional',
                'indent'                        => $indent,
                'indent-spaces'                 => 4,
                'wrap'                          => $wrap,
                'alt-text'                      => $altText,
                'hide-comments'                 => $hideComments,
                'indent-cdata'                  => $cdataIndent,
                'break-before-br'               => $breakBeforeBr,
                'clean'                         => 1,
                'merge-divs'                    => 0,
                'merge-spans'                   => 0,
                'new-empty-tags'                => 'a,b,li,strong,span',
                'new-blocklevel-tags'           => 'fb:like, fb:send, fb:comments, fb:activity, fb:recommendations, fb:like-box, fb:login-button, fb:facepile, fb:live-stream, fb:fan, fb:pile, g:plusone, article, aside, bdi, command, details, summary, figure, figcaption, footer, header, hgroup, mark, meter, nav, progress, ruby, rt, rp, section, time, wbr, audio, video, source, embed, track, canvas, datalist, keygen, output'.$tagsNotToStrip,
                'drop-proprietary-attributes'   => false
            );

            $tidy = new tidy;
            $tidy->parseString($buffer, $tidyConfig, 'utf8');
            $tidy->cleanRepair();

            $tidy = $tidy."\n<!-- URL Normalizer (by JoomlaWorks): HTML Tidy engine enabled -->";

            // HTML5 Mode
            $tidy = preg_replace("#<!DOCTYPE(.+?)>#s", "<!doctype html>", $tidy);
            $tidy = preg_replace("# xmlns=\"(.+?)\"#s", "", $tidy);
            $tidy = preg_replace("# (xml|xmlns)\:(.+?)=\"(.+?)\"#s", "", $tidy);

            $htmlFind = array(
                '<meta http-equiv="content-type" content="text/html; charset=utf-8" />',
                ' type=\'text/javascript\'',
                ' type="text/javascript"',
                ' type=\'text/css\'',
                ' type="text/css"',
                ' language=\'Javascript\'',
                ' language="Javascript"',
                '//<![CDATA[',
                '//]]>'
            );
            $htmlReplace = array(
                '<meta charset="utf-8" />',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            );

            $tidy = str_ireplace($htmlFind, $htmlReplace, $tidy);

            $buffer = $tidy;
        }

        // URL Normalizations
        if ($targetDomain) {
            foreach ($originDomains as $originDomain) {
                $originReplacements[] = 'http://'.$originDomain;
                $originReplacements[] = 'https://'.$originDomain;
                $targetReplacements[] = $targetDomain;
                $targetReplacements[] = $targetDomain;
            }

            // Common replacements
            $originReplacements[] = 'http://youtu.be';
            $originReplacements[] = 'http://youtube.com';
            $originReplacements[] = 'http://www.youtube.com';
            $originReplacements[] = 'http://vimeo.com';
            $originReplacements[] = 'http://www.vimeo.com';

            $targetReplacements[] = 'https://youtu.be';
            $targetReplacements[] = 'https://www.youtube.com';
            $targetReplacements[] = 'https://www.youtube.com';
            $targetReplacements[] = 'https://vimeo.com';
            $targetReplacements[] = 'https://vimeo.com';

            $buffer = str_ireplace($originReplacements, $targetReplacements, $buffer);
        }

        // Required to override HTTP headers
        JResponse::allowCache(true);

        // Client-side caching
        if ($clientSideCaching) {
            // Frontpage Check
            $menu = $app->getMenu();
            if ($menu->getActive() == $menu->getDefault()) {
                $frontpage = true;
            } else {
                $frontpage = false;
            }

            if ($frontpage) {
                $cacheTTL = $cacheTTLHomePage;
            } else {
                $cacheTTL = $cacheTTLInnerPages;
            }

            // Set client-side caching headers for guest users
            if ($user->guest) {
                if (in_array($option, $excludedComponents)) {
                    JResponse::setHeader('Cache-Control', 'private, max-age=0, no-cache, no-store', true);
                    JResponse::setHeader('Pragma', 'no-cache', true);
                    JResponse::setHeader('Expires', 'Mon, 01 Jan 2001 00:00:00 GMT', true);
                } else {
                    JResponse::setHeader('Cache-Control', 'public, max-age='.$cacheTTL.', stale-while-revalidate='.($cacheTTL*2).', stale-if-error='.($cacheTTL*5), true);
                    JResponse::setHeader('Pragma', 'public', true);
                    JResponse::setHeader('Expires', gmdate('D, d M Y H:i:s', time()+$cacheTTL).' GMT', true);
                }
            } else {
                JResponse::setHeader('Cache-Control', 'private, max-age=0, no-cache, no-store', true);
                JResponse::setHeader('Pragma', 'no-cache', true);
                JResponse::setHeader('Expires', 'Mon, 01 Jan 2001 00:00:00 GMT', true);
            }
        }

        // Common HTTP headers
        if ($user->guest) {
            JResponse::setHeader('X-Logged-In', 'False', true);
        } else {
            JResponse::setHeader('X-Logged-In', 'True', true);
        }
        JResponse::setHeader('X-Powered-By', 'URL Normalizer v1.5 (by JoomlaWorks) - https://www.joomlaworks.net', true);

        // Mark the output
        if (JRequest::getCmd('format') == '' || JRequest::getCmd('format') == 'html' || JRequest::getCmd('format') == 'raw') {
            $buffer .= "\n<!-- URL Normalizer (by JoomlaWorks): Executed onAfterRender -->\n";
        }
        return $buffer;
    }
} // Class end