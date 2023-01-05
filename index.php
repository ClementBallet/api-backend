<?php
require __DIR__ . '/vendor/autoload.php';

use App\Controller\Home;
use App\Lib\App;
use App\Lib\Request;
use App\Lib\Response;
use App\Lib\Router;
use App\Model\Posts;

Posts::load();

Router::get('/', function () {
    $controller = new Home();
    $controller->indexAction();
});

Router::get('/post', function (Request $req, Response $res) {
    $res->toJSON(Posts::all());
});

Router::post('/post', function (Request $req, Response $res) {
    $post = Posts::add($req->getJSON());
    $res->status(201)->toJSON($post);
});

Router::get('/post/([0-9]*)', function (Request $req, Response $res) {
    $post = Posts::findById($req->params[0]);

    if ($post)
    {
        $res->toJSON($post);
    }
    else
    {
        $res->status(404)->toJSON(['error' => "Not Found"]);
    }
});

App::run();