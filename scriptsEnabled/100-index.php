<?php
function index4()
{
    global $stack;

    $template = new timply('index.html');
    $currentYear  = '2014';
    $currentId    = 'year' . $currentYear;

    foreach ($stack->getStack() as $key => $object) {
        
        $currentIndex = $object->getPath() . 'index.html';
        $currentTitle = (!empty($object->getMetas()->title)) ? $object->getMetas()->title : $object->getOutputName();

        if (!file_exists($currentIndex)) {
            $template->setElement('content', '<ul id="' . $currentId . '"></ul>');
            file_put_contents($currentIndex, $template->returnHtml());
        }
        $html = new DOMDocument();
        $html->loadHTMLFile($currentIndex);
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
        file_put_contents($currentIndex, $html->saveHTML());
    }
}
?>