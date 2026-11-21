<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaisineContributor extends Pivot
{
    protected $table = 'saisine_contributors';

    protected $fillable = [
        'saisine_id',
        'user_id',
        'contributor_type',
        'added_by',
    ];

    /**
     * The saisine this pivot belongs to.
     */
    public function saisine()
    {
        return $this->belongsTo(Saisine::class);
    }

    /**
     * The user (contributor/observer) in this pivot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who added this contributor.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
