<?php
function summary2()
{
    global $stack, $object;
    $markdown = $object->getInputContent();
    $html     = $object->getOutputContent();
    //define options
    $titlePattern   = (!empty($GLOBALS['summaryTitlePattern'])) ? $GLOBALS['summaryTitlePattern'] : '/(###\s*)(.*)/i';
    $summaryPattern = (!empty($GLOBALS['summaryName'])) ? $GLOBALS['summaryName'] : '/(###\s*)(summary)/i';
    $sanitize       = function($string) {
                          $string = mb_strtolower($string);
                          $string = strtr($string, array('à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'a'=>'a', 'a'=>'a', 'a'=>'a',
                                                         'ç'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c',
                                                         'd'=>'d', 'd'=>'d',
                                                         'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e',
                                                         'g'=>'g', 'g'=>'g', 'g'=>'g',
                                                         'h'=>'h', 'h'=>'h',
                                                         'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', '?'=>'i',
                                                         'j'=>'j', 'k'=>'k', '?'=>'k', 'l'=>'l', 'l'=>'l', 'l'=>'l', '?'=>'l', 'l'=>'l',
                                                         'ñ'=>'n', 'n'=>'n', 'n'=>'n', 'n'=>'n', '?'=>'n', '?'=>'n',
                                                         'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'o'=>'o', 'o'=>'o', 'o'=>'o', 'œ'=>'o', 'ø'=>'o',
                                                         'r'=>'r', 'r'=>'r',
                                                         's'=>'s', 's'=>'s', 's'=>'s', 'š'=>'s', '?'=>'s',
                                                         't'=>'t', 't'=>'t', 't'=>'t',
                                                         'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u',
                                                         'w'=>'w', 'ý'=>'y', 'ÿ'=>'y', 'y'=>'y',
                                                         'z'=>'z', 'z'=>'z', 'ž'=>'z'));
                          $string = preg_replace('/\W/', '', $string);
                          return $string;
                      };
    if (preg_match($summaryPattern, $markdown, $title)) {
        $markdown = str_replace($title[0], '{{{summary}}}', $markdown);
        preg_match_all($titlePattern, $markdown, $matches);
        if (is_array($matches)) {
            $level = 1;
            foreach ($matches[2] as $key => $value) {
                if (strpos($value, '<a name') === false) {
                    $anchor = $sanitize($value);
                    $list .= $level . '.  [' .trim($value) . '](#' . $anchor . '),' . PHP_EOL;
                    $markdown = str_replace($matches[0][$key], $matches[1][$key] . '<a name="' . $anchor . '"></a>' . $value, $markdown);
                    $level++;
                }
            }
            $list     = rtrim($list, PHP_EOL . ',') . '.' . PHP_EOL;
            $summary  = $title[1] . '<a name="' . $sanitize($title[2]) . '"></a>' . $title[2] . PHP_EOL . $list;
            $markdown = str_replace('{{{summary}}}', $summary, $markdown);
            $object->setInputContent($markdown);
        }
    }
    // Penser à la fin de la création du sommaire à ajouter une ancre à celui-ci
    // pour qu'à la prochaine génération, si il est déjà créé, le Plugin n'en fait pas un second
    // genre ###summary[summary] ne matchera plus.
}
?>