<?php

namespace GlpiPlugin\Pokedex;

use CommonGLPI;
use Html;
use Profile;
use Session;

class PokedexProfile extends CommonGLPI
{
    public static function getTypeName($nb = 0)
    {
        return 'Pokédex';
    }

    public static function getIcon()
    {
        return 'ti ti-bug';
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item instanceof Profile) {
            return self::createTabEntry(self::getTypeName(), 0, $item::getType(), self::getIcon());
        }
        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item instanceof Profile) {
            self::showForProfile($item);
        }
        return true;
    }

    public static function showForProfile(Profile $profile)
    {
        $canedit = Profile::canUpdate();

        if ($canedit) {
            echo '<form method="post" action="' . htmlspecialchars(Profile::getFormURL()) . '" data-track-changes="true">';
            echo Html::hidden('id', ['value' => $profile->getID()]);
            echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);
        }

        $rights = [
            [
                'rights' => (new Pokemon())->getRights(),
                'label'  => Pokemon::getTypeName(),
                'field'  => Pokemon::$rightname,
            ],
        ];

        echo "<div class='card-body p-0'>";
        $profile->displayRightsChoiceMatrix($rights, ['canedit' => $canedit]);
        echo "</div>";

        if ($canedit) {
            echo '<div class="card-body mx-n2 border-top d-flex flex-row-reverse">';
            echo Html::submit(_x('button', 'Save'), [
                'name'  => 'update',
                'class' => 'btn btn-primary',
                'icon'  => 'ti ti-device-floppy',
            ]);
            echo '</div>';
            Html::closeForm();
        }
    }
}