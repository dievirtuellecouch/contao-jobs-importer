<?php

namespace DVC\JobsImporterToPlentaBasic\Utility;

use HTMLPurifier_Config;
use HTMLPurifier;

class TextCleaner
{
    public static function cleanHtml(string $html): string
    {
        $purifyConfig = HTMLPurifier_Config::createDefault();
        $purifyConfig->set('HTML.Allowed', 'p,ul,ol,li,em,a,h2,h3,h4,h5,h6');

        $purifier = new HTMLPurifier($purifyConfig);
        $html = $purifier->purify($html);

        $html = \preg_replace('/\s+/mu', ' ', $html);
        $html = \preg_replace('/\s*\&nbsp;/mu', ' ', $html);
        $html = \preg_replace('/\&nbsp;\s*/mu', ' ', $html);
        $html = \preg_replace('/(<br>|<br\s*\/>)+/mu', '', $html);
        $html = \preg_replace('/(<p>\s+<\/p>)+/mu', '', $html);

        return $html;
    }
}
