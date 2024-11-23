<?php
class Project {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name) {
        $stmt = $this->pdo->prepare('INSERT INTO projects (name, status) VALUES (?, "design_pending")');
        $stmt->execute([$name]);
        return $this->pdo->lastInsertId();
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM projects ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    // Add other project-related methods here
}