<?php

$i18n = new i18n(DIR_LOCALES.DS.'lang_{LANGUAGE}.yml', DIR_CACHE.DS.'langcache');
$i18n->setFallbackLang('de');
$i18n->setPrefix('lang');
$i18n->setSectionSeparator('_');
$i18n->setMergeFallback(true); // make keys available from the fallback language
$i18n->finishSetup();
