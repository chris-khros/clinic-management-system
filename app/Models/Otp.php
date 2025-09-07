<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'type',
        'expires_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid (not expired and not used)
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Generate a new OTP code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for email verification
     */
    public static function createForEmail(string $email, string $type = 'verification'): self
    {
        // Invalidate any existing OTPs for this email and type
        self::where('email', $email)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true, 'used_at' => now()]);

        return self::create([
            'email' => $email,
            'otp_code' => self::generateCode(),
            'type' => $type,
            'expires_at' => now()->addMinutes(10), // OTP expires in 10 minutes
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verify(string $email, string $code, string $type = 'verification'): bool
    {
        $otp = self::where('email', $email)
            ->where('otp_code', $code)
            ->where('type', $type)
            ->where('is_used', false)
            ->first();

        if (!$otp || !$otp->isValid()) {
            return false;
        }

        $otp->markAsUsed();
        return true;
    }
}
