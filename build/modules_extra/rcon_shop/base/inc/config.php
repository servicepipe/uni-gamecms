<?php
const MODULE_NAME = 'rcon_shop';

$ExtraModule = new ExtraModule(MODULE_NAME);

global $Autoloader;

$Autoloader->addNamespace('RconShop', $ExtraModule->getClassesDirectory());
$Autoloader->register();