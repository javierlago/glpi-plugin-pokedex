<?php

namespace GlpiPlugin\Pokedex\Api;

use Glpi\Api\HL\Controller\AbstractController;
use Glpi\Api\HL\Doc as Doc;
use Glpi\Api\HL\Route;
use Glpi\Api\HL\RouteVersion;
use Glpi\Http\JSONResponse;
use Glpi\Http\Request;
use Glpi\Http\Response;
use GlpiPlugin\Pokedex\Pokemon;

#[Route(path: '/Pokedex', tags: ['Pokedex'])]
final class PokedexController extends AbstractController
{
    public static function getRawKnownSchemas(): array
    {
        return [];
    }

    #[Route(path: '/Pokemon', methods: ['GET'], security_level: Route::SECURITY_AUTHENTICATED)]
    #[RouteVersion(introduced: '2.0')]
    #[Doc\Route(description: 'List all registered Pokemon. Returns an array of Pokemon objects with their ID, name, types, weight, height and sprite image URL.')]
    public function listPokemons(Request $request): Response
    {
        global $DB;

        $results = [];
        $iterator = $DB->request([
            'FROM' => Pokemon::getTable(),
            'WHERE' => ['is_deleted' => 0],
        ]);

        foreach ($iterator as $row) {
            $results[] = [
                'id'         => (int) $row['id'],
                'name'       => $row['name'],
                'pokemon_id' => (int) $row['pokemon_id'],
                'image_url'  => $row['image_url'],
                'types'      => $row['types'],
                'weight'     => (int) $row['weight'],
                'height'     => (int) $row['height'],
            ];
        }

        return new JSONResponse($results);
    }

    #[Route(path: '/Pokemon/{id}', methods: ['GET'], requirements: ['id' => '\d+'], security_level: Route::SECURITY_AUTHENTICATED)]
    #[RouteVersion(introduced: '2.0')]
    #[Doc\Route(description: 'Get a single Pokemon by its database ID. Returns 404 if the Pokemon does not exist or has been deleted.')]
    public function getPokemon(Request $request): Response
    {
        $id = (int) $request->getAttribute('id');

        $pokemon = new Pokemon();
        if (!$pokemon->getFromDB($id)) {
            return new JSONResponse(
                ['error' => 'Pokemon not found'],
                404
            );
        }

        if ((int) $pokemon->fields['is_deleted'] === 1) {
            return new JSONResponse(
                ['error' => 'Pokemon not found'],
                404
            );
        }

        return new JSONResponse([
            'id'         => (int) $pokemon->fields['id'],
            'name'       => $pokemon->fields['name'],
            'pokemon_id' => (int) $pokemon->fields['pokemon_id'],
            'image_url'  => $pokemon->fields['image_url'],
            'types'      => $pokemon->fields['types'],
            'weight'     => (int) $pokemon->fields['weight'],
            'height'     => (int) $pokemon->fields['height'],
        ]);
    }
}
