<?php

require_once __DIR__ . '/src/schema.php';
require_once __DIR__ . '/src/registrations.php';

add_action('pre_post_update', 'pre_save_registration_email');

function pre_save_registration_email($post_id) {
    if (isset($_POST['acf'])) {
        $acf_data = $_POST['acf'];
        
        if (isset($acf_data['field_64749cde33fd7'])) {
            $event_id = $acf_data['field_64749cde33fd7'];
        }

        if (isset($acf_data['field_64749cfff238e'])) {
            $last_name = $acf_data['field_64749cfff238e'];
        }

        if (isset($acf_data['field_64749d4bf238f'])) {
            $first_name = $acf_data['field_64749d4bf238f'];
        }

        if (isset($acf_data['field_64749d780cd14'])) {
            $user_email = $acf_data['field_64749d780cd14'];
        }

        if (isset($acf_data['field_64749d920cd15'])) {
            $phone = $acf_data['field_64749d920cd15'];
        }

        $event_pdf_entrance_ticket_id = get_field('event_pdf_entrance_ticket', $event_id);

        $event_pdf_url = wp_get_attachment_url($event_pdf_entrance_ticket_id);

        $subject = 'Détails de votre inscription à l\'événement';
        $message = 'Bonjour ' . $first_name . ' ' . $last_name . ',<br><br>';
        $message .= 'Merci de votre inscription à notre événement. Voici les détails :<br>';
        $message .= 'Nom de l\'événement : ' . get_the_title($event_id) . '<br>';
        $message .= 'Date de l\'événement : ' . get_field('event_date', $event_id) . '<br>';
        $message .= 'Heure de l\'événement : ' . get_field('event_time', $event_id) . '<br><br>';
        $message .= 'Veuillez trouver votre billet d\'entrée en pièce jointe.<br>';

        $to = $user_email;

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $attachments = array($event_pdf_url);

        $mail_sent = wp_mail($to, $subject, $message, $headers, $attachments);

        if ($mail_sent) {
            wp_redirect(admin_url('edit.php?post_type=registrations'));
            exit();
         } else {
            $error_message = 'Une erreur est survenue lors de l\'envoi de l\'e-mail.';
            wp_die($error_message);
         }
    }
}
