<?php

$path = OW::getPluginManager()->getPlugin('spodshowcase')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spodshowcase');

OW::getPluginManager()->addPluginSettingsRouteName('spodshowcase', 'showcase-settings');

