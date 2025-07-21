<?php
// Email értesítés beküldés után

add_action('init', function() {
    add_action('idea_after_submission', 'ideaboard_send_notification', 10, 2);
});

function ideaboard_send_notification($post_id, $type) {
    $emails_raw = get_option('ideaboard_notification_emails');
    if (!$emails_raw) return;

    $emails = array_map('trim', explode(',', $emails_raw));
    $emails = array_filter($emails, 'is_email');
    if (empty($emails)) return;

    $subject = 'Új ötlet érkezett';
    $name = get_post_meta($post_id, 'name', true);
    $email = get_post_meta($post_id, 'email', true);
    $telefon = get_post_meta($post_id, 'telefon', true);
    $title = get_the_title($post_id);
    $content = get_post_field('post_content', $post_id);
    $gdpr = get_post_meta($post_id, 'gdpr_consent', true) ? 'igen' : 'nem';

    $body = "Új ötlet érkezett a következő adatokkal:\n\n";
    $body .= "Beküldő neve: {$name}\n";
    $body .= "Beküldő e-mail: {$email}\n";
    $body .= "Beküldő telefonszám: {$telefon}\n";
    $body .= "Ötlet címe: {$title}\n";
    $body .= "Ötlet leírása:\n{$content}\n";
    $body .= "\nAdatkezelési hozzájárulás: {$gdpr}\n";
    $body .= "\nÖtlet ID: {$post_id}\n\n";
    $body .= "Nézd meg kérlek, és hagyd jóvá, ha érdemesnek találod rá!";

    wp_mail($emails, $subject, $body);
}
