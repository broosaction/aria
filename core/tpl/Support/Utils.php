<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 7:31
 */

namespace Core\tpl\Support;


class Utils
{

    public static function minifyHTML($input)
    {
        if (trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if (strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . self::minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        if (strpos($input, '</style>') !== false) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
                return '<style' . $matches[1] . '>' . self::minify_css($matches[2]) . '</style>';
            }, $input);
        }
        if (strpos($input, '</script>') !== false) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
                return '<script' . $matches[1] . '>' . self::minify_js($matches[2]) . '</script>';
            }, $input);
        }

        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
            $input);
    }

    // CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
    public static function minify_css($input)
    {
        if (trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
            $input);
    }

// JavaScript Minifier
    public static function minify_js($input)
    {
        if (trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
            $input);
    }

    public static function escapeCharsInString($str, $escapeChar, $repChar, $strDelimiter = '"')
    {

        $ret = "";
        $inQuote = false;
        $escaped = false;
        for ($i = 0, $iMax = strlen($str); $i <= $iMax; $i++) {
            $char = substr($str, $i, 1);
            switch ($char) {
                case '\\':
                    $escaped = true;
                    $ret .= $char;
                    break;
                case $strDelimiter:
                    if (!$escaped) {
                        $inQuote = !$inQuote;
                    }
                    $ret .= $char;
                    break;
                default:
                    if ($inQuote && $char === $escapeChar) {
                        $ret .= $repChar;
                    } else {
                        $ret .= $char;
                    }
            }
            if ($escaped) {
                $escaped = false;
            }
        }
        return $ret;
    }

    public static function str_replace_first($find, $replace, $string)
    {
        $pos = strpos($string, $find);
        if ($pos !== false) {
            return substr_replace($string, $replace, $pos, strlen($find));
        }
        return "";
    }

    public static function removeSpecialChars($text)
    {
        $find = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ', ' ', '"', "'"];
        $rep = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'n', 'N', '-', "", ""];
        return str_replace($find, $rep, $text);
    }

    public static function removeWhiteSpaces($str)
    {
        $in = false;
        $escaped = false;
        $ws_string = "";
        for ($i = 0; $i <= strlen($str) - 1; $i++) {
            $char = substr($str, $i, 1);
            $je = false;
            $continue = false;
            switch ($char) {
                case '\\':
                    $je = true;
                    $escaped = true;
                    break;
                case '"':
                    if (!$escaped) {
                        $in = !$in;
                    }
                    break;
                case " ":
                    if (!$in) {
                        $continue = true;
                    }
                    break;
            }
            if (!$je) {
                $escaped = false;
            }
            if (!$continue) {
                $ws_string .= $char;
            }
        }
        return $ws_string;
    }

    public static function zeroFill($text, $digits)
    {
        $ret = '';
        if (strlen($text) < $digits) {
            $ceros = $digits - strlen($text);
            for ($i = 0; $i <= $ceros - 1; $i++) {
                $ret .= "0";
            }
            $ret .= $text;
            return $ret;
        }

        return $text;
    }

    public static function matchTags($regex, $append = "", $content = '')
    {
        $matches = array();
        if (!preg_match_all($regex, $content, $matches)) {
            return false;
        }
        $offset = 0;
        $_offset = 0;
        $ret = array();
        foreach ($matches[0] as $k => $match) {
            $_cont = substr($content, $offset);
            $in_str = false;
            $escaped = false;
            $i = strpos($_cont, $match);
            $tag = $matches[1][$k];
            $len_match = strlen($match);
            $offset += $i + $len_match;
            $str_char = "";
            $lvl = 1;
            $prev_char = "";
            $prev_tag = "";
            $struct = "";
            $in_tag = false;
            $capturing_tag_name = false;
            $_m = array();
            foreach ($matches as $z => $v) {
                $_m[$z] = $matches[$z][$k];
            }
            $ret[$k] = array(
                'match' => $match,
                'matches' => $_m,
                'all' => $match,
                'inner' => "",
                'starts_at' => $offset - $len_match,
                'ends_at' => 0,
            );
            for ($j = $i + strlen($match), $jMax = strlen($_cont); $j <= $jMax; $j++) {
                $char = $_cont[$j];
                $prev_char = $char;
                $struct .= $char;
                $break = false;
                switch ($char) {
                    case "\\":
                        $escaped = true;
                        continue 2;
                        break;
                    case "'":
                    case '"':
                        if (!$escaped) {
                            if ($in_str && $char === $str_char) {
                                $str_char = $char;
                            }
                            $in_str = !$in_str;
                        }
                        break;
                    case '>':
                        if (!$in_str) {
                            if ($in_tag) {
                                $in_tag = false;
                                if ($prev_tag === '/' . $tag) {
                                    $lvl--;
                                    if ($lvl <= 0) {
                                        $break = true;
                                    }
                                } else if (strpos($prev_tag, '/') === 0) {
                                    $lvl--;
                                } else {
                                    if ($prev_char !== '/' && !in_array(str_replace('/', "", $prev_tag), array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'))) {
                                        $lvl++;
                                    }
                                }
                                if ($capturing_tag_name) {
                                    $capturing_tag_name = false;
                                }
                            }
                        }
                        break;
                    case '<':
                        if ($in_tag) {
                            continue 2;
                        }
                        if (!$in_str) {
                            $prev_tag = "";
                            $in_tag = true;
                            $capturing_tag_name = true;
                            continue 2;
                        }
                        break;
                    case ' ':
                        if ($capturing_tag_name) {
                            $capturing_tag_name = false;
                        }
                        break;
                    default:
                        if ($capturing_tag_name) {
                            $prev_tag .= $char;
                        }
                }
                if ($escaped) {
                    $escaped = false;
                }
                if ($break) {
                    break;
                }
            }
            $ret[$k]['all'] .= $struct;
            $struct_len = strlen($struct);
            $ret[$k]['inner'] = substr($struct, 0, $struct_len - strlen($tag) - 3);
            $ret[$k]['ends_at'] = $ret[$k]['starts_at'] + $struct_len + $len_match;
            if ($break && !empty($append)) {
                $content = substr_replace($content, $append, $ret[$k]['ends_at'], 0);
            }
        }
        return $ret;
    }
}