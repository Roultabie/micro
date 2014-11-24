<?php
function rss3()
{
    global $stack;
    $template = new timply('feed.rss');
    $template->setElement('siteTitle', $GLOBALS['siteTitle']);
    $template->setElement('siteLink', $GLOBALS['siteTitle']);
    $template->setElement('siteDescription', $GLOBALS['siteTitle']);
    foreach ($stack->getStack() as $key => $object) {
        if (is_object($object)) {
            $template->setElement('itemTitle', $object->getMetas()->title, 'Item');
            $template->setElement('itemLink', $object->getOutputLink(), 'Item');
            $template->setElement('itemDescription', MarkdownExtra::defaultTransform($object->getInputContent()), 'Item');
            $template->setElement('itemPubDate', $date, 'Item');
            $template->setElement('itemGuid', $object->getOutputLink(), 'Item');
        }
    }
    file_put_contents(PUBLIC_PATH . '/feed.rss', $template->returnHtml());
}
?>