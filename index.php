<?php

ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

include 'tests/bootstrap.php';

$phRender = new \PhRender\PhRender();
$template = new \PhRender\Template\Template($phRender);
$template->loadHtml(TEST_PATH . 'template/overall.html');
$scopeData = json_decode(file_get_contents(TEST_PATH . 'template/overallData.json'), true);
$template->getScope()->setData($scopeData);

echo $template->render();

