<?php
function page1()
{
    global $object;
    $template = new timply('page.html');
    $object->setTemplate($template);
}
function page2()
{
    global $stack, $object;
    $template = $object->getTemplate();
    $template->setElement('content', MarkdownExtra::defaultTransform($object->getInputContent()));
    $template->setElement('pageLinkAbs', $object->getOutputUrlAbs());
    $template->setElement('pageLinkRel', $object->getOutputUrlRel());
    $template->setElement('sourceLinkAbs', $object->getInputUrlAbs());
    $template->setElement('sourceLinkRel', $object->getInputUrlRel());
    $object->setOutputContent($template->returnHtml());
}
function page4()
{
    global $stack;
    foreach ($stack->getStack() as $key => $object) {
        if ($object->getMetas()->type === 'page') {
            file_put_contents($object->getOutputUri(), $object->getOutputContent());
        }
    }
}
?>