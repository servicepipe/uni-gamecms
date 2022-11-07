<?php
const MODULE_NAME = 'demos';

$ExtraModule = new ExtraModule(MODULE_NAME);

$module = [
	'name'          => 'demos',
	'to_head'       => "<script src=\"../modules_extra/demos/ajax/ajax.js?v={cache}\"></script>"
		. "<link rel=\"stylesheet\" href=\"../modules_extra/demos/templates/" . configs()->template . "/css/primary.css?v={cache}\">",
	'tpl_dir'       => "../../../modules_extra/demos/templates/" . configs()->template . "/tpl/",
	'tpl_dir_admin' => "../../../modules_extra/demos/templates/admin/tpl/"
];

global $Autoloader;

$Autoloader->addNamespace('Demos', $ExtraModule->getClassesDirectory());
$Autoloader->addNamespace('Demos\\Methods', $ExtraModule->getClassesDirectory('Methods'));
$Autoloader->addNamespace('Demos\\Methods\\AutoDemo', $ExtraModule->getClassesDirectory('Methods/AutoDemo'));
$Autoloader->register();