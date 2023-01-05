<?php
namespace App\Lib;

/**
 * Classe Router qui sert à vérifier la méthode de la requête et faire concorder le chemin qu'on lui donne.
 * Si cela concorde, elle exécute une fonction callback qui renvoie une nouvelle instance de Request et Response
 */
class Router
{
    /**
     * Get = lecture des données
     * @param $route
     * @param $callback
     * @return void
     */
    public static function get($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0)
        {
            return;
        }

        self::on($route, $callback);
    }

    /**
     * Post = écriture des données
     * @param $route
     * @param $callback
     * @return void
     */
    public static function post($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0)
        {
            return;
        }

        self::on($route, $callback);
    }

    public static function on($regex, $cb)
    {
        $params = $_SERVER['REQUEST_URI'];
        $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
        $regex = str_replace('/', '\/', $regex);
        $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);

        if ($is_match) {
            // first value is normally the route, lets remove it
            array_shift($matches);
            // Get the matches as parameters
            $params = array_map(function ($param) {
                return $param[0];
            }, $matches);
            $cb(new Request($params), new Response());
        }
    }
}