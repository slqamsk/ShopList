<?php
// src/models/ShoppingListModel.php

class ShoppingListModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByHousehold($householdId) {
        $stmt = $this->pdo->prepare("
            SELECT sl.*, p.name as product_name, u.username as added_by_username
            FROM shopping_list sl
            JOIN products p ON sl.product_id = p.id
            JOIN users u ON sl.added_by = u.id
            WHERE sl.household_id = ?
            ORDER BY sl.status DESC, p.name
        ");
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($householdId, $productId, $userId, $quantity = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO shopping_list (household_id, product_id, added_by, quantity) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$householdId, $productId, $userId, $quantity]);
    }

    public function updateStatus($id, $status, $householdId = null) {
        $sql = "UPDATE shopping_list SET status = ? WHERE id = ?";
        $params = [$status, $id];
        if ($householdId) {
            $sql .= " AND household_id = ?";
            $params[] = $householdId;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateQuantity($id, $quantity, $householdId = null) {
        $sql = "UPDATE shopping_list SET quantity = ? WHERE id = ?";
        $params = [$quantity, $id];
        if ($householdId) {
            $sql .= " AND household_id = ?";
            $params[] = $householdId;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id, $householdId = null) {
        $sql = "DELETE FROM shopping_list WHERE id = ?";
        $params = [$id];
        if ($householdId) {
            $sql .= " AND household_id = ?";
            $params[] = $householdId;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function clearBought($householdId) {
        $stmt = $this->pdo->prepare("DELETE FROM shopping_list WHERE household_id = ? AND status = 'bought'");
        return $stmt->execute([$householdId]);
    }

    public function clearAll($householdId) {
        $stmt = $this->pdo->prepare("DELETE FROM shopping_list WHERE household_id = ?");
        return $stmt->execute([$householdId]);
    }
}
