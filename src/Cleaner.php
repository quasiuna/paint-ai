<?php

namespace quasiuna\paintai;

class Cleaner
{
    public static function removeCommentsFromJavaScript($js)
    {
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
        return preg_replace($pattern, '', $js);
    }
    
    public static function removeNewLinesFromString($string)
    {
        return preg_replace('/[\s\n\r]+/', ' ', $string);
    }
    
    public static function removeWhitespace($string)
    {
        return preg_replace('/[\s\n\r]+/', '', $string);
    }
}
