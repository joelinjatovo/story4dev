<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('html', [$this, 'formatHtml']),
            new TwigFilter('excerpt', [$this, 'formatExcerpt']),
        ];
    }

    public function formatExcerpt($text, $length = 200, $more = '...'){
        $text = strip_tags($text);
        if( strlen($text) > $length ){
            return mb_substr($text, 0, $length).$more;
        }
        
        return $text;
    }
    
    public function formatHtml($html)
    {
        $html = trim($html);
        
        if($html){
            // Unsafe HTML tags that members may abuse
            $unsafe=array(
                '/<iframe(.*?)<\/iframe>/is',
                '/<title(.*?)<\/title>/is',
                '/<pre(.*?)<\/pre>/is',
                '/<frame(.*?)<\/frame>/is',
                '/<frameset(.*?)<\/frameset>/is',
                '/<object(.*?)<\/object>/is',
                '/<script(.*?)<\/script>/is',
                '/<embed(.*?)<\/embed>/is',
                '/<applet(.*?)<\/applet>/is',
                '/<meta(.*?)>/is',
                '/<!doctype(.*?)>/is',
                '/<link(.*?)>/is',
                '/<body(.*?)>/is',
                '/<\/body>/is',
                '/<head(.*?)>/is',
                '/<\/head>/is',
                '/onclick="(.*?)"/is',
                '/onload="(.*?)"/is',
                '/onunload="(.*?)"/is',
                '/<html(.*?)>/is',
                '/<\/html>/is'
            );

            // Remove these tags and all parameters within them
            $html = preg_replace($unsafe, "", $html);
        }
        
        return $html;
    }
}