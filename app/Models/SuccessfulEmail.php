<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuccessfulEmail extends Model
{
    use HasFactory, SoftDeletes;

    const RAW_TEXT_COLUMN = 'raw_text';
    const EMAIL_COLUMN = 'email';
    const CHUNK_SIZE = 100;

    protected $fillable = [
        'affiliate_id', 'envelope', 'from', 'subject', 'dkim', 'SPF',
        'spam_score', 'email', 'raw_text', 'sender_ip', 'to', 'timestamp'
    ];
}
