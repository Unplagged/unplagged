<?php

class Templates {
    
    public static function getBarcode($svg) {
        $html = file_get_contents("barcode.html", true);
        $html = self::replace($html, "{SVG}", $svg);
        return $html;
    }
    
    public static function getBibliography($sources, $footer) {
        $html = file_get_contents("bibliography.html", true);
        $html = self::replace($html, "{SOURCES}", $sources);
        $html = self::replace($html, "{FOOTER}", $footer);
        return $html;
    }
    
    public static function getFooter($page) {
        $html = file_get_contents("footer.html", true);
        $html = self::replace($html, "{PAGE_NUMBER}", $page);
        return $html;
    }
   
    public static function getIntro1($footer) {
        $html = file_get_contents("intro1.html", true);
        $html = self::replace($html, "{FOOTER}", $footer);
        return $html;
    }
   
    public static function getIntro2($footer) {
        $html = file_get_contents("intro2.html", true);
        $html = self::replace($html, "{FOOTER}", $footer);
        return $html;
    }
    
    public static function getPage($title1, $title2, $td1, $td2, $footer) {
        $html = file_get_contents("page.html", true);
        $html = self::replace($html, "{TITLE1}", $title1);
        $html = self::replace($html, "{TITLE2}", $title2);
        $html = self::replace($html, "{TD1}", $td1);
        $html = self::replace($html, "{TD2}", $td2);
        $html = self::replace($html, "{FOOTER}", $footer);
        return $html;
    }
    
    public static function getSource($author, $year, $title, $journal, $address, $publisher) {
        $html = file_get_contents("source.html", true);
        $html = self::replace($html,"{AUTHOR}", $author);
        $html = self::replace($html,"{YEAR}", $year);
        $html = self::replace($html,"{TITLE}", $title);
        $html = self::replace($html,"{JOURNAL}", $journal);
        $html = self::replace($html,"{ADDRESS}", $address);
        $html = self::replace($html,"{PUBLISHER}", $publisher);
        return $html;
    }
    
    private static function replace($str, $tag, $value) {
        $str = str_replace($tag, $value, $str);
        return $str;
    }
}
?>
