<?php
##
# How to create plugin to work on static objects :
# 
# Actually, two function exists : pluginName0 and pluginName1
# 
# pluginName0 is called before files are created / edited and after objects are initialized.
# At this point you can get / edit objects, all changes affects the result (write).
# 
# pluginName1 is called after writes.
# At this point, you can get / edit objects but nothing affect the result (write).
# It's usually used for clean vars / works before calling next object in stack.

function example0()
{
    // How to get stack :
    $stack = $GLOBALS['stack']->getStack();

    // How to travel into the stack :
    foreach ($GLOBALS['stack']->getStack() as $key => $object) {
        // How to get object basic elements
        $object->getFilePath();
        $object->getSourceName();
        $object->getBaseName();
        $object->getHtmlName();
        $object->getHtml();
        $object->getMarkdown();

        //How to edit object basic elements
        $object->setFilePath();
        $object->setSourceName();
        $object->setBaseName();
        $object->setHtmlName();
        $object->setHtml();
        $object->setMarkdown();
    }
}

function example1()
{
    unset($foo);
    unset($bar);
}
?>