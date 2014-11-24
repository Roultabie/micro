<?php
function meta2()
{
    global $stack, $object;
    $markdown = $object->getInputContent();
    $html     = $object->getOutputContent();
    // Get existing metas
    preg_match_all('/^%(?<metas>[a-z\d\s\:\-]*)$/im', $markdown, $matches);
    if (is_array($matches['metas'])) {
        foreach ($matches['metas'] as $i => $meta) {
            $sepPos = strpos($meta, ':');
            $key    = ($sepPos !== false) ? substr($meta, 0, $sepPos) : $meta;
            $value  = ($sepPos !== false) ? trim(substr($meta, $sepPos + 1)) : true;
            $metas[trim($key)] = $value;
            // And clean markdown file
            $markdown = str_replace(trim($matches[0][$i]), '', $markdown);
        }
        $object->setInputContent($markdown);
        //Update metas
        if (empty($metas['type'])) $metas['type']               = $GLOBALS['defaultType'];
        if (empty($metas['author'])) $metas['author']           = $GLOBALS['author'];
        if (empty($metas['creation'])) $metas['creation']       = date('Y-m-d H:i:s', filemtime($object->getInputUri));
        if (empty($metas['publication'])) $metas['publication'] = date('Y-m-d H:i:s', time());
        if ($GLOBALS['regen'] === false || (!isset($metas['revision']))) {
            $metas['revision'] = date('Y-m-d H:i:s', filemtime($object->getInputUri));
        }
        foreach ($metas as $key => $value) {
            $newMetas .= '%' . $key . ':' . $value . PHP_EOL;
        }
        file_put_contents($object->getInputUri(), $newMetas . ltrim($markdown));
        // Update object
        $object->setMetas((object)$metas);
        $template = $object->getTemplate();
        $template->setElement('metaType', $object->getMetas()->type);
        $template->setElement('metaAuthor', $object->getMetas()->author);
        $template->setElement('metaCreation', $object->getMetas()->creation);
        $template->setElement('metaPublication', $object->getMetas()->publication);
        $template->setElement('metaRevision', $object->getMetas()->revision);
        // Clean vars
        unset($newMetas);
    }
}
?>