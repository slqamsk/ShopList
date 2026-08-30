<?php
// src/models/HouseholdModel.php

class HouseholdModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $userId) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO households (name, created_by) VALUES (?, ?)");
            $stmt->execute([$name, $userId]);
            $householdId = $this->pdo->lastInsertId();

            // Добавляем создателя как администратора
            $stmt = $this->pdo->prepare("INSERT INTO household_members (household_id, user_id, role) VALUES (?, ?, 'admin')");
            $stmt->execute([$householdId, $userId]);

            $this->pdo->commit();
            return $householdId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT h.*, hm.role 
            FROM households h
            JOIN household_members hm ON h.id = hm.household_id
            WHERE hm.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($householdId, $userId = null) {
        $sql = "SELECT * FROM households WHERE id = ?";
        if ($userId) {
            $sql .= " AND EXISTS (SELECT 1 FROM household_members WHERE household_id = households.id AND user_id = ?)";
        }
        $stmt = $this->pdo->prepare($sql);
        $params = [$householdId];
        if ($userId) $params[] = $userId;
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMembers($householdId) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.username, u.email, hm.role, hm.joined_at
            FROM household_members hm
            JOIN users u ON hm.user_id = u.id
            WHERE hm.household_id = ?
        ");
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember($householdId, $userId, $role = 'member') {
        $stmt = $this->pdo->prepare("INSERT INTO household_members (household_id, user_id, role) VALUES (?, ?, ?)");
        return $stmt->execute([$householdId, $userId, $role]);
    }

    public function isMember($householdId, $userId) {
        $stmt = $this->pdo->prepare("SELECT id FROM household_members WHERE household_id = ? AND user_id = ?");
        $stmt->execute([$householdId, $userId]);
        return $stmt->fetch() !== false;
    }

    public function isAdmin($householdId, $userId) {
        $stmt = $this->pdo->prepare("SELECT role FROM household_members WHERE household_id = ? AND user_id = ?");
        $stmt->execute([$householdId, $userId]);
        $row = $stmt->fetch();
        return $row && $row['role'] === 'admin';
    }
}
