<?php

namespace Modules\Agents\Models;

use Illuminate\Database\Eloquent\Model;

class AgentTransaction extends Model
{
    protected $fillable = [
        'agent_id',
        'type',
        'amount',
        'description',
        'reference_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
