<?php

namespace App\Models;

use Database\Factories\WebProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class WebProfile extends Model
{
    /** @use HasFactory<WebProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'profile_name',
        'is_default',
        'title',
        'description',
        'logo_url',
        'favicon_url',
        'email',
        'phone',
        'hotline',
        'whatsapp',
        'address',
        'facebook_url',
        'zalo_url',
        'map_embedded',
        'policy_content',
        'introduction_content',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $webProfile): void {
            if ($webProfile->is_default) {
                self::query()
                    ->whereKeyNot($webProfile->getKey())
                    ->update(['is_default' => false]);
            }
        });

        static::deleting(function (self $webProfile): void {
            if ($webProfile->is_default && self::query()->whereKeyNot($webProfile->getKey())->exists()) {
                throw ValidationException::withMessages([
                    'is_default' => 'Hãy đặt cấu hình khác làm mặc định trước khi xóa cấu hình hiện tại.',
                ]);
            }
        });
    }
}
