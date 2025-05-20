<?php

class DBLayer
{
    public $link_id;
    public $query_result;
    public $saved_queries = array();
    public $num_queries = 0;

    public function __construct($db_host, $db_username, $db_password, $db_name)
    {
        // Ajouter un message d'erreur pour le débogage
        error_log("Tentative de connexion à la base de données: $db_host, $db_username, $db_name");
        
        $this->link_id = @mysqli_connect($db_host, $db_username, $db_password);

        if ($this->link_id) {
            if (mysqli_select_db($this->link_id, $db_name)) {
                error_log("Connexion à la base de données réussie");
                return $this->link_id;
            } else {
                error_log("Erreur lors de la sélection de la base de données: " . mysqli_error($this->link_id));
                $this->close();
            }
        } else {
            error_log("Erreur de connexion à MySQL: " . mysqli_connect_error());
            $this->link_id = false;
        }
    }

    public function isValid()
    {
        return $this->link_id;
    }

    public function query($sql)
    {
        if (!$this->link_id) {
            error_log("Erreur: Pas de connexion à la base de données");
            return false;
        }

        $this->query_result = mysqli_query($this->link_id, $sql);

        if ($this->query_result) {
            ++$this->num_queries;
            return $this->query_result;
        } else {
            error_log("Erreur SQL: " . mysqli_error($this->link_id) . " - Requête: $sql");
            return false;
        }
    }


    public function result($query_id = 0, $row = 0)
    {
        return ($query_id) ? mysqli_result($query_id, $row) : false;
    }


    public function fetch_assoc($query_id = 0)
    {
        return ($query_id) ? mysqli_fetch_assoc($query_id) : false;
    }


    public function fetch_row($query_id = 0)
    {
        return ($query_id) ? mysqli_fetch_row($query_id) : false;
    }


    public function num_rows($query_id = 0)
    {
        return ($query_id) ? mysqli_num_rows($query_id) : false;
    }


    public function affected_rows()
    {
        return ($this->link_id) ? mysqli_affected_rows($this->link_id) : false;
    }


    public function insert_id()
    {
        return ($this->link_id) ? mysqli_insert_id($this->link_id) : false;
    }


    public function get_num_queries()
    {
        return $this->num_queries;
    }


    public function get_saved_queries()
    {
        return $this->saved_queries;
    }


    public function free_result($query_id = false)
    {
        return ($query_id) ? mysqli_free_result($query_id) : false;
    }


    public function close()
    {
        if ($this->link_id) {
            if ($this->query_result) {
                $this->free_result($this->query_result);
            }

            return mysqli_close($this->link_id);
        } else {
            return false;
        }
    }


    public function escape($str)
    {
        if (!$this->link_id) {
            return addslashes($str);
        }

        return mysqli_real_escape_string($this->link_id, $str);
    }
}

function error($message)
{
    error_log("Erreur PlayerMap: $message");
    die($message);
}

function sort_players($a, $b)
{
    if ($a['faction'] == $b['faction']) {
        return 0;
    }
    return ($a['faction'] < $b['faction']) ? -1 : 1;
}

function get_zone_name($zone_id)
{
    global $lang;
    include_once "zone_names_".$lang.".php";
    if (isset($zone_names[$zone_id])) {
        return $zone_names[$zone_id];
    } else {
        return "Unknown zone";
    }
}

function test_realm()
{
    global $server, $port;
    $errno = 0;
    $errstr = '';
    $fp = @fsockopen($server, $port, $errno, $errstr, 2);
    if ($fp) {
        fclose($fp);
        return true;
    } else {
        return false;
    }
}
