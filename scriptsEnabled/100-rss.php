<?php
function rss4()
{
    global $stack;

    //$maxItems = (!empty($GLOBALS['maxItems'])) ? $GLOBALS['maxItems'] : 10;
    if (REGEN === false) {
        $feedUri  = PUBLIC_PATH . '/feed.rss';
        $template = new timply('feed.rss');
        $template->setElement('siteTitle', $GLOBALS['siteTitle']);
        $template->setElement('siteLink', $GLOBALS['siteTitle']);
        $template->setElement('siteDescription', $GLOBALS['siteTitle']);
        foreach ($stack->getStack() as $key => $object) {
            if (is_object($object)) {
                $template->setElement('itemTitle', $object->getMetas()->title, 'Item');
                $template->setElement('itemLink', $object->getOutputUrlAbs(), 'Item');
                $template->setElement('itemDescription', MarkdownExtra::defaultTransform($object->getInputContent()), 'Item');
                $template->setElement('itemPubDate', $date, 'Item');
                $template->setElement('itemGuid', $object->getOutputUrlAbs(), 'Item');
            }
        }
        if (file_exists($feedUri)) {
            $oldRss = simplexml_load_file($feedUri);
            foreach ($oldRss->channel->item as $elements) {
                $template->setElement('itemTitle', $elements->title, 'Item');
                $template->setElement('itemLink',$elements->link, 'Item');
                $template->setElement('itemDescription', $elements->description, 'Item');
                $template->setElement('itemPubDate', $elements->pubDate, 'Item');
                $template->setElement('itemGuid', $elements->guid, 'Item');
            }
        }
        file_put_contents($feedUri, $template->returnHtml());
    }
}
?>