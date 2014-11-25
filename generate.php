<?php
$mdPattern      = '/([^.]+)\.(md|markdown)/Ui';
$outputExt      = '.html';
$publicDir      = 'public';
$templateDir    = 'template';
$publicBase     = '';

date_default_timezone_set('UTC');

require_once 'libs/static.php';
require_once 'libs/timply.php';
require_once 'libs/Markdown.php';

define('SCRIPT_PATH', str_replace('generate.php', '', __FILE__));
if (file_exists(SCRIPT_PATH . 'config.php')) include SCRIPT_PATH . 'config.php';
define('PUBLIC_PATH', (!empty($argv[1])) ? $argv[1] : SCRIPT_PATH . $publicDir);
define('PUBLIC_BASE', (!empty($argv[2])) ? rtrim($argv[2], '/') : rtrim($publicBase, '/'));
define('PUBLIC_DIR', (!empty(PUBLIC_BASE)) ? array_pop(explode('/', PUBLIC_BASE)) : $publicDir);
define('PUBLIC_URL', $publicUrl);
define('TEMPLATE_PATH', (is_dir(PUBLIC_PATH . '/_' . $templateDir)) ? PUBLIC_PATH . '/_' . $templateDir : SCRIPT_PATH . $templateDir);
define('REGEN', ($argv[3] === 'regen') ? true : false);

timply::setUri(TEMPLATE_PATH);

function execScripts($level)
{
    global $scripts;
    if (is_array($scripts)) {
        foreach ($scripts as $script) {
            $function = $script . $level;
            if (function_exists($function)) {
                $function();
            }
        }
    }
    else {
        $dirPath = SCRIPT_PATH . '/scriptsEnabled';
        $list    = dir($dirPath);
        while (($entry = $list->read()) !== false) {
            if (preg_match('/([^.]+)\.php/Ui', $entry)) {
                include_once $dirPath . '/' . $entry;
                $scripts[] = preg_replace('/\d+\-([^.]+)\.php/Ui', '\1', $entry);
            }
        }
        if (is_array($scripts)) sort($scripts);
        execScripts(0);
    }
}

function generate($dirPath = '', $currentDir = '')
{
    global $stack, $object, $level;
    // Init Stack
    $stack = new staticStack();

    // Init and exec scripts level 0
    execScripts(0);
    if (empty($dirPath))  {
        $dirPath    = PUBLIC_PATH;
        $currentDir = PUBLIC_DIR;
    }
    else {
        list($before, $after) = explode(PUBLIC_DIR, $dirPath);
    }
    $after   = preg_replace('|/+|', '/', $after . '/');
    $dirPath = preg_replace('|/+|', '/', PUBLIC_PATH . $after . '/');
    if (is_dir($dirPath)) {
        $noScan      = (is_array($GLOBALS['noScan'])) ? array_merge($GLOBALS['noScan'], array('.', '..')) : array('.', '..');
        $public = dir($dirPath);
        while (($entry = $public->read()) !== false) {
            if (preg_match($GLOBALS['mdPattern'], $entry)) {
                $fileName   = preg_replace($GLOBALS['mdPattern'], '\1', $entry);
                $outputName = $fileName . $GLOBALS['outputExt'];
                if (!file_exists($dirPath . '/' . $outputName) || REGEN === true) {
                    $object = new staticObject();
                    // Exec scripts level 1
                    execScripts(1);
                    $object->setShortName($fileName);
                    $object->setPath($dirPath);
                    $object->setDirName($after);
                    $object->setInputName($entry);
                    $object->setInputContent(file_get_contents($dirPath . $entry));
                    $object->setInputUri($dirPath . $entry);
                    $object->setInputUrlRel($after . $object->getInputName());
                    $object->setInputUrlAbs(PUBLIC_URL . $object->getInputUrlRel());
                    $object->setOutputName($outputName);
                    $object->setOutputUri($dirPath . $outputName);
                    $object->setOutputUrlRel($after . $object->getOutputName());
                    $object->setOutputUrlAbs(PUBLIC_URL . $object->getOutputUrlRel());
                    // Exec scripts level 2
                    execScripts(2);
                    $stack->addObject($object);
                }
            }
            elseif (is_dir($dirPath . '/' .$entry) && !in_array($entry, $noScan) && $entry[0] !== '_') {
                generate($dirPath . '/' .$entry, $entry);
            }
        }
        $level++;
    }
    if (is_object($stack)) {
        // Exec scripts level 3
        execScripts(3);
    }
}
if (is_dir(PUBLIC_PATH) && file_exists(TEMPLATE_PATH)) {
    generate();
    // Exec scripts level 4
    execScripts(4);
}
?>