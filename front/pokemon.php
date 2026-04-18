<?php

use GlpiPlugin\Pokedex\Pokemon;
use Glpi\Exception\Http\NotFoundHttpException;
use Glpi\Exception\Http\AccessDeniedHttpException;

$plugin = new Plugin();
if (!$plugin->isInstalled('pokedex') || !$plugin->isActivated('pokedex')) {
    throw new NotFoundHttpException();
}

if (Pokemon::canView()) {
    Html::header(
        Pokemon::getTypeName(),
        $_SERVER['PHP_SELF'],
        'tools',
        Pokemon::class,
        'pokemon'
    );
    Search::show(Pokemon::class);
    Html::footer();
} else {
    throw new AccessDeniedHttpException();
}
