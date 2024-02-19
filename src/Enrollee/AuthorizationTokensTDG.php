<?php

namespace App\Enrollee;

use \PDO;

class AuthorizationTokensTDG
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addToken(string $token): void
    {
        $id = $this->pdo->lastInsertId();
        $stmt = $this->pdo->prepare("INSERT INTO authorizationTokens (token_id, token) VALUE (:token_id, :token)");
        $stmt->bindValue(':token_id', $id);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
    }

    public function getIdByToken(string $token): int
    {
        $stmt = $this->pdo->prepare('SELECT token_id FROM authorizationtokens WHERE token=:token');
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['token_id'];
    }
}