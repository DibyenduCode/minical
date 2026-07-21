<?php

namespace App\Services;

use App\Core\Database;

class PaymentService {
    public static function processPayment(int $bookingId, int $userId, string $gateway, float $amount, string $currency = 'USD'): array {
        $db = Database::getInstance();
        $transactionId = 'tx_' . strtolower($gateway) . '_' . bin2hex(random_bytes(8));

        $stmt = $db->prepare("
            INSERT INTO `payments` (`booking_id`, `user_id`, `gateway`, `transaction_id`, `amount`, `currency`, `status`)
            VALUES (:booking_id, :user_id, :gateway, :transaction_id, :amount, :currency, 'success')
        ");
        $stmt->execute([
            'booking_id'     => $bookingId,
            'user_id'        => $userId,
            'gateway'        => $gateway,
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'currency'       => $currency
        ]);

        // Update booking status to 'paid'
        $updateBooking = $db->prepare("UPDATE `bookings` SET `status` = 'paid' WHERE `id` = :id");
        $updateBooking->execute(['id' => $bookingId]);

        return [
            'status'         => 'success',
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'currency'       => $currency
        ];
    }
}
