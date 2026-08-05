<?php

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table            = 'banners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'position', 'title', 'subtitle', 'description', 'badge_text', 'button_text',
        'image', 'bg_color', 'style', 'link', 'sort_order', 'status',
    ];

    public const POSITION_HOME_SLIDER = 'home_slider';
    public const POSITION_HOME_SIDE   = 'home_side';
    public const POSITION_HOME_MID    = 'home_mid';

    public static function positions(): array
    {
        return [
            self::POSITION_HOME_SLIDER => 'Home Slider',
            self::POSITION_HOME_SIDE   => 'Home Side Banner',
            self::POSITION_HOME_MID    => 'Home Mid Banner',
        ];
    }

    /**
     * Recommended upload dimensions per banner position (width × height in px).
     */
    public static function recommendedImageSizes(): array
    {
        return [
            self::POSITION_HOME_SLIDER => ['width' => 580, 'height' => 460, 'label' => '580 × 460 px'],
            self::POSITION_HOME_SIDE   => ['width' => 346, 'height' => 193, 'label' => '346 × 193 px'],
            self::POSITION_HOME_MID    => ['width' => 580, 'height' => 219, 'label' => '580 × 219 px'],
        ];
    }

    public static function recommendedImageSize(string $position): string
    {
        $sizes = self::recommendedImageSizes();

        return $sizes[$position]['label'] ?? $sizes[self::POSITION_HOME_SLIDER]['label'];
    }

    public function activeByPosition(string $position): array
    {
        return $this->where('status', 1)
            ->where('position', $position)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Resolve banner link to absolute site URL or external URL.
     */
    public static function resolveLink(?string $link): string
    {
        $link = trim((string) $link);
        if ($link === '') {
            return site_url('shop');
        }
        if (preg_match('#^https?://#i', $link)) {
            return $link;
        }

        return site_url(ltrim($link, '/'));
    }
}
