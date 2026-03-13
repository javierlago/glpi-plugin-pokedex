<?php

function plugin_pokedex_install()
{
    global $DB;

    $default_charset   = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();

    $migration = new Migration(PLUGIN_POKEDEX_VERSION);

    if (!$DB->tableExists('glpi_plugin_pokedex_pokemons')) {
        $DB->doQuery(
            "CREATE TABLE `glpi_plugin_pokedex_pokemons` (
                `id`            INT unsigned NOT NULL AUTO_INCREMENT,
                `name`          VARCHAR(255) NOT NULL DEFAULT '',
                `pokemon_id`    INT unsigned NOT NULL DEFAULT 0,
                `image_url`     VARCHAR(500) NOT NULL DEFAULT '',
                `types`         VARCHAR(255) NOT NULL DEFAULT '',
                `weight`        INT NOT NULL DEFAULT 0,
                `height`        INT NOT NULL DEFAULT 0,
                `is_deleted`    TINYINT NOT NULL DEFAULT 0,
                `date_mod`      DATETIME DEFAULT NULL,
                `date_creation` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC"
        );
    }

    $migration->executeMigration();

    return true;
}

function plugin_pokedex_uninstall()
{
    global $DB;

    $DB->doQuery("DROP TABLE IF EXISTS `glpi_plugin_pokedex_pokemons`");

    return true;
}
