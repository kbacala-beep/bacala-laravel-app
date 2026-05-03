<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'report_id',
        'file_path',
        'file_type',
    ];

    /**
     * An attachment belongs to a report.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}