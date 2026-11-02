<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AprioriRule extends Model
{
    protected $fillable = [
        'antecedent',
        'consequent',
        'support',
        'confidence',
        'lift',
    ];

    protected function casts(): array
    {
        return [
            'antecedent' => 'array',
            'consequent' => 'array',
            'support' => 'decimal:2',
            'confidence' => 'decimal:2',
            'lift' => 'decimal:2',
        ];
    }
}
