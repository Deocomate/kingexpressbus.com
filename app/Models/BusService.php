<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusService extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'icon',
        'priority',
    ];

    public function buses(): BelongsToMany
    {
        return $this->belongsToMany(Bus::class, 'bus_bus_service');
    }
}
