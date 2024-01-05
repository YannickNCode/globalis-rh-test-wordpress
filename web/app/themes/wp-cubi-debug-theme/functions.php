<?php

require_once __DIR__ . '/src/schema.php';
require_once __DIR__ . '/src/registrations.php';

add_action('manage_events_posts_custom_column', 'display_export_column', 10, 2);
add_filter('manage_edit-events_columns', 'add_export_column');

function add_export_column($columns)
{
    $columns['export'] = 'Export';
    return $columns;
}

function display_export_column($column, $post_id)
{
    if ($column === 'export') {
        echo '<a href="' . admin_url('admin-ajax.php?action=export_event_registrations&event_id=' . $post_id) . '" class="button">Export</a>';
    }
}

function get_event_registrations($event_id)
{
    $registrations = array();

    $args = array(
        'post_type'      => 'registrations',
        'meta_key'       => 'registration_event_id',
        'meta_value'     => $event_id,
        'posts_per_page' => -1,
    );

    $registrations_query = new WP_Query($args);

    if ($registrations_query->have_posts()) {
        while ($registrations_query->have_posts()) {
            $registrations_query->the_post();
            $registration_data = array(
                'first_name' => get_field('registration_last_name'),
                'last_name'  => get_field('registration_first_name'),
                'email'      => get_field('registration_email'),
                'phone'      => get_field('registration_phone'),

            );
            $registrations[] = $registration_data;
        }
        wp_reset_postdata();
    }

    return $registrations;
}


add_action('wp_ajax_export_event_registrations', 'export_event_registrations');

function export_event_registrations()
{
    $event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

    $registrations = get_event_registrations($event_id);

    $xlsxWriter = new \OpenSpout\Writer\XLSX\Writer();
    $xlsxWriter->openToBrowser('event_registrations.xlsx');

    $xlsxWriter->addRow(\OpenSpout\Common\Entity\Row::fromValues(['Nom', 'Prénom', 'Email', 'Téléphone']));

    foreach ($registrations as $registration) {
        $rowValues = [
            $registration['last_name'],
            $registration['first_name'],
            $registration['email'],
            $registration['phone'],
        ];
        $xlsxWriter->addRow(\OpenSpout\Common\Entity\Row::fromValues($rowValues));
    }

    $xlsxWriter->close();
    exit();
}

