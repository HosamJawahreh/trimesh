<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    protected $fillable = [
        'filename',
        'original_file_path',
        'repaired_file_path',
        'holes_filled',
        'original_volume_cm3',
        'repaired_volume_cm3',
        'volume_change_cm3',
        'volume_change_percent',
        'original_vertices',
        'repaired_vertices',
        'original_faces',
        'repaired_faces',
        'watertight_achieved',
        'repair_method',
        'repair_notes'
    ];

    protected $casts = [
        'holes_filled' => 'integer',
        'original_volume_cm3' => 'decimal:4',
        'repaired_volume_cm3' => 'decimal:4',
        'volume_change_cm3' => 'decimal:4',
        'volume_change_percent' => 'decimal:2',
        'original_vertices' => 'integer',
        'repaired_vertices' => 'integer',
        'original_faces' => 'integer',
        'repaired_faces' => 'integer',
        'watertight_achieved' => 'boolean',
    ];
}
