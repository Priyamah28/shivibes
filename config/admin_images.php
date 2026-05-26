<?php

/**
 * Admin image upload specs — tuned for Shivibes storefront (mobile + laptop).
 *
 * ratio = width / height
 */
return [

    'presets' => [

        'product' => [
            'label' => 'Product image',
            'ratio' => 1.0,
            'ratio_label' => '1:1 square',
            'recommended' => ['width' => 1200, 'height' => 1200],
            'min' => ['width' => 800, 'height' => 800],
            'max' => ['width' => 2400, 'height' => 2400],
            'max_kb' => 2048,
            'ratio_tolerance' => 0.06,
            'disk_folder' => 'products',
            'mimes' => ['jpeg', 'jpg', 'png', 'webp'],
            'devices' => 'Product cards (mobile 2-col grid) and product detail gallery.',
            'tips' => 'Center the product in frame. Avoid text too close to edges — square crops on listing pages.',
        ],

        'banner_home_hero' => [
            'label' => 'Homepage hero slide',
            'ratio' => 16 / 9,
            'ratio_label' => '16:9 wide',
            'recommended' => ['width' => 1920, 'height' => 1080],
            'min' => ['width' => 1200, 'height' => 675],
            'max' => ['width' => 2560, 'height' => 1440],
            'max_kb' => 5120,
            'ratio_tolerance' => 0.05,
            'disk_folder' => 'banners',
            'mimes' => ['jpeg', 'jpg', 'png', 'webp'],
            'devices' => 'Full-width hero (~70vh tall on laptop, full-bleed on mobile).',
            'tips' => 'Keep faces and products in the center-left (text overlays on the left). Use high contrast.',
        ],

        'banner_promo_strip' => [
            'label' => 'Promo strip banner',
            'ratio' => 21 / 9,
            'ratio_label' => '21:9 ultra-wide',
            'recommended' => ['width' => 1920, 'height' => 823],
            'min' => ['width' => 1200, 'height' => 514],
            'max' => ['width' => 2560, 'height' => 1097],
            'max_kb' => 4096,
            'ratio_tolerance' => 0.06,
            'disk_folder' => 'banners',
            'mimes' => ['jpeg', 'jpg', 'png', 'webp'],
            'devices' => 'Wide shallow banners in promo areas.',
            'tips' => 'Important content in the horizontal center third.',
        ],

        'category' => [
            'label' => 'Category image',
            'ratio' => 4 / 3,
            'ratio_label' => '4:3 landscape',
            'recommended' => ['width' => 1200, 'height' => 900],
            'min' => ['width' => 800, 'height' => 600],
            'max' => ['width' => 2000, 'height' => 1500],
            'max_kb' => 2048,
            'ratio_tolerance' => 0.06,
            'disk_folder' => 'categories',
            'mimes' => ['jpeg', 'jpg', 'png', 'webp'],
            'devices' => 'Homepage category tiles (fixed height, full width crop).',
            'tips' => 'Landscape photos work best. Avoid portrait orientation.',
        ],

    ],

];
