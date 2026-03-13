<?php

use GlpiPlugin\Pokedex\Pokemon;
use Glpi\Exception\Http\NotFoundHttpException;

$plugin = new Plugin();
if (!$plugin->isInstalled('pokedex') || !$plugin->isActivated('pokedex')) {
    throw new NotFoundHttpException();
}

$pokemon = new Pokemon();

if (isset($_POST['add'])) {
    $pokemon->check(-1, CREATE, $_POST);
    $newid = $pokemon->add($_POST);
    Html::redirect("{$CFG_GLPI['root_doc']}/plugins/pokedex/front/pokemon.form.php?id=$newid");

} else if (isset($_POST['update'])) {
    $pokemon->check($_POST['id'], UPDATE);
    $pokemon->update($_POST);
    Html::back();

} else if (isset($_POST['delete'])) {
    $pokemon->check($_POST['id'], DELETE);
    $pokemon->delete($_POST);
    $pokemon->redirectToList();

} else if (isset($_POST['purge'])) {
    $pokemon->check($_POST['id'], PURGE);
    $pokemon->delete($_POST, 1);
    Html::redirect("{$CFG_GLPI['root_doc']}/plugins/pokedex/front/pokemon.php");

} else if (isset($_POST['restore'])) {
    $pokemon->check($_POST['id'], DELETE);
    $pokemon->restore($_POST);
    Html::back();

} else {
    $withtemplate = $_GET['withtemplate'] ?? 0;
    Html::header(
        Pokemon::getTypeName(),
        $_SERVER['PHP_SELF'],
        'plugins',
        Pokemon::class,
        'pokemon'
    );
    $pokemon->display([
        'id'           => $_GET['id'] ?? 0,
        'withtemplate' => $withtemplate,
    ]);
    Html::footer();
}
