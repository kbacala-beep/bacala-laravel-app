<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Scopes\BarangayScope;
use App\Models\Barangay;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'category_id',
        'resident_name',
        'subject',
        'description',
        'status',
    ];

    /**
     * Auto-clean attachments on force delete.
     */
    protected static function booted()
    {
        static::forceDeleted(function ($report) {
            foreach ($report->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        });
        static::addGlobalScope(new BarangayScope);

        // Auto-assign barangay_id when a resident creates a report
        static::creating(function ($report) {
            if (auth()->check()) {
                $report->barangay_id = auth()->user()->barangay_id;
            }
        });

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}