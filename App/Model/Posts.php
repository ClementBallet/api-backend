<?php
namespace App\Model;

use App\Lib\Config;

/**
 * Model Posts qui va chercher des articles dans une base de données
 */
class Posts
{
    private static $DATA = [];

    /**
     * Retrouve tous les articles
     * @return array
     */
    public static function all()
    {
        return self::$DATA;
    }

    /**
     * Ajoute un article
     * @param $post
     * @return mixed
     */
    public static function add($post)
    {
        $post->id = count(self::$DATA) + 1;
        self::$DATA[] = $post;
        self::save();
        return $post;
    }

    /**
     * Retrouve un article avec son id passé dans l'URL
     * @param int $id
     * @return array|mixed
     */
    public static function findById(int $id)
    {
        foreach (self::$DATA as $post)
        {
            if ($post->id === $id)
            {
                return $post;
            }
        }
        return [];
    }

    /**
     * Charge le fichier de données
     * @return void
     */
    public static function load()
    {
        $DB_PATH = Config::get('DB_PATH', __DIR__ . '/../../db.json');
        self::$DATA = json_decode(file_get_contents($DB_PATH));
    }

    /**
     * Sauve un article dans le fichier de données
     * @return void
     */
    public static function save()
    {
        $DB_PATH = Config::get('DB_PATH', __DIR__ . '/../../db.json');
        file_put_contents($DB_PATH, json_encode(self::$DATA, JSON_PRETTY_PRINT));
    }
}