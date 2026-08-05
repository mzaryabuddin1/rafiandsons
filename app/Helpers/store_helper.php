<?php

if (! function_exists('category_fa_icon')) {
    /**
     * Resolve a Font Awesome 5 Free icon class for a category.
     * Prefers Qist catIcon stored in description, then slug map.
     */
    function category_fa_icon(?string $slug = null, ?string $storedIcon = null): string
    {
        $aliases = [
            'fa-snowflake-o'     => 'fa-snowflake',
            'fa-refresh'         => 'fa-sync-alt',
            'fa-sun-o'           => 'fa-sun',
            'fa-dot-circle-o'    => 'fa-dot-circle',
            'fa-phone-square'    => 'fa-mobile-alt',
            'fa-thermometer-full'=> 'fa-temperature-high',
            'fa-battery-full'    => 'fa-car-battery',
            'fa-desktop'         => 'fa-tv',
            'fa-server'          => 'fa-door-closed',
            'fa-cube'            => 'fa-tshirt',
            'fa-bicycle'         => 'fa-motorcycle',
            'fa-bars'            => 'fa-box',
            'fa-tablet'          => 'fa-tablet-alt',
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

        $slug = strtolower(trim((string) $slug));
        if ($slug !== '' && isset($bySlug[$slug])) {
            return $bySlug[$slug];
        }

        $raw = trim((string) $storedIcon);
        if ($raw !== '') {
            // Qist sometimes stores "fa fa-refresh"
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

        return 'fa-box';
    }
}
