<?php
namespace App\Lib;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    public static string $host;
    public static string $user;
    public static string $pass;
    public static string $dbName;
    private static ?PDO $connexion = NULL;
    private static false|PDOStatement $request;

    /**
     * Connexion à la base de données à l'aide de PDO
     * @return PDO|PDOException
     */
    public static function connect(): PDO|PDOException
    {
        try
        {
            if (is_null(self::$connexion))
            {
                $host = self::$host;
                $dbName = self::$dbName;
                $user = self::$user;
                $pass = self::$pass;

                $path = "mysql:host=$host;dbname=$dbName;charset=utf8";
                $pdo = new PDO($path, $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connexion = $pdo;
            }
            return self::$connexion;
        }
        catch (PDOException $e)
        {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Requête préparée en 3 étapes :
     * - on prépare l'exécution en passant la requête SQL en paramètre
     * - on exécute la requête en lui passant un tableau avec d'éventuels paramètres (Ex: id, string...)
     * - on renvoie le nombre de lignes en BDD ramenées ou affectées par la requête
     * @param string $query La requête SQL
     * @param array $array Les valeurs associées aux variables SQL (? ou :id par exemple)
     * @return int Renvoie le nombre de lignes en BDD ramenées ou affectées par la requête
     */
    public static function prepReq(string $query, array $array = []): int
    {
        self::$request = self::connect()->prepare($query);
        self::$request->execute($array);
        return self::$request->rowCount();
    }

    /**
     * Récupère les données de la requête
     * @return bool|array Renvoie un tableau associatif contenant toutes les lignes du jeu de résultats ou false si une erreur est survenue
     */
    public static function fetchData(): bool|array
    {
        return self::$request->fetchAll(PDO::FETCH_ASSOC);
    }
}