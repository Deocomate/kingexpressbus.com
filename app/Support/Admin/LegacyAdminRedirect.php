<?php

namespace App\Support\Admin;

/**
 * Maps legacy Filament /admin paths to the Blade /quan-tri admin.
 */
final class LegacyAdminRedirect
{
    /**
     * Exact path prefixes (longest match wins). Keys have no leading slash after "admin/".
     *
     * @var array<string, string>
     */
    private const MAP = [
        'login' => '/quan-tri/dang-nhap',
        'bookings' => '/quan-tri/dat-ve',
        'trips' => '/quan-tri/chuyen-xe',
        'trip-blocks' => '/quan-tri/chuyen-xe?section=blocks',
        'routes' => '/quan-tri/tuyen-duong',
        'buses' => '/quan-tri/doi-xe',
        'bus-services' => '/quan-tri/doi-xe?section=services',
        'provinces' => '/quan-tri/dia-diem?section=provinces',
        'district-types' => '/quan-tri/dia-diem?section=district-types',
        'districts' => '/quan-tri/dia-diem?section=districts',
        'stops' => '/quan-tri/dia-diem?section=stops',
        'holiday-surcharges' => '/quan-tri/phu-thu',
        'web-profiles' => '/quan-tri/cau-hinh-website?section=profile',
        'menus' => '/quan-tri/cau-hinh-website?section=menus',
    ];

    public static function target(?string $any): string
    {
        $path = trim((string) $any, '/');

        if ($path === '') {
            return '/quan-tri';
        }

        $bestKey = null;
        $bestLen = -1;

        foreach (array_keys(self::MAP) as $key) {
            if ($path === $key || str_starts_with($path, $key.'/')) {
                $len = strlen($key);
                if ($len > $bestLen) {
                    $bestKey = $key;
                    $bestLen = $len;
                }
            }
        }

        if ($bestKey === null) {
            return '/quan-tri';
        }

        $target = self::MAP[$bestKey];

        // Pass through numeric record IDs onto edit-capable modules.
        if (preg_match('#^'.preg_quote($bestKey, '#').'/(\d+)(?:/edit)?$#', $path, $m)) {
            return match ($bestKey) {
                'bookings' => '/quan-tri/dat-ve/'.$m[1],
                'trips' => '/quan-tri/chuyen-xe/'.$m[1],
                'routes' => '/quan-tri/tuyen-duong/'.$m[1],
                'buses' => '/quan-tri/doi-xe/'.$m[1],
                'holiday-surcharges' => '/quan-tri/phu-thu/'.$m[1],
                default => $target,
            };
        }

        if (preg_match('#^'.preg_quote($bestKey, '#').'/create$#', $path)) {
            return match ($bestKey) {
                'bookings' => '/quan-tri/dat-ve/tao',
                'trips' => '/quan-tri/chuyen-xe/tao',
                'routes' => '/quan-tri/tuyen-duong/tao',
                'buses' => '/quan-tri/doi-xe/tao',
                'holiday-surcharges' => '/quan-tri/phu-thu/tao',
                'trip-blocks' => '/quan-tri/chuyen-xe/khoa/them',
                default => $target,
            };
        }

        return $target;
    }
}
