<?php

namespace GlpiPlugin\Pokedex;

use CommonDBTM;
use Session;
use Glpi\Application\View\TemplateRenderer;

class Pokemon extends CommonDBTM
{
    static $rightname = 'pokedex';

    public static function getTypeName($nb = 0)
    {
        return 'Pokémon';
    }

    public static function getIcon()
    {
        return 'ti ti-bug';
    }

    public function getRights($interface = 'central')
    {
        return [
            READ   => __('Read'),
            CREATE => __('Create'),
            UPDATE => __('Update'),
            PURGE  => ['short' => __('Purge'), 'long' => _x('button', 'Delete permanently')],
        ];
    }

    public static function getMenuName($nb = 0)
    {
        return self::getTypeName($nb);
    }

    public static function getMenuContent()
    {
        $search = self::getSearchURL(false);
        $form   = self::getFormURL(false);

        return [
            'title'   => 'Pokédex',
            'icon'    => self::getIcon(),
            'page'    => $search,
            'options' => [
                'pokemon' => [
                    'title' => self::getMenuName(Session::getPluralNumber()),
                    'icon'  => self::getIcon(),
                    'page'  => $search,
                    'links' => [
                        'search' => $search,
                        'add'    => $form,
                    ],
                ],
            ],
        ];
    }

    public function getFormFields(): array
    {
        return [];
    }

    public function rawSearchOptions()
    {
        $tab = parent::rawSearchOptions();

        $tab[] = [
            'id'            => 2,
            'table'         => self::getTable(),
            'field'         => 'id',
            'name'          => __('ID'),
            'massiveaction' => false,
            'datatype'      => 'number',
        ];

        $tab[] = [
            'id'            => 3,
            'table'         => self::getTable(),
            'field'         => 'pokemon_id',
            'name'          => 'ID Pokédex',
            'massiveaction' => false,
            'datatype'      => 'integer',
        ];

        $tab[] = [
            'id'            => 4,
            'table'         => self::getTable(),
            'field'         => 'types',
            'name'          => 'Tipos',
            'massiveaction' => false,
            'datatype'      => 'text',
        ];

        $tab[] = [
            'id'            => 5,
            'table'         => self::getTable(),
            'field'         => 'image_url',
            'name'          => 'Imagen',
            'massiveaction' => false,
            'datatype'      => 'string',
            'nosearch'      => true,
        ];

        return $tab;
    }

    public function prepareInputForAdd($input)
    {
        $pokemon_id = $this->getRandomUniquePokemonId();
        if ($pokemon_id === false) {
            return false;
        }

        $data = $this->fetchFromPokeApi($pokemon_id);
        if ($data === false) {
            Session::addMessageAfterRedirect('Error al conectar con PokeAPI', false, ERROR);
            return false;
        }

        $input['name']       = $data['name'];
        $input['pokemon_id'] = $data['pokemon_id'];
        $input['image_url']  = $data['image_url'];
        $input['weight']     = $data['weight'];
        $input['height']     = $data['height'];
        $input['types']      = $data['types'];

        return $input;
    }

    private function fetchFromPokeApi(int $pokemon_id)
    {
        $context = stream_context_create([
            'http' => ['timeout' => 5],
        ]);

        $url = "https://pokeapi.co/api/v2/pokemon/{$pokemon_id}";
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return false;
        }

        return [
            'name'       => $data['name'],
            'pokemon_id' => $data['id'],
            'image_url'  => $data['sprites']['front_default'] ?? '',
            'weight'     => $data['weight'] ?? 0,
            'height'     => $data['height'] ?? 0,
            'types'      => implode(', ', array_map(fn($t) => $t['type']['name'], $data['types'])),
        ];
    }

    private function getRandomUniquePokemonId()
    {
        global $DB;

        for ($i = 0; $i < 20; $i++) {
            $candidate = rand(1, 1025);
            $result = $DB->request([
                'FROM'  => self::getTable(),
                'WHERE' => ['pokemon_id' => $candidate],
                'COUNT' => 'cpt',
            ]);
            $row = $result->current();
            if ((int)$row['cpt'] === 0) {
                return $candidate;
            }
        }

        Session::addMessageAfterRedirect('No se pudo encontrar un Pokémon único tras 20 intentos.', false, ERROR);
        return false;
    }

    public function showForm($ID, $options = [])
    {
        $this->initForm($ID, $options);
        TemplateRenderer::getInstance()->display('@pokedex/pokemon.form.html.twig', [
            'item'   => $this,
            'params' => $options,
        ]);

        return true;
    }
}
