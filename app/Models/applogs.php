<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class applogs extends Model
{
    use HasFactory;

    // タイムスタンプを無効にする
    public $timestamps = false;

    /**
     * The log type constants.
     */
    public const LOG_TYPE_LOGIN = 1;

    public const LOG_MESSAGES = [
        self::LOG_TYPE_LOGIN => 'ログイン',
    ];

    /**
     * プロキシーを考慮したクライアントIPアドレスを取得
     * 将来的にはヘルパー関数に移動して共通化する
     * https://qiita.com/miriwo/items/aec51864a9aa3082fee0
     */
    protected static function getClientIpAttribute(): string
    {
        $forwardedFor = request()->headers->get('X-Forwarded-For');
        $realIp = request()->headers->get('X-Real-IP');
        $clientIp = null;
        $isClientIpUnreliable = false;

        if ($forwardedFor) {
            $ipAddresses = explode(',', $forwardedFor);
            $clientIp = trim($ipAddresses[0]);
        } elseif ($realIp) {
            $clientIp = $realIp;
        }

        if (!$clientIp) {
            $clientIp = request()->ip();
            $isClientIpUnreliable = true;
        }

        return $clientIp;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'logged_at',
        'log_type',
        'log_user',
        'log_message',
        'remote_addr',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    /**
     * Set the log date to,e.
     *
     * @param  string  $value
     * @return void
     */
    public function loggedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? '' : date('Y-m-d H:i', strtotime($value)),
            set: fn ($value) => $this->attributes['logged_at'] = $value === '' ? null : $value, 
        );
    }

    /**
     * Insert a log message.
     */
    public static function insertLog(int $logType, string $logMessage): void
    {
        // 認証済みユーザーの場合はログインユーザー名を取得
        $logUser = auth()->check() ? auth()->user()->name : null;

        self::create([
            'logged_at' => now(),
            'log_type' => $logType,
            'log_user' => $logUser,
            'log_message' => $logMessage,
            'remote_addr' => self::getClientIpAttribute(),
        ]);
    }   
}
