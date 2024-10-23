<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'photo',
        'position_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function getPhotoUrl(): string|null
    {
        if ($this->photo) {
            return Storage::disk('public')->url("users/".$this->photo);
        }

        return null;
    }

    public static function customPaginate($count = 5, $page = 1, $offset = null): array
    {
        $query = self::with('position')
            ->orderBy('created_at', 'desc');

        if ($offset !== null) {
            $users = $query->skip($offset)->take($count)->get();
        } else {
            $users = $query->skip(($page - 1) * $count)->take($count)->get();
        }

        $totalUsers = self::count();
        $totalPages = ceil($totalUsers / $count);

        if ($offset !== null) {
            $nextOffset = ($offset + $count) < $totalUsers ? ($offset + $count) : null;
            $prevOffset = $offset > 0 ? max(0, $offset - $count) : null;
            $nextUrl = $nextOffset !== null ? URL::current() . "?offset=" . $nextOffset . "&count=" . $count : null;
            $prevUrl = $prevOffset !== null ? URL::current() . "?offset=" . $prevOffset . "&count=" . $count : null;
        } else {
            $nextUrl = $page < $totalPages ? URL::current() . "?page=" . ($page + 1) . "&count=" . $count : null;
            $prevUrl = $page > 1 ? URL::current() . "?page=" . ($page - 1) . "&count=" . $count : null;
        }

        return [
            'users' => $users,
            'pagination' => [
                'success' => true,
                'page' => (int)$page,
                'total_pages' => (int)$totalPages,
                'total_users' => (int)$totalUsers,
                'count' => (int)$count,
                'links' => [
                    'next_link' => $nextUrl,
                    'prev_link' => $prevUrl,
                ],
            ]
        ];
    }
}
