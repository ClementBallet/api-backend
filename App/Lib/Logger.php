<?php
namespace App\Lib;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

/**
 * Classe Logger qui créée des fichiers de logs pour toutes les actions rencontrées lors de l'execution de l'application.
 * Fichiers pouvant être créés par cette classe :
 * - app.log, utilisé à la demande pour afficher les informations désirées
 * - errors.log, pour toutes les erreurs qui pourraient se passer dans l'app
 * - requests.log, pour tout ce qui touche aux requêtes HTTP faites dans l'app
 */
class Logger extends \Monolog\Logger
{
    private static $loggers = [];

    public function __construct($key = "app", $config = null)
    {
        parent::__construct($key);

        if (empty($config))
        {
            $LOG_PATH = Config::get('LOG_PATH', __DIR__ . '/../../logs');
            $config = [
                'logFile' => "{$LOG_PATH}/{$key}.log",
                'logLevel' => Level::Debug
            ];
        }

        $this->pushHandler(new StreamHandler($config['logFile'], $config['logLevel']));
    }

    /**
     * Active les logs de l'app par défaut
     * @param $key
     * @param $config
     * @return Logger|mixed
     */
    public static function getInstance($key = "app", $config = null)
    {
        if (empty(self::$loggers[$key]))
        {
            self::$loggers[$key] = new Logger($key, $config);
        }

        return self::$loggers[$key];
    }

    /**
     * Active les logs error et request
     * @return void
     */
    public static function enableSystemLogs()
    {
        $LOG_PATH = Config::get('LOG_PATH', __DIR__ . '/../../logs');

        // Error Log
        self::$loggers['error'] = new Logger('errors');
        self::$loggers['error']->pushHandler(new StreamHandler("{$LOG_PATH}/errors.log"));
        ErrorHandler::register(self::$loggers['error']);

        // Request Log
        $data = [
            $_SERVER,
            $_REQUEST,
            trim(file_get_contents("php://input"))
        ];
        self::$loggers['request'] = new Logger('request');
        self::$loggers['request']->pushHandler(new StreamHandler("{$LOG_PATH}/request.log"));
        self::$loggers['request']->info("REQUEST", $data);
    }
}