<?php

namespace App\Models;

use App\Enums\ReportStatusEnum;
use App\Models\Events\ReportCreated;
use App\Models\Events\ReportCreating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * BotMessage
 *
 * @mixin Builder
 */
class Report extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['status', 'content', 'utility_id', 'project_id'];

    protected $dispatchesEvents = [
        'creating' => ReportCreating::class,
        'created'  => ReportCreated::class,
    ];

    public static function getStatuses()
    {
        return ReportStatusEnum::cases();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function utility(): BelongsTo
    {
        return $this->belongsTo(Utility::class);
    }

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }
}
