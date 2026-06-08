<?php
declare(strict_types=1);

/**
 * Almaretna Child — Seeder traduzioni camere
 *
 * Inserisce tramite update_post_meta le traduzioni EN/DE/FR/ES
 * per titolo, estratto e contenuto di tutte e 7 le camere.
 * Eseguito una sola volta, idempotente.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

add_action('init', function (): void {
    if (get_option('alm_room_translations_seeded_v1') === '1') {
        return;
    }

    $data = [

        // ── P1-S01: Camera Singola — Piano 1 ─────────────────────────────────
        'P1-S01' => [
            'en' => [
                'title'   => 'Single Room — Floor 1',
                'excerpt' => 'Single room with private bathroom on the first floor, overlooking the Sicilian countryside. Ideal for solo travellers seeking privacy and silence on the slopes of Etna.',
                'content' => '<p>A cosy and bright room on the first floor of the villa. The en-suite bathroom ensures complete privacy. The window overlooks the Sicilian countryside with Etna in the background, perfect for a peaceful start to the day.</p><p>Its location on the first floor makes it ideal for guests who prefer to avoid stairs while enjoying the full tranquillity of the villa.</p>',
            ],
            'de' => [
                'title'   => 'Einzelzimmer — Etage 1',
                'excerpt' => 'Einzelzimmer mit eigenem Badezimmer im ersten Stock mit Blick auf die sizilianische Landschaft. Ideal für Alleinreisende, die Ruhe und Abgeschiedenheit an den Hängen des Ätna suchen.',
                'content' => '<p>Ein gemütliches und helles Zimmer im ersten Stock der Villa. Das eigene Badezimmer garantiert vollständige Privatsphäre. Das Fenster blickt auf die sizilianische Landschaft mit dem Ätna im Hintergrund – perfekt für einen ruhigen Tagesbeginn.</p><p>Die Lage im ersten Stock ist ideal für Gäste, die keine Treppen steigen möchten und dennoch die volle Stille der Villa genießen.</p>',
            ],
            'fr' => [
                'title'   => 'Chambre Simple — Étage 1',
                'excerpt' => 'Chambre simple avec salle de bain privée au premier étage, vue sur la campagne sicilienne. Idéale pour les voyageurs solitaires en quête d\'intimité et de silence sur les pentes de l\'Etna.',
                'content' => '<p>Une chambre intime et lumineuse au premier étage de la villa. La salle de bain privative garantit une totale intimité. La fenêtre donne sur la campagne sicilienne avec l\'Etna en arrière-plan, parfaite pour commencer la journée en toute sérénité.</p><p>Sa situation au premier étage en fait le choix idéal pour les hôtes qui préfèrent éviter les escaliers tout en profitant de la quiétude de la villa.</p>',
            ],
            'es' => [
                'title'   => 'Habitación Individual — Piso 1',
                'excerpt' => 'Habitación individual con baño privado en el primer piso, con vistas a la campiña siciliana. Ideal para viajeros en solitario que buscan privacidad y silencio en las laderas del Etna.',
                'content' => '<p>Una habitación acogedora y luminosa en el primer piso de la villa. El baño privado garantiza total privacidad. La ventana da a la campiña siciliana con el Etna al fondo, perfecta para comenzar el día con calma.</p><p>Su ubicación en el primer piso la hace ideal para quienes prefieren evitar las escaleras y disfrutar de la tranquilidad de la villa.</p>',
            ],
        ],

        // ── P2-M01: Matrimoniale Vista Mare A — Piano 2 ──────────────────────
        'P2-M01' => [
            'en' => [
                'title'   => 'Double Room Sea View A — Floor 2',
                'excerpt' => 'Double room with private bathroom and panoramic views of the Ionian coast from Taormina to Catania and Mount Etna.',
                'content' => '<p>One of the villa\'s most sought-after rooms: overlooking the Ionian coast, it offers a view stretching from Taormina in the north to Catania and the open sea to the south. Etna rises majestically in the background.</p><p>En-suite bathroom, double bed, silent air conditioning. Ideal for couples or romantic stays. Minimum 2 nights.</p>',
            ],
            'de' => [
                'title'   => 'Doppelzimmer Meerblick A — Etage 2',
                'excerpt' => 'Doppelzimmer mit eigenem Badezimmer und Panoramablick auf die ionische Küste von Taormina bis Catania und den Ätna.',
                'content' => '<p>Eines der beliebtesten Zimmer der Villa: Mit Blick auf die ionische Küste bietet es ein Panorama von Taormina im Norden bis nach Catania und dem offenen Meer im Süden. Der Ätna erhebt sich imposant im Hintergrund.</p><p>Eigenes Badezimmer, Doppelbett, leise Klimaanlage. Ideal für Paare oder romantische Aufenthalte. Mindestaufenthalt 2 Nächte.</p>',
            ],
            'fr' => [
                'title'   => 'Chambre Double Vue Mer A — Étage 2',
                'excerpt' => 'Chambre double avec salle de bain privée et vue panoramique sur la côte ionienne de Taormine à Catane et sur l\'Etna.',
                'content' => '<p>L\'une des chambres les plus prisées de la villa : face à la côte ionienne, elle offre un panorama allant de Taormine au nord jusqu\'à Catane et la mer ouverte au sud. L\'Etna se dresse majestueusement en arrière-plan.</p><p>Salle de bain privative, lit double, climatisation silencieuse. Idéale pour les couples ou les séjours romantiques. Minimum 2 nuits.</p>',
            ],
            'es' => [
                'title'   => 'Habitación Doble Vista Mar A — Piso 2',
                'excerpt' => 'Habitación doble con baño privado y vistas panorámicas a la costa jónica desde Taormina hasta Catania y al Etna.',
                'content' => '<p>Una de las habitaciones más solicitadas de la villa: con vistas a la costa jónica, ofrece un panorama que se extiende desde Taormina al norte hasta Catania y el mar abierto al sur. El Etna se alza imponente al fondo.</p><p>Baño privado, cama doble, aire acondicionado silencioso. Ideal para parejas o estancias románticas. Mínimo 2 noches.</p>',
            ],
        ],

        // ── P2-M02: Matrimoniale Vista Mare B — Piano 2 ──────────────────────
        'P2-M02' => [
            'en' => [
                'title'   => 'Double Room Sea View B — Floor 2',
                'excerpt' => 'Double room with private bathroom and panoramic views. Same type as Room A, ideal for groups or families booking together.',
                'content' => '<p>The mirror image of Double Room Sea View A, this room shares the same floor and the same extraordinary view over the Ionian Sea. Perfect for friends or family members occupying the adjacent room.</p><p>Independent en-suite bathroom, double bed, full amenities. The combination of rooms A+B accommodates up to 4 adults with complete privacy.</p>',
            ],
            'de' => [
                'title'   => 'Doppelzimmer Meerblick B — Etage 2',
                'excerpt' => 'Doppelzimmer mit eigenem Badezimmer und Panoramablick. Gleicher Typ wie Zimmer A, ideal für Gruppen oder Familien, die gemeinsam buchen.',
                'content' => '<p>Das Spiegelzimmer zum Doppelzimmer Meerblick A: Es teilt dieselbe Etage und dieselbe außergewöhnliche Aussicht auf das Ionische Meer. Perfekt für Freunde oder Familienmitglieder, die das benachbarte Zimmer belegen.</p><p>Unabhängiges eigenes Badezimmer, Doppelbett, vollständige Ausstattung. Die Kombination der Zimmer A+B bietet Platz für bis zu 4 Erwachsene mit vollständiger Privatsphäre.</p>',
            ],
            'fr' => [
                'title'   => 'Chambre Double Vue Mer B — Étage 2',
                'excerpt' => 'Chambre double avec salle de bain privée et vue panoramique. Même type que la chambre A, idéale pour les groupes ou les familles qui réservent ensemble.',
                'content' => '<p>Le pendant de la Chambre Double Vue Mer A, cette chambre partage le même étage et la même vue extraordinaire sur la mer Ionienne. Parfaite pour des amis ou des membres de la famille occupant la chambre adjacente.</p><p>Salle de bain privative indépendante, lit double, équipement complet. La combinaison des chambres A+B peut accueillir jusqu\'à 4 adultes en toute intimité.</p>',
            ],
            'es' => [
                'title'   => 'Habitación Doble Vista Mar B — Piso 2',
                'excerpt' => 'Habitación doble con baño privado y vistas panorámicas. Mismo tipo que la habitación A, ideal para grupos o familias que reservan juntos.',
                'content' => '<p>El espejo de la Habitación Doble Vista Mar A, esta habitación comparte la misma planta y la misma extraordinaria vista sobre el mar Jónico. Perfecta para amigos o familiares que ocupan la habitación contigua.</p><p>Baño privado independiente, cama doble, equipamiento completo. La combinación de las habitaciones A+B permite alojar hasta 4 adultos con total privacidad.</p>',
            ],
        ],

        // ── P2-FAM-M: Suite Familiare — Camera Matrimoniale ──────────────────
        'P2-FAM-M' => [
            'en' => [
                'title'   => 'Family Suite — Double Room',
                'excerpt' => 'Part of the Family Suite on Floor 2. Double room with private hallway leading to the shared bathroom with the twin room.',
                'content' => '<p>The Family Suite consists of two rooms connected by a private hallway and a shared bathroom. This is the double room of the suite: double bed, sea view, and a small sitting area in the hallway.</p><p>The suite can be booked as a whole unit (for families of up to 4) or as a standalone room; in that case the bathroom is shared with the adjacent twin room. Ideal for families with children or groups who want to stay close while having distinct spaces.</p>',
            ],
            'de' => [
                'title'   => 'Familiensuite — Doppelzimmer',
                'excerpt' => 'Teil der Familiensuite in Etage 2. Doppelzimmer mit privatem Flur, der zum gemeinsam genutzten Badezimmer mit dem Zweibettzimmer führt.',
                'content' => '<p>Die Familiensuite besteht aus zwei durch einen privaten Flur und ein gemeinsames Badezimmer verbundenen Zimmern. Dies ist das Doppelzimmer der Suite: Doppelbett, Meerblick und ein kleiner Sitzbereich im Flur.</p><p>Die Suite kann als vollständige Einheit (für Familien bis zu 4 Personen) oder als Einzelzimmer gebucht werden. Im letzteren Fall wird das Badezimmer mit dem angrenzenden Zweibettzimmer geteilt. Ideal für Familien mit Kindern oder Gruppen, die nah beieinander sein möchten.</p>',
            ],
            'fr' => [
                'title'   => 'Suite Familiale — Chambre Double',
                'excerpt' => 'Partie de la Suite Familiale à l\'étage 2. Chambre double avec couloir privé menant à la salle de bain partagée avec la chambre à deux lits.',
                'content' => '<p>La Suite Familiale est composée de deux chambres reliées par un couloir privé et une salle de bain commune. Voici la chambre double de la suite : lit double, vue mer et un petit salon dans le couloir.</p><p>La suite peut être réservée comme unité entière (pour des familles jusqu\'à 4 personnes) ou comme chambre individuelle, auquel cas la salle de bain est partagée avec la chambre adjacente à deux lits. Idéale pour les familles avec enfants ou les groupes souhaitant rester proches tout en ayant des espaces distincts.</p>',
            ],
            'es' => [
                'title'   => 'Suite Familiar — Habitación Doble',
                'excerpt' => 'Parte de la Suite Familiar en el piso 2. Habitación doble con pasillo privado que conduce al baño compartido con la habitación de dos camas.',
                'content' => '<p>La Suite Familiar se compone de dos habitaciones conectadas por un pasillo privado y un baño compartido. Esta es la habitación doble de la suite: cama doble, vistas al mar y un pequeño salón en el pasillo.</p><p>La suite puede reservarse como unidad completa (para familias de hasta 4 personas) o como habitación individual; en este caso el baño es compartido con la habitación contigua de dos camas. Ideal para familias con niños o grupos que desean estar cerca manteniendo espacios separados.</p>',
            ],
        ],

        // ── P2-FAM-D: Suite Familiare — Camera Doppia ────────────────────────
        'P2-FAM-D' => [
            'en' => [
                'title'   => 'Family Suite — Twin Room',
                'excerpt' => 'Part of the Family Suite on Floor 2. Room with two single beds and a private hallway leading to the shared bathroom.',
                'content' => '<p>The twin room of the Family Suite: two single beds, ideal for children or young adults. Connected via a private hallway to the double room and the shared bathroom.</p><p>Can be booked separately or as part of the full suite. If booked separately, the bathroom is shared with the adjacent double room. The complete suite (double + twin) sleeps up to 4 adults or 2 adults + 2 children.</p>',
            ],
            'de' => [
                'title'   => 'Familiensuite — Zweibettzimmer',
                'excerpt' => 'Teil der Familiensuite in Etage 2. Zimmer mit zwei Einzelbetten und privatem Flur, der zum gemeinsamen Badezimmer führt.',
                'content' => '<p>Das Zweibettzimmer der Familiensuite: zwei Einzelbetten, ideal für Kinder oder junge Erwachsene. Verbunden über einen privaten Flur mit dem Doppelzimmer und dem gemeinsamen Badezimmer.</p><p>Kann separat oder als Teil der vollständigen Suite gebucht werden. Bei separater Buchung wird das Badezimmer mit dem angrenzenden Doppelzimmer geteilt. Die komplette Suite (Doppelzimmer + Zweibettzimmer) bietet Platz für bis zu 4 Erwachsene oder 2 Erwachsene + 2 Kinder.</p>',
            ],
            'fr' => [
                'title'   => 'Suite Familiale — Chambre à Deux Lits',
                'excerpt' => 'Partie de la Suite Familiale à l\'étage 2. Chambre avec deux lits simples et couloir privé menant à la salle de bain commune.',
                'content' => '<p>La chambre à deux lits de la Suite Familiale : deux lits simples, idéale pour les enfants ou les jeunes adultes. Reliée via un couloir privé à la chambre double et à la salle de bain commune.</p><p>Peut être réservée séparément ou dans le cadre de la suite complète. En cas de réservation séparée, la salle de bain est partagée avec la chambre double adjacente. La suite complète (double + deux lits) peut accueillir jusqu\'à 4 adultes ou 2 adultes + 2 enfants.</p>',
            ],
            'es' => [
                'title'   => 'Suite Familiar — Habitación Twin',
                'excerpt' => 'Parte de la Suite Familiar en el piso 2. Habitación con dos camas individuales y pasillo privado que conduce al baño compartido.',
                'content' => '<p>La habitación twin de la Suite Familiar: dos camas individuales, ideal para niños o jóvenes adultos. Conectada a través de un pasillo privado con la habitación doble y el baño compartido.</p><p>Puede reservarse por separado o como parte de la suite completa. Si se reserva por separado, el baño es compartido con la habitación doble contigua. La suite completa (doble + twin) aloja hasta 4 adultos o 2 adultos + 2 niños.</p>',
            ],
        ],

        // ── MA-M01: Matrimoniale Mansarda A ──────────────────────────────────
        'MA-M01' => [
            'en' => [
                'title'   => 'Double Room Attic A',
                'excerpt' => 'Double room in the attic with exposed beam ceiling, views of Etna. Shared bathroom with the adjacent room.',
                'content' => '<p>The attic rooms are the most characterful in the villa: wooden beam ceilings, authentic atmosphere, a light that changes with the hours of the day. This one overlooks Etna: in the morning the mountain emerges through the clouds, in the evening it glows red.</p><p>The bathroom is shared with the adjacent Attic B room, for the exclusive use of the guests of the two rooms only. Minimum 2 nights.</p>',
            ],
            'de' => [
                'title'   => 'Doppelzimmer Dachgeschoss A',
                'excerpt' => 'Doppelzimmer im Dachgeschoss mit Holzbalkendecke, Blick auf den Ätna. Gemeinsames Badezimmer mit dem benachbarten Zimmer.',
                'content' => '<p>Die Dachgeschosszimmer sind die charakteristischsten der Villa: Holzbalkendecke, authentische Atmosphäre, ein Licht, das sich mit den Stunden des Tages verändert. Dieses Zimmer blickt auf den Ätna: morgens taucht der Berg aus den Wolken auf, abends leuchtet er rot.</p><p>Das Badezimmer wird mit dem benachbarten Dachgeschosszimmer B geteilt, ausschließlich für die Gäste der beiden Zimmer. Mindestaufenthalt 2 Nächte.</p>',
            ],
            'fr' => [
                'title'   => 'Chambre Double Mansarde A',
                'excerpt' => 'Chambre double en mansarde avec plafond à poutres apparentes, vue sur l\'Etna. Salle de bain partagée avec la chambre adjacente.',
                'content' => '<p>Les chambres en mansarde sont les plus caractéristiques de la villa : plafond à poutres en bois, atmosphère authentique, une lumière qui change au fil des heures. Celle-ci donne sur l\'Etna : le matin, la montagne émerge des nuages, le soir elle se teinte de rouge.</p><p>La salle de bain est partagée avec la chambre Mansarde B adjacente, à usage exclusif des seuls occupants des deux chambres. Minimum 2 nuits.</p>',
            ],
            'es' => [
                'title'   => 'Habitación Doble Buhardilla A',
                'excerpt' => 'Habitación doble en buhardilla con techo de vigas, vistas al Etna. Baño compartido con la habitación contigua.',
                'content' => '<p>Las habitaciones de la buhardilla son las más características de la villa: techo con vigas de madera, atmósfera auténtica y una luz que cambia con las horas del día. Esta da al Etna: por la mañana la montaña emerge entre las nubes, por la tarde se tiñe de rojo.</p><p>El baño es compartido con la habitación Buhardilla B contigua, de uso exclusivo para los huéspedes de las dos habitaciones. Mínimo 2 noches.</p>',
            ],
        ],

        // ── MA-M02: Matrimoniale Mansarda B ──────────────────────────────────
        'MA-M02' => [
            'en' => [
                'title'   => 'Double Room Attic B',
                'excerpt' => 'Double room in the attic with exposed beam ceiling, views of the Ionian coast. Shared bathroom with the adjacent room.',
                'content' => '<p>The second attic room: same rustic charm with exposed beams, but with the window looking towards the Ionian coast. From here you can see the blue line of the sea and, on the clearest days, the silhouette of Taormina.</p><p>The bathroom is shared with the adjacent Attic A room, reserved exclusively for the guests of the two rooms. Minimum 2 nights.</p>',
            ],
            'de' => [
                'title'   => 'Doppelzimmer Dachgeschoss B',
                'excerpt' => 'Doppelzimmer im Dachgeschoss mit Holzbalkendecke, Blick auf die ionische Küste. Gemeinsames Badezimmer mit dem benachbarten Zimmer.',
                'content' => '<p>Das zweite Dachgeschosszimmer: derselbe rustikale Charme mit Sichtbalken, aber mit dem Fenster zur ionischen Küste. Von hier aus sieht man die blaue Linie des Meeres und an den klarsten Tagen die Silhouette von Taormina.</p><p>Das Badezimmer wird mit dem benachbarten Dachgeschosszimmer A geteilt, ausschließlich für die Gäste der beiden Zimmer. Mindestaufenthalt 2 Nächte.</p>',
            ],
            'fr' => [
                'title'   => 'Chambre Double Mansarde B',
                'excerpt' => 'Chambre double en mansarde avec plafond à poutres apparentes, vue sur la côte ionienne. Salle de bain partagée avec la chambre adjacente.',
                'content' => '<p>La deuxième chambre en mansarde : même charme rustique avec poutres apparentes, mais avec la fenêtre orientée vers la côte ionienne. D\'ici, on aperçoit la ligne bleue de la mer et, par les journées les plus claires, la silhouette de Taormine.</p><p>La salle de bain est partagée avec la chambre Mansarde A adjacente, réservée exclusivement aux occupants des deux chambres. Minimum 2 nuits.</p>',
            ],
            'es' => [
                'title'   => 'Habitación Doble Buhardilla B',
                'excerpt' => 'Habitación doble en buhardilla con techo de vigas, vistas a la costa jónica. Baño compartido con la habitación contigua.',
                'content' => '<p>La segunda habitación de la buhardilla: el mismo encanto rústico con vigas vistas, pero con la ventana orientada hacia la costa jónica. Desde aquí se ve la línea azul del mar y, en los días más despejados, la silueta de Taormina.</p><p>El baño es compartido con la habitación Buhardilla A contigua, reservado exclusivamente para los huéspedes de las dos habitaciones. Mínimo 2 noches.</p>',
            ],
        ],

    ]; // end $data

    $inserted = 0;

    foreach ($data as $room_id_value => $langs) {
        $posts = get_posts([
            'post_type'      => 'almaretna_room',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => '_room_id',
                'value'   => $room_id_value,
                'compare' => '=',
            ]],
        ]);

        if (empty($posts)) {
            continue;
        }

        $post_id = $posts[0]->ID;

        foreach ($langs as $lang => $fields) {
            foreach ($fields as $field => $value) {
                update_post_meta($post_id, "_room_{$field}_{$lang}", $value);
            }
        }

        $inserted++;
    }

    if ($inserted > 0) {
        update_option('alm_room_translations_seeded_v1', '1');
    }
}, 20);
