<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = ['ticket_id', 'recipient', 'subject', 'source', 'status', 'error'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
