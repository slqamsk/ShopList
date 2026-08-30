<?php
// src/models/InvitationModel.php

class InvitationModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($householdId, $inviterId, $inviteeEmail = null, $expiresInDays = 7) {
        $code = bin2hex(random_bytes(16));
        $expiresAt = $expiresInDays ? date('Y-m-d H:i:s', strtotime("+$expiresInDays days")) : null;
        $stmt = $this->pdo->prepare("INSERT INTO invitations (household_id, inviter_id, invitee_email, code, expires_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$householdId, $inviterId, $inviteeEmail, $code, $expiresAt]);
        return $code;
    }

    public function findByCode($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM invitations WHERE code = ? AND status = 'pending' AND (expires_at IS NULL OR expires_at > NOW())");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function accept($code, $userId) {
        $inv = $this->findByCode($code);
        if (!$inv) return false;

        $this->pdo->beginTransaction();
        try {
            // Добавляем пользователя в домохозяйство
            $stmt = $this->pdo->prepare("INSERT INTO household_members (household_id, user_id) VALUES (?, ?)");
            $stmt->execute([$inv['household_id'], $userId]);

            // Обновляем статус приглашения
            $stmt = $this->pdo->prepare("UPDATE invitations SET status = 'accepted' WHERE code = ?");
            $stmt->execute([$code]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
