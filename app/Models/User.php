<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MODERATOR = 'moderator';

    public const ROLE_USER = 'user';

    public const ROLE_LABELS = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_MODERATOR => 'Moderator',
        self::ROLE_USER => 'User',
    ];

    private const ROLE_LEVELS = [
        self::ROLE_USER => 10,
        self::ROLE_MODERATOR => 20,
        self::ROLE_ADMIN => 30,
        self::ROLE_SUPER_ADMIN => 40,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'uploaded_by');
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? 'User';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function roleAtLeast(string $role): bool
    {
        return (self::ROLE_LEVELS[$this->role] ?? 0) >= (self::ROLE_LEVELS[$role] ?? 0);
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->roleAtLeast(self::ROLE_MODERATOR);
    }

    public function canManageTaxonomy(): bool
    {
        return $this->roleAtLeast(self::ROLE_ADMIN);
    }

    public function canDeleteProjects(): bool
    {
        return $this->roleAtLeast(self::ROLE_ADMIN);
    }

    public function canManageUsers(): bool
    {
        return $this->roleAtLeast(self::ROLE_SUPER_ADMIN);
    }

    public function manageableRoles(): array
    {
        if ($this->hasRole(self::ROLE_SUPER_ADMIN)) {
            return self::ROLE_LABELS;
        }

        return [];
    }

    public function canAssignRole(string $role): bool
    {
        return array_key_exists($role, $this->manageableRoles());
    }
}
