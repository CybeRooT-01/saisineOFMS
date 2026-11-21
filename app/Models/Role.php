<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    // Role name constants
    public const BUSINESS_TEAM = 'business_team';
    public const RJG = 'rjg';
    public const COMPLIANCE_DIRECTOR = 'compliance_director';
    public const DRJ = 'drj';
    public const INTERNAL_CONTRIBUTOR = 'internal_contributor';
    public const OBSERVER = 'observer';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    // Relationships

    /**
     * The users that belong to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }
}
