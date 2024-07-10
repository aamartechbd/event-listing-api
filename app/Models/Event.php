<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title',     
    'user_id', 
    'event_type',
    'country',
    'venue',
    'event_date',
    'event_time',
    'category',
    'website_link',
    'description',
    'video_link',
    'featured_photo'];
}
