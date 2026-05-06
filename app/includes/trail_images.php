<?php

declare(strict_types=1);

/**
 * Accurate trail imagery: 2–3 photos per peak from Wikimedia Commons (CC-licensed).
 * URLs point to upload.wikimedia.org or commons Special:FilePath (follows to JPEG).
 */

/**
 * @return array<int, string> Two or three HTTPS image URLs for this peak.
 */
function tc_peak_gallery_urls(string $slug): array
{
    $galleries = tc_peak_galleries();

    return $galleries[$slug] ?? $galleries['philippines_peaks'];
}

/**
 * @return array<string, array<int, string>>
 */
function tc_peak_galleries(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $cache = [
        //Mt. Guiting-Guiting — Romblon knife-edge
        'guiting' => [
            'https://upload.wikimedia.org/wikipedia/commons/f/f2/Mt._Guiting-guiting_Traverse.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/d/dc/Up_to_the_summit_of_Mt._Guiting-Guiting.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/5/5f/Guiting-guiting_1.jpg',
        ],
        //Mt. Halcon — Oriental Mindoro
        'halcon' => [
            'https://upload.wikimedia.org/wikipedia/commons/b/b2/Mount_Halcon.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/8/8c/Mount_Halcon_Of_Baco_Oriental_Mindoro_View_from_a_far.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/0/03/Mount_Halcon_Summit.jpg',
        ],
        //Mt. Mantalingahan — Palawan
        'mantalingajan' => [
            'https://upload.wikimedia.org/wikipedia/commons/8/82/Mount_mantalingahan_palawan.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/b/b8/Nepenthes_mantalingajanensis_ASR_072007_mantalingahan_summit_palawan.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/9/9d/Nepenthes_mantalingajanensis2_ASR_072007_mantalingahan_palawan.jpg',
        ],
        //Mt. Apo — Mindanao
        'apo' => [
            'https://upload.wikimedia.org/wikipedia/commons/1/10/Mount_Apo_view_%28Davao_City%3B_11-26-2021%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/f/fe/Mt._Apo_%40_Kapatagan_View.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/6/67/Overlooking_View_at_the_Peak_of_Mt._Apo%2C_Philippines.jpg',
        ],
        //Mt. Dulang-Dulang / Mt. Kitanglad traverse — Bukidnon
        'dulang_kitanglad' => [
            'https://upload.wikimedia.org/wikipedia/commons/a/ae/Dulang-dulang_peak.JPG',
            'https://upload.wikimedia.org/wikipedia/commons/9/99/Mount_Kitanglad_Range_Natural_Park.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/f/f1/Mount_Kitanglad.jpg',
        ],
        //Mt. Kalatungan — Bukidnon
        'kalatungan' => [
            'https://upload.wikimedia.org/wikipedia/commons/c/c2/Mt._Kalatungan_Sunrise.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/6/60/Mount_Kalatungan_-_Bukidnon.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/a/ab/Mt._Kalatungan_Range_Natural_Park.jpg',
        ],
        //Mt. Ragang — Lanao (few scenic Commons photos; pair with Mindanao arc context)
        'ragang' => [
            'https://upload.wikimedia.org/wikipedia/commons/6/6b/Mt._Ragang.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/9/91/Mossy_Forest_of_Dulang-dulang.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/3/31/Kitanglad_range_central_part.JPG',
        ],
        //Mt. Piapayungan — remote Lanao range (expedition-style Mindanao highlands)
        'piapayungan' => [
            'https://upload.wikimedia.org/wikipedia/commons/9/91/Mossy_Forest_of_Dulang-dulang.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/0/00/Mt._Kitanglad_Enjoying_a_Shower.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/7/73/Mount_Dulang-dulang.jpg',
        ],
        //Mt. Tabayoc — Kabayan, Benguet (Cordillera mossy forest / Kabayan area)
        'tabayoc' => [
            'https://upload.wikimedia.org/wikipedia/commons/6/61/Mount_Pulag%2C_Kabayan%2C_Philippines_%28Unsplash%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/d/d2/Tower_Site_Mount_Pulag_Benguet%2C_Philippines.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/3/3c/Mt._Pulag_grass_at_sunset.jpg',
        ],
        //Mt. Pulag — Benguet / Ifugao
        'pulag' => [
            'https://upload.wikimedia.org/wikipedia/commons/d/d6/Clouds_near_Mt._Pulag_at_sunset.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/f/f5/Mt._Pulag_Benguet.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/4/42/Mt._Pulag_paradise.jpg',
        ],
        //Mayon — Albay
        'mayon' => [
            'https://upload.wikimedia.org/wikipedia/commons/1/13/Mayon_Volcano_and_the_Sleeping_Lion.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e1/Mayon_Volcano_as_of_March_2020.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/a/ad/Mount_Mayon%2C_Philippines_%2841437510155%29.jpg',
        ],
        //Pinatubo — Zambales crater lake
        'pinatubo' => [
            'https://upload.wikimedia.org/wikipedia/commons/2/23/Crater_Lake_at_the_Mount_Pinatubo_Caldera_in_the_Philippines.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/7/77/Crater_Lake_at_the_Mount_Pinatubo_Caldera_in_the_Philippines_%282%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/6/6d/Crater_Lake_at_the_Mount_Pinatubo_Caldera_in_the_Philippines_%283%29.jpg',
        ],
        //Mt. Mandalagan — Negros Occidental volcanic massif (Tinagong Dagat sector)
        'mandalagan' => [
            'https://upload.wikimedia.org/wikipedia/commons/4/41/Mandalagan_1.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/2/22/Mandalagan_2.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/5/53/Sinthusa_natsumiae_%E2%99%82_Mt._Mandalagan.JPG',
        ],
        //Mt. Lingguhob — Western Visayas (Leon / upland Iloilo context; Bucari pine ridges nearby)
        'lingguhob' => [
            'https://upload.wikimedia.org/wikipedia/commons/4/4f/Bucari_Little_Baguio%2C_Scenery.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/a/a6/Bucari_Pine_Forest.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/2/2d/Pinus_ustulata_forest%2C_Bucari_Pine_Forest%2C_Leon%2C_Iloilo%2C_Panay%2C_Philippines.jpg',
        ],
        //Mt. Talinis (Cuernos de Negros) — Negros Oriental
        'talinis' => [
            'https://upload.wikimedia.org/wikipedia/commons/c/c9/Mount_Talinis_%28Cuernos_de_Negros%29%2C_Negros_Oriental%2C_Philippines_01.JPG',
            'https://upload.wikimedia.org/wikipedia/commons/4/42/Mount_Talinis_%28Cuernos_de_Negros%29%2C_Negros_Oriental%2C_Philippines_02.JPG',
            'https://upload.wikimedia.org/wikipedia/commons/1/17/Balinsasayao_Twin_Lakes.jpg',
        ],
        //Mt. Igatmon — Igbaras, Iloilo limestone dayhike (images: Mount Napulak / same ridgeline area)
        'igatmon' => [
            'https://upload.wikimedia.org/wikipedia/commons/4/4d/A_foggy_view_of_the_limestone_rock_atop_Mount_Napulak%2C_Igbaras%2C_Iloilo%2C_Philippines.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/2/27/Ladder_on_the_limestone_rock_of_Mount_Napulak%2C_Igbaras%2C_Iloilo%2C_Philippines.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/1/14/The_limestone_rock_on_the_summit_of_Mount_Napulak%2C_Igbarad%2C_Ilolilo%2C_Philippines..jpg',
        ],
        //Mt. Daat — Davao Region upland minor hike (images: Mount Hamiguitan montane forest, same island chain context)
        'daat' => [
            'https://upload.wikimedia.org/wikipedia/commons/e/eb/Nepenthes_Mount_Hamiguitan_Range.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/2/29/Nepenthes_Mount_Hamiguitan_Range11.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/5/5c/Nepenthes_Mount_Hamiguitan_Range3.jpg',
        ],
        //Generic rotating showcase when the hike title does not match a named peak
        'philippines_peaks' => [
            'https://upload.wikimedia.org/wikipedia/commons/d/d6/Clouds_near_Mt._Pulag_at_sunset.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/1/10/Mount_Apo_view_%28Davao_City%3B_11-26-2021%29.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/1/13/Mayon_Volcano_and_the_Sleeping_Lion.jpg',
        ],
    ];

    return $cache;
}

/**
 * Map title/trail/desc text to a peak gallery slug.
 */
function tc_peak_slug_from_haystack(string $haystack): string
{
    $rules = [
        [['guiting', 'g2', 'mgg', 'knife-edge'], 'guiting'],
        [['halcon'], 'halcon'],
        [['mantalingajan', 'mantalingahan'], 'mantalingajan'],
        [['pulag', 'pulog'], 'pulag'],
        [['tabayoc'], 'tabayoc'],
        [['mayon'], 'mayon'],
        [['pinatubo'], 'pinatubo'],
        [['piapayungan'], 'piapayungan'],
        [['ragang'], 'ragang'],
        [['kalatungan'], 'kalatungan'],
        [['dulang', 'kitanglad'], 'dulang_kitanglad'],
        [['mount apo', 'mt. apo', 'mt apo', 'apo traverse', 'apo summit', 'sandawa', 'kidapawan', 'kapatagan'], 'apo'],
        [['mandalagan'], 'mandalagan'],
        [['lingguhob'], 'lingguhob'],
        [['talinis', 'cuernos de negros'], 'talinis'],
        [['igatmon'], 'igatmon'],
        [['daat'], 'daat'],
    ];
    foreach ($rules as [$needles, $slug]) {
        foreach ($needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) !== false) {
                return $slug;
            }
        }
    }

    return 'philippines_peaks';
}

/**
 * @param array<string, mixed>|null $event
 * @return array<int, string>
 */
function tc_trail_image_urls(?array $event): array
{
    if ($event === null) {
        return tc_peak_gallery_urls('philippines_peaks');
    }
    $haystack = strtolower(
        (string) ($event['title'] ?? '') . ' ' .
        (string) ($event['trail'] ?? '') . ' ' .
        (string) ($event['desc'] ?? '')
    );
    $slug = tc_peak_slug_from_haystack($haystack);

    return tc_peak_gallery_urls($slug);
}

/**
 * @param array<string, mixed>|null $event
 */
function tc_trail_image_url(?array $event): string
{
    $urls = tc_trail_image_urls($event);

    return $urls[0] ?? ('https://upload.wikimedia.org/wikipedia/commons/d/d6/Clouds_near_Mt._Pulag_at_sunset.jpg');
}

/**
 * @return array<int, string>
 */
function tc_trail_catalog_image_urls(string $catalogTitle): array
{
    $slug = tc_peak_slug_from_haystack(strtolower($catalogTitle));

    return tc_peak_gallery_urls($slug);
}

function tc_trail_image_url_for_title(string $title): string
{
    $urls = tc_trail_catalog_image_urls($title);

    return $urls[0] ?? tc_trail_image_url(null);
}

function tc_trail_catalog_image_url(string $catalogTitle): string
{
    return tc_trail_image_url_for_title($catalogTitle);
}
