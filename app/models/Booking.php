<?php

namespace App\Models;

use App\Core\Model;

class Booking extends Model {
    protected string $table = 'bookings';

    public function getDashboardStats(int $userId): array {
        $today = date('Y-m-d');

        // Today's Bookings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `bookings` WHERE `user_id` = :user_id AND `booking_date` = :today AND `status` != 'cancelled'");
        $stmt->execute(['user_id' => $userId, 'today' => $today]);
        $todayBookings = (int)$stmt->fetchColumn();

        // Upcoming Bookings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `bookings` WHERE `user_id` = :user_id AND `booking_date` >= :today AND `status` IN ('confirmed', 'paid', 'pending')");
        $stmt->execute(['user_id' => $userId, 'today' => $today]);
        $upcomingBookings = (int)$stmt->fetchColumn();

        // Total Bookings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `bookings` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $totalBookings = (int)$stmt->fetchColumn();

        // Cancelled Bookings
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `bookings` WHERE `user_id` = :user_id AND `status` = 'cancelled'");
        $stmt->execute(['user_id' => $userId]);
        $cancelledBookings = (int)$stmt->fetchColumn();

        // Revenue
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(p.amount), 0) FROM `payments` p 
            JOIN `bookings` b ON b.id = p.booking_id 
            WHERE b.user_id = :user_id AND p.status = 'success'
        ");
        $stmt->execute(['user_id' => $userId]);
        $revenue = (float)$stmt->fetchColumn();

        // Pending Payments
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `bookings` WHERE `user_id` = :user_id AND `status` = 'awaiting_payment'");
        $stmt->execute(['user_id' => $userId]);
        $pendingPayments = (int)$stmt->fetchColumn();

        return [
            'today' => $todayBookings,
            'upcoming' => $upcomingBookings,
            'total' => $totalBookings,
            'cancelled' => $cancelledBookings,
            'revenue' => $revenue,
            'pending_payments' => $pendingPayments
        ];
    }

    public function getBookingsForUser(int $userId, ?string $filter = null, ?string $search = null): array {
        $sql = "SELECT b.*, e.name as event_name, e.is_paid, e.price, e.currency 
                FROM `bookings` b 
                JOIN `events` e ON e.id = b.event_id 
                WHERE b.user_id = :user_id";
        $params = ['user_id' => $userId];

        if ($filter === 'today') {
            $sql .= " AND b.booking_date = CURRENT_DATE()";
        } elseif ($filter === 'upcoming') {
            $sql .= " AND b.booking_date >= CURRENT_DATE() AND b.status IN ('confirmed', 'paid', 'pending')";
        } elseif ($filter === 'completed') {
            $sql .= " AND b.status = 'completed'";
        } elseif ($filter === 'cancelled') {
            $sql .= " AND b.status = 'cancelled'";
        }

        if (!empty($search)) {
            $sql .= " AND (b.customer_name LIKE :search OR b.customer_email LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY b.booking_date DESC, b.start_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createBooking(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `bookings` (`user_id`, `event_id`, `customer_name`, `customer_email`, `booking_date`, `start_time`, `end_time`, `status`)
            VALUES (:user_id, :event_id, :customer_name, :customer_email, :booking_date, :start_time, :end_time, :status)
        ");
        $stmt->execute([
            'user_id'        => $data['user_id'],
            'event_id'       => $data['event_id'],
            'customer_name'  => trim($data['customer_name']),
            'customer_email' => strtolower(trim($data['customer_email'])),
            'booking_date'   => $data['booking_date'],
            'start_time'     => $data['start_time'],
            'end_time'       => $data['end_time'],
            'status'         => $data['status'] ?? 'confirmed'
        ]);

        $bookingId = (int)$this->db->lastInsertId();

        // Add log
        $stmtLog = $this->db->prepare("INSERT INTO `booking_logs` (`booking_id`, `action`, `note`) VALUES (:booking_id, 'created', 'Booking created successfully')");
        $stmtLog->execute(['booking_id' => $bookingId]);

        return $bookingId;
    }

    public function cancelBooking(int $bookingId, string $reason = ''): bool {
        $stmt = $this->db->prepare("UPDATE `bookings` SET `status` = 'cancelled', `cancellation_reason` = :reason WHERE `id` = :id");
        $res = $stmt->execute(['reason' => $reason, 'id' => $bookingId]);

        if ($res) {
            $stmtLog = $this->db->prepare("INSERT INTO `booking_logs` (`booking_id`, `action`, `note`) VALUES (:booking_id, 'cancelled', :note)");
            $stmtLog->execute(['booking_id' => $bookingId, 'note' => 'Cancelled: ' . $reason]);
        }
        return $res;
    }

    public function getExistingBookingsForDate(int $userId, string $date): array {
        $stmt = $this->db->prepare("
            SELECT `start_time`, `end_time` FROM `bookings` 
            WHERE `user_id` = :user_id AND `booking_date` = :date AND `status` != 'cancelled'
        ");
        $stmt->execute(['user_id' => $userId, 'date' => $date]);
        return $stmt->fetchAll();
    }
}
