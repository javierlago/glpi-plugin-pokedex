<?php

use GlpiPlugin\Pokedex\Pokemon;
use GlpiPlugin\Pokedex\Api\PokedexController;
use Glpi\Plugin\Hooks;

define('PLUGIN_POKEDEX_VERSION', '1.0.0');

function plugin_version_pokedex()
{
    return [
        'name'         => 'Pokedex',
        'version'      => PLUGIN_POKEDEX_VERSION,
        'author'       => 'Pokedex Team',
        'license'      => 'GPLv3+',
        'requirements' => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.99.99',
            ],
        ],
    ];
}

function plugin_init_pokedex()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS[Hooks::MENU_TOADD]['pokedex'] = ['plugins' => Pokemon::class];
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['pokedex'][] = 'css/pokemon-card.css';
    $PLUGIN_HOOKS[Hooks::API_CONTROLLERS]['pokedex'] = [PokedexController::class];
}

function plugin_pokedex_check_config()
{
    return true;
}
