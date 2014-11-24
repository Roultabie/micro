<?php
function page1()
{
    global $object;
    timply::setFileName('page.html');
    $template = new timply();
    $object->setTemplate($template);
}
function page2()
{
    global $stack, $object;
    $template = $object->getTemplate();
    $template->setElement('content', MarkdownExtra::defaultTransform($object->getInputContent()));
    $object->setOutputContent($template->returnHtml());
}
function page3()
{
    global $stack;
    foreach ($stack->getStack() as $key => $object) {
        if ($object->getMetas()->type === 'page') {
            file_put_contents($object->getOutputUri(), $object->getOutputContent());
        }
    }
}
?>