<?php
/**
 * Plugin Name: IdeaBoard Lite
 * Description: Két-boardos ötletbeküldő rendszer AJAX-os, e-mail és név alapú szavazással.
 * Author: Business Bloom Dev
 * Version: 4.2
 */

if (!defined('IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN')) {
    define('IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN', 'example.com');
}

// 1. Custom Post Type: Ideas
add_action('init', function() {
    register_post_type('idea', [
        'labels' => [
            'name' => 'Ötletek',
            'singular_name' => 'Ötlet'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title','editor','custom-fields'],
        'rewrite' => ['slug' => 'otletek'],
    ]);

    register_taxonomy('board', 'idea', [
        'labels' => [
            'name' => 'Boardok',
            'singular_name' => 'Board'
        ],
        'public' => true,
        'rewrite' => ['slug' => 'board']
    ]);
});

// 2. Admin oszlopok
add_filter('manage_idea_posts_columns', function($columns) {
    $columns['name'] = 'Beküldő neve';
    $columns['email'] = 'Beküldő e-mail';
    $columns['votes'] = 'Szavazatok';
    return $columns;
});

add_action('manage_idea_posts_custom_column', function($column, $post_id) {
    if ($column == 'votes') {
        echo intval(get_post_meta($post_id, 'votes', true));
    }
    if ($column == 'name') {
        echo esc_html(get_post_meta($post_id, 'name', true));
    }
    if ($column == 'email') {
        echo esc_html(get_post_meta($post_id, 'email', true));
    }
}, 10, 2);

// 3. Beküldő űrlapok
// Shortcode: ügyfél beküldő űrlap
add_shortcode('idea_form_client', function() {
    ob_start();
    if (isset($_GET['submitted']) && $_GET['submitted'] == '1') {
        echo '<div style="color:green">Ötlet sikeresen beküldve!</div>';
    }
    ?>
    <form class="idea-submit-form" method="post">
        <label>Név:<br><input type="text" name="name" required></label>
        <div class="ideaboard-row">
            <label>E-mail cím:<br><input type="email" name="email" required></label>
            <label>Telefonszám:<br><input type="text" name="telefon"></label>
        </div>
        
        <small class="ideahint">Adatai megadására azért van szükségünk, hogy pontosítás esetén az ötlettel kapcsolatban megkereshessük.</small>
        <label>Ötlet címe:<br><input type="text" name="title" required></label>
        <label>Ötlet leírása:<br><textarea name="content" required></textarea></label>
        <label class="checkboxlabel"><input type="checkbox" name="gdpr" required> Elolvastam és elfogadom az <a href="https://example.com/adatkezelesi-tajekoztato/" target="_blank">adatkezelési tájékoztatót</a>.</label>
        <input type="hidden" name="board" value="ugyfel">
        <input type="hidden" name="idea_submit_client" value="1">
        <input type="text" name="website_hp" style="display:none"> <!-- Honeypot mező -->
        <input type="submit" value="Beküldés">
    </form>
    <?php return ob_get_clean();
});
// Shortcode: dolgozói beküldő űrlap
add_shortcode('idea_form_employee', function() {
    ob_start();
    if (isset($_GET['submitted']) && $_GET['submitted'] == '1') {
        echo '<div style="color:green">Ötlet sikeresen beküldve!</div>';
    }
    ?>
    <form class="idea-submit-form" method="post" onsubmit="return validateEmployeeEmail(this)">
        <label>Név:<br><input type="text" name="name" required></label>
        <div class="ideaboard-row">
            <label>Céges e-mail cím:<br><input type="email" name="email" required></label>
            <label>Telefonszám:<br><input type="text" name="telefon"></label>
        </div>
        <small class="ideahint">Adatai megadására azért van szükségünk, hogy pontosítás esetén az ötlettel kapcsolatban megkereshessük.</small>
        <label>Ötlet címe:<br><input type="text" name="title" required></label>
        <label>Ötlet leírása:<br><textarea name="content" required></textarea></label>
        <label class="checkboxlabel"><input type="checkbox" name="gdpr" required> Elolvastam és elfogadom az <a href="https://example.com/adatkezelesi-tajekoztato/" target="_blank">adatkezelési tájékoztatót</a>.</label>
        <input type="hidden" name="board" value="dolgozo">
        <input type="hidden" name="idea_submit_employee" value="1">
        <input type="text" name="website_hp" style="display:none"> <!-- Honeypot mező -->
        <input type="submit" value="Beküldés">
    </form>
    <script>
    function validateEmployeeEmail(form) {
        var email = form.email.value.trim();
        if (!email.endsWith('@' + ideaVote.employee_domain)) {
            alert('Csak ' + ideaVote.employee_domain + ' végű e-mail címmel küldhető be.');
            return false;
        }
        return true;
    }
    </script>
    <?php return ob_get_clean();
});

function idea_handle_submission($type) {
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $telefon = isset($_POST['telefon']) ? sanitize_text_field($_POST['telefon']) : '';
    $title = sanitize_text_field($_POST['title']);
    $content = sanitize_textarea_field($_POST['content']);
    $board = sanitize_text_field($_POST['board']);
    $gdpr = isset($_POST['gdpr']) ? 1 : 0;

    $domain = get_option('ideaboard_employee_email_domain', IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN);
    if ($type === 'employee' && !preg_match('/@' . preg_quote($domain, '/') . '$/i', $email)) {
        wp_die('Csak ' . $domain . ' végű e-mail címmel küldhető be.');
    }

    $post_id = wp_insert_post([
        'post_type' => 'idea',
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'draft'
    ]);

    if ($post_id) {
        wp_set_object_terms($post_id, $board, 'board');
        update_post_meta($post_id, 'name', $name);
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'telefon', $telefon);
        update_post_meta($post_id, 'votes', 0);
        update_post_meta($post_id, 'vote_data', []);
        update_post_meta($post_id, 'gdpr_consent', $gdpr);

    }

    do_action('idea_after_submission', $post_id, $type);
    
    wp_redirect(add_query_arg('submitted', '1', get_permalink()));
    exit;
}

add_action('wp_ajax_nopriv_idea_vote', 'handle_idea_vote');
add_action('wp_ajax_idea_vote', 'handle_idea_vote');

function handle_idea_vote() {
    if (!empty($_POST['website'])) {
        wp_send_json_error('Spam gyanú.');
        exit;
    }

    $id = intval($_POST['idea_id']);
    $email = sanitize_email($_POST['email']);
    $name = sanitize_text_field($_POST['name']);

    if (!$id) {
        wp_send_json_error('Hibás adatok.');
    }

    $board_terms = wp_get_post_terms($id, 'board', ['fields' => 'slugs']);
    $domain = get_option('ideaboard_employee_email_domain', IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN);
    if ($email && in_array('dolgozo', $board_terms) && !preg_match('/@' . preg_quote($domain, '/') . '$/i', $email)) {
        wp_send_json_error('Csak ' . $domain . ' végű e-mail címmel szavazhatsz.');
    }

    $data = get_post_meta($id, 'vote_data', true);
    if (!$data) $data = [];

    if ($email) {
        foreach ($data as $entry) {
            if (!empty($entry['email']) && $entry['email'] === $email) {
                wp_send_json_error('Már szavaztál.');
            }
        }
    }

    if ($email || $name) {
        $data[] = ['name' => $name, 'email' => $email];
        update_post_meta($id, 'vote_data', $data);
    }

    $votes = (int) get_post_meta($id, 'votes', true);
    update_post_meta($id, 'votes', $votes + 1);

    wp_send_json_success(['votes' => $votes + 1]);
}

// 6. Admin szavazás log
add_action('add_meta_boxes', function() {
    add_meta_box('vote_logs', 'Szavazók', function($post) {
        $data = get_post_meta($post->ID, 'vote_data', true);
        if ($data && is_array($data)) {
            echo '<ul>';
            foreach ($data as $entry) {
                echo '<li>' . esc_html($entry['name']) . ' (' . esc_html($entry['email']) . ')</li>';
            }
            echo '</ul>';
        } else {
            echo 'Még nincs névvel beküldött szavazás. A szavazatok számát fent a votes mező mellett láthatod.';
        }
    }, 'idea');
});

// 7. Shortcode: lista
add_shortcode('idea_list', function($atts) {
    $board = sanitize_text_field($atts['board']);
    $q = new WP_Query([
        'post_type' => 'idea',
        'tax_query' => [[
            'taxonomy' => 'board',
            'field' => 'slug',
            'terms' => $board
        ]],
        'orderby' => 'meta_value_num',
        'meta_key' => 'votes',
        'order' => 'DESC',
        'posts_per_page' => -1
    ]);
    ob_start();
    while($q->have_posts()) { $q->the_post();
        $id = get_the_ID();
        $votes = get_post_meta($id, 'votes', true) ?: 0;
        echo '<div class="idea-card">';
        echo '<h2 class="idea-title">' . get_the_title() . '</h2>';
        echo '<div class="idea-description">' . get_the_content() . '</div>';
        echo '<div class="idea-votes">Szavazatok: <span id="vote-count-' . $id . '">' . intval($votes) . '</span></div>';
        echo '<form class="vote-form" data-board="' . esc_attr($board) . '">';
        echo '<label class="checkboxlabel"><input type="checkbox" class="provide-data"> Megadom az adataimat</label>';
        echo '<div class="vote-data-fields" style="display:none">';
        echo '<input type="text" id="vote-name-'.$id.'" placeholder="Név">';
        echo '<input type="email" id="vote-email-'.$id.'" placeholder="E-mail cím">';
        echo '<label class="checkboxlabel gdpr-field"><input type="checkbox" name="gdpr"> Elolvastam és elfogadom az <a target="_blank" href="https://example.com/adatkezelesi-tajekoztato/.pdf">adatkezelési tájékoztatót</a>.</label>';
        echo '</div>';
        echo '<input type="text" name="website" style="display:none"> <!-- Honeypot mező -->';
        echo '<div class="clr"></div>';
        echo '<button type="submit" class="vote-button" data-id="'.$id.'">Szavazok</button>';
        echo '</form></div>';

    }
    wp_reset_postdata();
    return ob_get_clean();
});


// GDPR
add_filter('comment_cookie_lifetime', function() { return 0; });
add_filter('user_contactmethods', function($methods) {
    unset($methods['aim'], $methods['jabber'], $methods['yim']);
    return $methods;
});

// Frontend CSS és JS betöltése külön
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('ideaboard-style', plugin_dir_url(__FILE__) . 'ideaboard-style.css');
    wp_enqueue_script('idea-vote', plugin_dir_url(__FILE__) . 'vote.js', ['jquery'], null, true);
    wp_localize_script('idea-vote', 'ideaVote', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'employee_domain' => get_option('ideaboard_employee_email_domain', IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN)

    ]);
});

// Honeypot validálás vote formnál (AJAX oldalon)
add_action('wp_ajax_idea_vote', 'handle_idea_vote');
add_action('wp_ajax_nopriv_idea_vote', 'handle_idea_vote');

function honeypot_check() {
    if (!empty($_POST['website'])) {
        wp_send_json_error('Spam gyanú.');
        exit;
    }
}

// Honeypot validálás beküldő űrlapnál
add_action('template_redirect', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['website_hp'])) {
            wp_die('Spam gyanú.');
        }
        if (isset($_POST['idea_submit_client'])) {
            idea_handle_submission('client');
        }
        if (isset($_POST['idea_submit_employee'])) {
            idea_handle_submission('employee');
        }
    }
});

// Saját admin menü
add_action('admin_menu', function() {
    add_menu_page('IdeaBoard', 'IdeaBoard', 'manage_options', 'ideaboard-main', function() {
        echo '<h1>IdeaBoard rendszer</h1><p>Az admin oszlopoknál és listáknál kezelheted az ötleteket.</p>';
    }, 'dashicons-lightbulb', 25);

    add_submenu_page('ideaboard-main', 'Beállítások', 'Beállítások', 'manage_options', 'ideaboard-settings', 'ideaboard_settings_page');
});

add_action('admin_init', function() {
    register_setting('ideaboard_options', 'ideaboard_notification_emails');
    register_setting('ideaboard_options', 'ideaboard_employee_email_domain');

});

function ideaboard_settings_page() {
    ?>
    <div class="wrap">
        <h1>IdeaBoard Beállítások</h1>
        <form method="post" action="options.php">
            <?php settings_fields('ideaboard_options'); ?>
            <?php do_settings_sections('ideaboard_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="ideaboard-row">Értesítési e-mail címek (vesszővel elválasztva)</th>
                    <td><input type="text" name="ideaboard_notification_emails" value="<?php echo esc_attr(get_option('ideaboard_notification_emails')); ?>" size="50"></td>
                </tr>
                <tr valign="top">
                    <th scope="ideaboard-row">Dolgozói e-mail domain</th>
                    <td><input type="text" name="ideaboard_employee_email_domain" value="<?php echo esc_attr(get_option('ideaboard_employee_email_domain', IDEABOARD_DEFAULT_EMPLOYEE_DOMAIN)); ?>" size="30"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Include notification funkció külön fájlból
include plugin_dir_path(__FILE__) . 'notifications.php';
