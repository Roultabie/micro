<?php
function index4()
{
    global $stack;

    $template = new timply('index.html');
    $currentYear  = '2014';
    $currentId    = 'year' . $currentYear;
    $template->setElement('content', '<ul id="' . $currentId . '"></ul>');
    $baseFile = $template->returnHtml();

    foreach ($stack->getStack() as $object) {

        $currentTitle = (!empty($object->getMetas()->title)) ? $object->getMetas()->title : $object->getOutputName();
        // To create indexes in dirs in this order sub3/sub2/index.html sub1/index.html index.html
        // We must create an array like array(sub3, sub2, '') to remove in the order subs dir from path we remove sub3 first...
        // Sanitize string because original string is sub2/sub3, if explode we have array(sub2, sub3, ""), we want array("", sub2, sub3)
        // when reverse : array(sub3, sub2, '')
        $toCreate = array_reverse(explode('/', str_replace(PUBLIC_PATH, '', $object->getPath())));
        array_shift($toCreate);
        $toCreate[] = '';
        $wPath    = $object->getPath();
        $pathId = md5($wPath);
        foreach ($toCreate as $dir) {
            $wIndex = $wPath . 'index.html';
            if (!empty($toWrite[$pathId]['html'])) {
                $data = $toWrite[$pathId]['html'];
            }
            elseif (file_exists($wIndex)) {
                $data = file_get_contents($wIndex);
            }
            else {
                $data = $baseFile;
            }
            $html = new DOMDocument();
            $html->loadHTML($data);
            $xpath    = new DOMXpath($html);
            $elements = $xpath->query('//*[@id="' . $currentId . '"]');

            //j'ajoute mes elements en sus
            $newLi = $html->createElement('li');
            $a     = $html->createElement('a', $currentTitle);
            $a->setAttribute('href', $object->getOutputUrlAbs());
            $newLi->appendChild($a);

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
            $toWrite[$pathId]['file'] = $wIndex;
            $toWrite[$pathId]['html'] = $html->saveHTML();
            $wPath = str_replace($dir, '', $wPath);
            $wPath = rtrim($wPath, '/') . '/';
            $pathId = md5($wPath);
        }
    }

    foreach ($toWrite as $datas) {
        file_put_contents($datas['file'], $datas['html'], LOCK_EX);
    }
}
?>