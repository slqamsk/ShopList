<?php
// src/models/ProductModel.php

class ProductModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByHousehold($householdId) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE household_id = ? ORDER BY name");
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id, $householdId = null) {
        $sql = "SELECT * FROM products WHERE id = ?";
        if ($householdId) $sql .= " AND household_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $params = [$id];
        if ($householdId) $params[] = $householdId;
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($householdId, $name) {
        $stmt = $this->pdo->prepare("INSERT INTO products (household_id, name) VALUES (?, ?)");
        $stmt->execute([$householdId, $name]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $name, $householdId = null) {
        $sql = "UPDATE products SET name = ? WHERE id = ?";
        $params = [$name, $id];
        if ($householdId) {
            $sql .= " AND household_id = ?";
            $params[] = $householdId;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id, $householdId = null) {
        $sql = "DELETE FROM products WHERE id = ?";
        $params = [$id];
        if ($householdId) {
            $sql .= " AND household_id = ?";
            $params[] = $householdId;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
