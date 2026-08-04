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

    public static function clientAsset(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');

        return '/'.config('assets.client_static_prefix').'/'.$relativePath;
    }

    public static function normalizeMediaPath(string $path): string
    {
        $value = trim($path);

        if ($value === '') {
            return $value;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        $value = str_replace('web information', 'web-information', $value);

        $legacyClientPrefix = '/'.config('assets.legacy_client_prefix').'/';
        $clientStaticPrefix = '/'.config('assets.client_static_prefix').'/';

        if (Str::startsWith($value, $legacyClientPrefix)) {
            return $clientStaticPrefix.substr($value, strlen($legacyClientPrefix));
        }

        $uploadsDirectory = config('assets.uploads_directory');

        foreach (['/userfiles/', 'userfiles/'] as $userfilesPrefix) {
            if (Str::startsWith($value, $userfilesPrefix)) {
                return $uploadsDirectory.'/'.ltrim(substr($value, strlen($userfilesPrefix)), '/');
            }
        }

        return $value;
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

        $normalized = self::normalizeMediaPath($value);

        if (Str::startsWith($normalized, '/')) {
            return asset(ltrim($normalized, '/'));
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        return Storage::disk('public')->url($normalized);
    }

    /**
     * Get status badge HTML for booking status.
     * Delegates to BookingStatus enum for label and color to avoid drift.
     */
    public static function getBookingStatusBadge(string $status): string
    {
        $enum = \App\Enums\BookingStatus::tryFrom($status);
        $label = $enum?->getLabel() ?? ucfirst($status);
        $color = $enum?->getColor() ?? 'secondary';
        $class = self::enumColorToBadgeClass($color);

        return sprintf('<span class="badge %s">%s</span>', $class, e($label));
    }

    /**
     * Get payment status badge HTML.
     * Delegates to PaymentStatus enum for label and color.
     */
    public static function getPaymentStatusBadge(string $status, string $method = ''): string
    {
        $enum = \App\Enums\PaymentStatus::tryFrom($status);
        $label = $enum?->getLabel() ?? ucfirst($status);
        $color = $enum?->getColor() ?? 'secondary';
        $class = self::enumColorToBadgeClass($color);

        $methodText = match ($method) {
            'online_banking' => ' (SePay)',
            'cash_on_pickup' => ' (Tiền mặt)',
            default => '',
        };

        return sprintf('<span class="badge %s">%s%s</span>', $class, e($label), e($methodText));
    }

    /** Map Filament/enum color names to Tailwind badge CSS classes (single mapping point). */
    private static function enumColorToBadgeClass(string $color): string
    {
        return match ($color) {
            'success' => 'badge-success',
            'danger' => 'badge-danger',
            'warning' => 'badge-warning',
            'info' => 'badge-info',
            'primary' => 'badge-primary',
            default => 'badge-secondary',
        };
    }
}
