<?php

if (! function_exists('category_fa_icon')) {
    /**
     * Resolve a Font Awesome 5 Free icon class for a category.
     * Prefers categories.icon, then legacy description, then slug map.
     */
    function category_fa_icon(?string $slug = null, ?string $storedIcon = null): string
    {
        $aliases = [
            'fa-snowflake-o'      => 'fa-snowflake',
            'fa-refresh'          => 'fa-sync-alt',
            'fa-sun-o'            => 'fa-sun',
            'fa-dot-circle-o'     => 'fa-dot-circle',
            'fa-phone-square'     => 'fa-mobile-alt',
            'fa-thermometer-full' => 'fa-temperature-high',
            'fa-battery-full'     => 'fa-car-battery',
            'fa-desktop'          => 'fa-tv',
            'fa-server'           => 'fa-door-closed',
            'fa-cube'             => 'fa-tshirt',
            'fa-bicycle'          => 'fa-motorcycle',
            'fa-bars'             => 'fa-box',
            'fa-tablet'           => 'fa-tablet-alt',
        ];

        $bySlug = [
            'electronics'      => 'fa-mobile-alt',
            'home-appliances'  => 'fa-blender',
            'computers'        => 'fa-laptop',
            'mobiles'          => 'fa-mobile-alt',
            'laptops'          => 'fa-laptop',
            'led-tv'           => 'fa-tv',
            'refrigerator'     => 'fa-door-closed',
            'washing-machine'  => 'fa-tshirt',
            'air-conditioner'  => 'fa-snowflake',
            'small-appliances' => 'fa-th-large',
            'microwave-oven'   => 'fa-temperature-high',
            'water-dispenser'  => 'fa-tint',
            'fans'             => 'fa-wind',
            'bikes'            => 'fa-motorcycle',
            'deep-freezer'     => 'fa-box',
            'batteries'        => 'fa-car-battery',
            'mattress'         => 'fa-bed',
            'tyres'            => 'fa-circle',
            'tablet'           => 'fa-tablet-alt',
            'solar'            => 'fa-sun',
        ];

        $raw = trim((string) $storedIcon);
        if ($raw !== '') {
            $parts = preg_split('/\s+/', $raw) ?: [];
            $class = '';
            foreach ($parts as $part) {
                if (str_starts_with($part, 'fa-')) {
                    $class = $part;
                    break;
                }
            }
            if ($class !== '') {
                return $aliases[$class] ?? $class;
            }
        }

        $slug = strtolower(trim((string) $slug));
        if ($slug !== '' && isset($bySlug[$slug])) {
            return $bySlug[$slug];
        }

        return 'fa-box';
    }
}

if (! function_exists('shop_query_url')) {
    /**
     * Build shop URL preserving current filters (q, category, sort).
     */
    function shop_query_url(array $overrides = [], array $remove = []): string
    {
        $request = service('request');
        $params  = [
            'q'         => trim((string) $request->getGet('q')),
            'category'  => trim((string) $request->getGet('category')),
            'sort'      => (string) $request->getGet('sort'),
            'min_price' => trim((string) $request->getGet('min_price')),
            'max_price' => trim((string) $request->getGet('max_price')),
        ];

        foreach ($remove as $key) {
            unset($params[$key]);
        }

        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }

        $params = array_filter($params, static fn ($v) => $v !== '' && $v !== null);
        $query  = $params !== [] ? '?' . http_build_query($params) : '';

        return site_url('shop') . $query;
    }
}
