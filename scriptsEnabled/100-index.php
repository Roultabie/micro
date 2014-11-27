<?php
function index4()
{
    global $stack;

    $template = new timply('index.html');
    $currentYear  = '2014';
    $currentId    = 'year' . $currentYear;
    $template->setElement('content', '<ul id="' . $currentId . '"></ul>');
    $baseFile = $template->returnHtml();

    foreach ($stack->getStack() as $key => $object) {

        $currentTitle = (!empty($object->getMetas()->title)) ? $object->getMetas()->title : $object->getOutputName();
        $toCreate = array_reverse(explode('/', str_replace(PUBLIC_PATH, '', $object->getPath())));
        $wPath    = $object->getPath();
        foreach ($toCreate as $dir) {
            $wIndex = $wPath . 'index.html';
            if (!file_exists($wIndex)) {
                file_put_contents($wIndex, $baseFile);
            }
            $html = new DOMDocument();
            $html->loadHTMLFile($wIndex);
            $xpath    = new DOMXpath($html);
            $elements = $xpath->query('//*[@id="' . $currentId . '"]');

            //j'ajoute mes elements en sus
            $newLi = $html->createElement('li', $currentTitle);

            if (!is_null($elements)) {
                $ul = $elements->item(0);
                $li = $ul->childNodes->item(0);
                // Insert Element before first li
                if (count($li) === 0) {
                    $ul->appendChild($newLi);
                }
                else {
                    $li->parentNode->insertBefore($newLi, $li);
                }
                
            }
            file_put_contents($wIndex, $html->saveHTML(), LOCK_EX);
            unset($html, $xpath, $elements, $ul, $li, $newLi);
            $wPath = str_replace($dir, '', $wPath);
            $wPath = rtrim($wPath, '/') . '/';
            echo $wPath . PHP_EOL;
        }
    }
}
?>