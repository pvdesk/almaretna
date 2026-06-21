<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Room
 *
 * Rappresenta una camera (CPT almaretna_room) con tutti i suoi meta.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Modello camera.
 */
class ALM_Room {

    /** @var int ID post WordPress. */
    public int $id;

    /** @var string Titolo della camera. */
    public string $title;

    /** @var string ID univoco camera (es. P1-S01). */
    public string $room_id;

    /** @var int Capacità adulti. */
    public int $capacity_adults;

    /** @var int Capacità bambini. */
    public int $capacity_children;

    /** @var float Prezzo base per notte. */
    public float $base_price;

    /** @var string Piano (piano-1, piano-2, mansarda). */
    public string $floor;

    /** @var string Tipo bagno: private | shared. */
    public string $bathroom_type;

    /** @var string Room ID della camera con bagno condiviso. */
    public string $shared_with;

    /** @var string ID camera su Beds24. */
    public string $beds24_id;

    /** @var bool True se fa parte di una suite familiare. */
    public bool $is_family_unit;

    /** @var string[] Room ID delle camere della suite familiare. */
    public array $family_unit_rooms;

    /** @var bool True se è il Piano Intero (blocca tutte le altre camere). */
    public bool $is_entire_floor;

    /** @var int Soggiorno minimo in notti. */
    public int $min_stay;

    /** @var bool Camera attiva e prenotabile. */
    public bool $active;

    /**
     * Costruttore: popola le proprietà da un WP_Post o ID.
     *
     * @param WP_Post|int $post
     */
    public function __construct(WP_Post|int $post) {
        if (is_int($post)) {
            $post = get_post($post);
        }

        if (!$post instanceof WP_Post) {
            throw new InvalidArgumentException('ALM_Room: post non valido.');
        }

        $this->id    = $post->ID;
        $this->title = $post->post_title;

        $this->room_id           = (string) get_post_meta($post->ID, '_room_id', true);
        $this->capacity_adults   = (int)    get_post_meta($post->ID, '_room_capacity_adults', true);
        $this->capacity_children = (int)    get_post_meta($post->ID, '_room_capacity_children', true);
        $this->base_price        = (float)  get_post_meta($post->ID, '_room_base_price', true);
        $this->floor             = (string) get_post_meta($post->ID, '_room_floor', true);
        $this->bathroom_type     = get_post_meta($post->ID, '_room_bathroom_type', true) ?: 'private';
        $this->shared_with       = (string) get_post_meta($post->ID, '_room_shared_with', true);
        $this->beds24_id         = (string) get_post_meta($post->ID, '_room_beds24_id', true);
        $this->is_family_unit    = (bool)   get_post_meta($post->ID, '_room_is_family_unit', true);
        $this->is_entire_floor   = (bool)   get_post_meta($post->ID, '_room_is_entire_floor', true);
        $this->min_stay          = (int)   (get_post_meta($post->ID, '_room_min_stay', true) ?: 1);
        $this->active            = get_post_meta($post->ID, '_room_active', true) !== '0';

        $raw_family = get_post_meta($post->ID, '_room_family_unit_rooms', true);
        $decoded    = $raw_family ? json_decode($raw_family, true) : [];
        $this->family_unit_rooms = is_array($decoded) ? $decoded : [];
    }

    /**
     * Costruisce un'istanza da un post ID.
     *
     * @param int $post_id
     * @return static
     */
    public static function get(int $post_id): static {
        return new static($post_id);
    }

    /**
     * Restituisce tutte le camere attive.
     *
     * @param array<string, mixed> $args  Argomenti aggiuntivi per WP_Query.
     * @return static[]
     */
    public static function get_all(array $args = []): array {
        $defaults = [
            'post_type'      => 'almaretna_room',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => '_room_active',
                    'value'   => '0',
                    'compare' => '!=',
                ],
                [
                    'key'     => '_room_active',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ];

        $query = new WP_Query(wp_parse_args($args, $defaults));
        $rooms = [];

        foreach ($query->posts as $post) {
            try {
                $rooms[] = new static($post);
            } catch (InvalidArgumentException) {
                continue;
            }
        }

        wp_reset_postdata();
        return $rooms;
    }

    /**
     * Cerca una camera per il meta _room_id.
     *
     * @param string $room_id  Es. "P2-M01"
     * @return static|null
     */
    public static function get_by_room_id(string $room_id): ?static {
        $posts = get_posts([
            'post_type'      => 'almaretna_room',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => '_room_id',
                'value'   => $room_id,
                'compare' => '=',
            ]],
        ]);

        if (empty($posts)) {
            return null;
        }

        try {
            return new static($posts[0]);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Restituisce l'URL dell'immagine in evidenza.
     *
     * @param string $size  Dimensione immagine WordPress.
     * @return string
     */
    public function get_thumbnail_url(string $size = 'large'): string {
        if (!has_post_thumbnail($this->id)) {
            return '';
        }
        $src = wp_get_attachment_image_src(get_post_thumbnail_id($this->id), $size);
        return $src ? (string) $src[0] : '';
    }

    /**
     * Restituisce le dotazioni della camera come array di WP_Term.
     *
     * @return WP_Term[]
     */
    public function get_amenities(): array {
        $terms = get_the_terms($this->id, 'amenity');
        return (is_array($terms) && !is_wp_error($terms)) ? $terms : [];
    }

    /**
     * Restituisce il permalink della camera.
     *
     * @return string
     */
    public function get_permalink(): string {
        return (string) get_permalink($this->id);
    }

    /**
     * Serializza la camera in array per REST/JSON.
     *
     * @return array<string, mixed>
     */
    public function to_array(): array {
        $amenities = array_map(
            fn(WP_Term $t): array => ['slug' => $t->slug, 'name' => $t->name],
            $this->get_amenities()
        );

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'room_id'          => $this->room_id,
            'permalink'        => $this->get_permalink(),
            'thumbnail'        => $this->get_thumbnail_url('large'),
            'capacity_adults'  => $this->capacity_adults,
            'capacity_children'=> $this->capacity_children,
            'base_price'       => $this->base_price,
            'floor'            => $this->floor,
            'bathroom_type'    => $this->bathroom_type,
            'shared_with'      => $this->shared_with,
            'is_family_unit'   => $this->is_family_unit,
            'family_unit_rooms'=> $this->family_unit_rooms,
            'is_entire_floor'  => $this->is_entire_floor,
            'min_stay'         => $this->min_stay,
            'active'           => $this->active,
            'amenities'        => $amenities,
        ];
    }

    /**
     * Indica se la camera fa parte di una suite familiare.
     *
     * @return bool
     */
    public function is_family_unit(): bool {
        return $this->is_family_unit;
    }

    /**
     * Restituisce l'altra camera della suite familiare, o null.
     *
     * @return static|null
     */
    public function get_family_partner(): ?static {
        if (!$this->is_family_unit || empty($this->family_unit_rooms)) {
            return null;
        }

        foreach ($this->family_unit_rooms as $partner_room_id) {
            if ($partner_room_id !== $this->room_id) {
                return static::get_by_room_id($partner_room_id);
            }
        }

        return null;
    }

    /**
     * Restituisce la camera con cui condivide il bagno, o null.
     *
     * @return static|null
     */
    public function get_bathroom_partner(): ?static {
        if ($this->bathroom_type !== 'shared' || empty($this->shared_with)) {
            return null;
        }
        return static::get_by_room_id($this->shared_with);
    }

    /**
     * Getter magico per retrocompatibilità con name e post_id.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key) {
        if ($key === 'name') {
            return $this->title;
        }
        if ($key === 'post_id') {
            return $this->id;
        }
        trigger_error(sprintf('Proprietà indefinita: ALM_Room::$%s', $key), E_USER_WARNING);
        return null;
    }

    /**
     * isset magico per retrocompatibilità.
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool {
        return in_array($key, ['name', 'post_id'], true);
    }
}
