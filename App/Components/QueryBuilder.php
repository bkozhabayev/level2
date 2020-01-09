<?php
namespace App\Components;
use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{
    private $pdo;
    private $queryFactory;

    public function __construct(PDO $pdo, QueryFactory $queryFactory)
    {
        $this->pdo = $pdo;
        $this->queryFactory = $queryFactory;
    }

    public function selectAll($table)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from("$table")
            ->orderBy(['id DESC']) ;
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(2);
        return $result;
    }

    public function selectOne($table, $id)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->where('id = :id')
            ->bindValue(':id', $id);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetch(2);
        return $result;
    }

    public function selectOneByEmail($table, $email)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['*'])
            ->from($table)
            ->where('email = :email')
            ->bindValue(':email', $email);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetch(2);
        return $result;
    }

    public function store($table, $data)
    {
        $insert = $this->queryFactory->newInsert();
        $insert
            ->into($table)
            ->cols($data);
        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }

    public function getAvatar($table, $id)
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols(['avatar'])
            ->from($table)
            ->where('id = :id')
            ->bindValue(':id', $id);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetch(2);
        return $result;
    }

    public function update($table, $data, $id)
    {
        $update = $this->queryFactory->newUpdate();
        $update
            ->table($table)
            ->cols($data)
            ->where('id = :id')
            ->bindValue(':id', $id);
        $sth = $this->pdo->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
    }

    public function selectAllCommentsJoin()
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols([
                'comments.id',
                'comments.whenadded',
                'comments.text',
                'comments.is_public',
                'users.username',
                'users.avatar' ])
            ->from('comments')
            ->join(
            'INNER',
            'users',
            'comments.user_id = users.id')
            ->orderBy(['comments.whenadded DESC']);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function selectInnerJoin()
    {
        $select = $this->queryFactory->newSelect();
        $select
            ->cols([
                'comments.id',
                'comments.whenadded',
                'comments.text',
                'comments.is_public',
                'users.username',
                'users.avatar' ])
            ->from('comments')
            ->join(
                'INNER',
                'users',
                'comments.user_id = users.id')
            ->where('is_public = :is_public')
            ->bindValue(':is_public', 1)
            ->orderBy(['comments.whenadded DESC']);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function deleteComment($id)
    {
        $delete = $this->queryFactory->newDelete();
        $delete
            ->from('comments')
            ->where('id = :id')
            ->bindValue(':id', $id);
        $sth = $this->pdo->prepare($delete->getStatement());
        $sth->execute($delete->getBindValues());
    }

}


























