<?php

$i18n = new i18n(DIR_LOCALES.DIRECTORY_SEPARATOR.'lang_{LANGUAGE}.yml', DIR_CACHE.DIRECTORY_SEPARATOR.'langcache');
$i18n->setFallbackLang('en');
$i18n->setPrefix('lang');
$i18n->setSectionSeparator('_');
$i18n->setMergeFallback(true); // make keys available from the fallback language
$i18n->finishSetup();