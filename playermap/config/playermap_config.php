<?php
// Player map configuration
$language         = "fr";
$site_encoding    = "utf8";

$db_type          = 'MySQL';

$realm_db['addr']     = 'localhost:3306';  // SQL server IP:port this realmd located on (utilisation de l'adresse locale)
$realm_db['user']     = 'blamacfly';                    // SQL server login this realmd located on
$realm_db['pass']     = 'ferwyn8289';                   // SQL server pass this realmd located on
$realm_db['name']     = 'acore_auth';                   // realmd DB name
$realm_db['encoding'] = 'utf8';                         // SQL connection encoding

//==== For each realm, you must have $world_db and $characters_db and $server filled in, label each with the realm id: ex: $world_db[REALMID]['addr'] === //

// position in array must represent realmd ID
$world_db[1]['addr']          = 'localhost:3306'; // SQL server IP:port this DB located on (utilisation de l'adresse locale)
$world_db[1]['user']          = 'blamacfly';                   // SQL server login this DB located on
$world_db[1]['pass']          = 'ferwyn8289';                  // SQL server pass this DB located on
$world_db[1]['name']          = 'acore_world';                 // World Database name, by default "mangos" for MaNGOS, "world" for AzerothCore/TrinityCore
$world_db[1]['encoding']      = 'utf8';                        // SQL connection encoding

// position in array must represent realmd ID
$characters_db[1]['addr']     = 'localhost:3306'; // SQL server IP:port this DB located on (utilisation de l'adresse locale)
$characters_db[1]['user']     = 'blamacfly';                   // SQL server login this DB located on
$characters_db[1]['pass']     = 'ferwyn8289';                  // SQL server pass this DB located on
$characters_db[1]['name']     = 'acore_characters';            // Character Database name
$characters_db[1]['encoding'] = 'utf8';                        // SQL connection encoding

//---- Game Server Configuration ----

$server_type        =  1;                          // 0=MaNGOS, 1=AzerothCore/TrinityCore

// position in array must represent realmd ID, same as in $world_db
$server[1]['addr']          = 'localhost'; // Game Server IP, as seen by MiniManager, from your webhost (utilisation de l'adresse locale)
$server[1]['addr_wan']      = 'sylvania.servegame.com'; // Game Server IP, as seen by clients - Must be external address
$server[1]['game_port']     =  8085;                    // Game Server port
$server[1]['rev']           = '';                       // MaNGOS rev. used (AzerothCore/TrinityCore does not need this)
$server[1]['both_factions'] =  true;                    // Allow to see opponent faction characters. Affects only players.


// === Player Map configuration === //

// GM online options
$gm_online                         = true;
$gm_online_count                   = true;

$map_gm_show_online_only_gmoff     = 1; // show GM point only if in '.gm off' [1/0]
$map_gm_show_online_only_gmvisible = 1; // show GM point only if in '.gm visible on' [1/0]
$map_gm_add_suffix                 = 1; // add '{GM}' to name [1/0]
$map_status_gm_include_all         = 1; // include 'all GMs in game'/'who on map' [1/0]

// Map options
$map_show_status                   = 1; // show server status block [1/0]
$map_time_to_show_uptime           = 0; // show server uptime [1/0]
$map_time_to_show_maxonline        = 0; // show max online [1/0]
$map_time_to_show_gmonline         = 0; // show GM online count [1/0]

// Default map options
$map_options['enablelink']         = 1; // enable link to other maps [1/0]
$map_options['showzones']          = 0; // show zone names [1/0]
$map_options['showcross']          = 0; // show map crosshair [1/0]
$map_options['showframe']          = 0; // show frame [1/0]
$map_options['showplayerscount']   = 1; // show players count [1/0]
$map_options['update_players']     = 1; // update players position [1/0]
$map_options['showtooltip']        = 1; // show player tooltip [1/0]
$map_options['showcoords']         = 0; // show cursor game coords [1/0]

// Tooltip options
$map_options['tooltip_faction']    = 1; // show faction icon in tooltip [1/0]
$map_options['tooltip_guild']      = 1; // show guild in tooltip [1/0]
$map_options['tooltip_honor']      = 0; // show honor points in tooltip [1/0]
$map_options['tooltip_arena']      = 0; // show arena points in tooltip [1/0]

// Map update time
$map_update_interval               = 5; // update players position interval (seconds) [default: 5]
?>
