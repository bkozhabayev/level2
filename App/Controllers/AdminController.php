<?php
namespace App\Controllers;

use App\Components\QueryBuilder;
use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use League\Plates\Engine;

class AdminController
{
    private $engine;
    private $qb;
    private $queryFactory;
    private $auth;

    public function __construct(Engine $engine, QueryBuilder $qb, QueryFactory $queryFactory, Auth $auth)
    {
        $this->engine = $engine;
        $this->qb = $qb;
        $this->queryFactory = $queryFactory;
        $this->auth = $auth;
    }

    public function check()
    {
        if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN))
        {
            header('/');
            exit;
        }
    }
    
    public function admin()
    {
        $this->check();
        $comments = $this->qb->selectAllCommentsJoin();
        echo $this->engine->render('admin', ['comments'=>$comments]);
    }

    public function allowComment()
    {
        $this->check();
        $data = [
            'is_public' => 1
        ];
        $this->qb->update('comments', $data, $_GET['id']);
        header('Location: /admin');
        exit;
    }

    public function disallowComment()
    {
        $this->check();
        $data = [
            'is_public' => 0
        ];
        $this->qb->update('comments', $data, $_GET['id']);
        header('Location: /admin');
        exit;
    }

    public function deleteComment()
    {
        $this->check();
        $this->qb->deleteComment($_GET['id']);
        header('Location: /admin');
        exit;
    }
}































