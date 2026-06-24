<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * System-wide helper functions for common operations.
 */
class SystemHelper
{
    /**
     * Format price to Vietnamese currency format.
     */
    public static function formatPrice(int|float $price): string
    {
        return number_format($price, 0, ',', '.').'đ';
    }

    /**
     * Format price without currency suffix.
     */
    public static function formatNumber(int|float $number): string
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * Generate unique booking code.
     */
    public static function generateBookingCode(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = Str::upper(Str::random(16));

            if (! DB::table('bookings')->where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate unique booking code');
    }

    /**
     * Format duration from minutes to readable format.
     */
    public static function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}p";
        } elseif ($hours > 0) {
            return "{$hours} giờ";
        } else {
            return "{$mins} phút";
        }
    }

    /**
     * Format time from H:i:s to H:i.
     */
    public static function formatTime(string $time): string
    {
        return date('H:i', strtotime($time));
    }

    /**
     * Format date from Y-m-d to d/m/Y.
     */
    public static function formatDate(string $date): string
    {
        return date('d/m/Y', strtotime($date));
    }

    /**
     * Parse date from d/m/Y to Y-m-d.
     */
    public static function parseDate(string $date): string
    {
        $parsed = \DateTime::createFromFormat('d/m/Y', $date);

        return $parsed ? $parsed->format('Y-m-d') : date('Y-m-d');
    }

    public static function mediaUrl(?string $path, ?string $fallback = null): ?string
    {
        $value = trim((string) $path);

        if ($value === '') {
            return $fallback;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        if (Str::startsWith($value, '/')) {
            return asset(ltrim($value, '/'));
        }

        if (Str::startsWith($value, 'storage/')) {
            return asset($value);
        }

        return Storage::disk('public')->url($value);
    }

    /**
     * Get status badge HTML for booking status.
     */
    public static function getBookingStatusBadge(string $status): string
    {
        $config = [
            'pending' => ['class' => 'badge-warning', 'text' => 'Chờ xác nhận'],
            'confirmed' => ['class' => 'badge-success', 'text' => 'Đã xác nhận'],
            'cancelled' => ['class' => 'badge-danger', 'text' => 'Đã hủy'],
            'completed' => ['class' => 'badge-primary', 'text' => 'Hoàn thành'],
        ];

        $data = $config[$status] ?? ['class' => 'badge-secondary', 'text' => ucfirst($status)];

        return sprintf('<span class="badge %s">%s</span>', $data['class'], $data['text']);
    }

    /**
     * Get payment status badge HTML.
     */
    public static function getPaymentStatusBadge(string $status, string $method = ''): string
    {
        $config = [
            'unpaid' => ['class' => 'badge-warning', 'text' => 'Chưa thanh toán'],
            'paid' => ['class' => 'badge-success', 'text' => 'Đã thanh toán'],
        ];

        $data = $config[$status] ?? ['class' => 'badge-secondary', 'text' => ucfirst($status)];
        $methodText = match ($method) {
            'online_banking' => ' (SePay)',
            'cash_on_pickup' => ' (Tiền mặt)',
            default => '',
        };

        return sprintf('<span class="badge %s">%s%s</span>', $data['class'], $data['text'], $methodText);
    }
}
