<?php

/**
 * Email List & Dashboard Access
 *
 * @package     EmailListDashboardAccess
 * @author      Henri Susanto
 * @copyright   2022 Henri Susanto
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Email List & Dashboard Access
 * Plugin URI:  https://github.com/susantohenri/email-list-dashboard-access
 * Description: Formidable add on for rbundle to build email list & dashboard access
 * Version:     1.0.0
 * Author:      Henri Susanto
 * Author URI:  https://github.com/susantohenri/
 * Text Domain: EmailListDashboardAccess
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define('ELDA_CSV_FILE_SAMPLE', plugin_dir_url(__FILE__) . 'elda-sample.csv');
define('ELDA_CSV_FILE_ACTIVE', plugin_dir_url(__FILE__) . 'elda-active.csv');
define('ELDA_CSV_FILE', plugin_dir_path(__FILE__) . 'elda-active.csv');
define('ELDA_CSV_FILE_SUBMIT', 'elda-submit');
define('ELDA_LATEST_CSV_OPTION', 'elda-last-uploaded-csv');

add_action('admin_menu', function () {
    add_menu_page('Email List & Dashboard Access', 'Email List & Dashboard Access', 'administrator', __FILE__, function () {
        if ($_FILES) {
            if ($_FILES[ELDA_CSV_FILE_SUBMIT]['tmp_name']) {
                move_uploaded_file($_FILES[ELDA_CSV_FILE_SUBMIT]['tmp_name'], ELDA_CSV_FILE);
                update_option(ELDA_LATEST_CSV_OPTION, $_FILES[ELDA_CSV_FILE_SUBMIT]['name']);
            }
        }
?>
        <div class="wrap">
            <h1>Email List & Dashboard Access</h1>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="">
                        <div class="meta-box-sortables">
                            <div id="dashboard_quick_press" class="postbox ">
                                <div class="postbox-header">
                                    <h2 class="hndle ui-sortable-handle">
                                        <span>Email List & Dashboard Access CSV</span>
                                        <div>
                                            <?php if (file_exists(ELDA_CSV_FILE)) : ?>
                                                <a class="button button-primary" href="<?= site_url() . '/wp-json/email-list-dashboard-access/v1/download-latest' ?>" style="text-decoration:none;">Export Current CSV</a>
                                            <?php endif ?>
                                            <a class="button button-primary" href="<?= site_url() . '/wp-json/email-list-dashboard-access/v1/download-sample' ?>" style="text-decoration:none;">Download Empty CSV Sample File</a>
                                        </div>
                                    </h2>
                                </div>
                                <div class="inside">
                                    <form name="post" action="" method="post" class="initial-form" enctype="multipart/form-data">
                                        <div class="input-text-wrap" id="title-wrap">
                                            <label> Last Uploaded CSV File Name: </label>
                                            <b><?= get_option(ELDA_LATEST_CSV_OPTION) ?></b>
                                        </div>
                                        <div class="input-text-wrap" id="title-wrap">
                                            <label for="title"> Choose New CSV File </label>
                                            <input type="file" name="<?= ELDA_CSV_FILE_SUBMIT ?>">
                                        </div>
                                        <p>
                                            <input type="submit" name="save" class="button button-primary" value="Upload Selected CSV">
                                            <br class="clear">
                                        </p>
                                    </form>
                                </div>
                            </div>
                            <div id="dashboard_quick_press" class="postbox ">
                                <div class="postbox-header">
                                    <h2 class="hndle ui-sortable-handle">
                                        <span>Notes</span>
                                    </h2>
                                </div>
                                <div class="inside">
                                    <form>
                                        <a href="https://docs.google.com/document/d/1pnufgElQvNdisyInMSZJDJG30T8LmkRhGW-BewgNmo0/edit">Detail Workflow</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }, '');
});

add_action('rest_api_init', function () {
    register_rest_route('email-list-dashboard-access/v1', '/download-sample', array(
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function () {
            $filename = basename(ELDA_CSV_FILE_SAMPLE);
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header('Content-Type: text/csv');
            readfile(ELDA_CSV_FILE_SAMPLE);
        }
    ));
    register_rest_route('email-list-dashboard-access/v1', '/download-latest', array(
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function () {
            $filename = basename(ELDA_CSV_FILE_ACTIVE);
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header('Content-Type: text/csv');
            readfile(ELDA_CSV_FILE_ACTIVE);
        }
    ));
});

add_action('frm_pre_create_entry', 'elda', 30, 2);
add_action('frm_pre_update_entry', 'elda', 10, 2);

function elda($values)
{
    if (58 != $values['form_id']) return $values;
    if ('Single Service' !== $values['item_meta'][880]) return $values;

    $provider_entries = elda_collect_entries(31, '727, 728, 873, 870, 729');
    $matching_provider_entry_ids = [];
    elda_service_subscribers_1a($values['item_meta'], $provider_entries, $matching_provider_entry_ids);
    elda_custom_matched_1c($values['item_meta'], $provider_entries, $matching_provider_entry_ids);
    $values['item_meta'][1088] = elda_custom_matched_1d($provider_entries, $matching_provider_entry_ids);
    $values['item_meta'][1526] = elda_custom_matched_1e($provider_entries, $matching_provider_entry_ids);
    $values['item_meta'][2594] = elda_check_2594_1f();
    $values['item_meta'][1532] = elda_extract_user_ids($provider_entries, $matching_provider_entry_ids);

    $seller_entries = elda_collect_entries(38, '563, 1422, 1421, 2535, 1858');
    $matching_seller_entry_ids = [];
    elda_seller_service_subscriber_2a($values['item_meta'], $seller_entries, $matching_seller_entry_ids);
    $values['item_meta'][1530] = seller_matching_seller_service_subscriber_2b($seller_entries, $matching_seller_entry_ids);
    $values['item_meta'][2595] = seller_matching_check_2595_2c();
    $values['item_meta'][2536] = elda_extract_user_ids($seller_entries, $matching_seller_entry_ids);

    return $values;
}

function elda_collect_entries($form_id, $field_ids)
{
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare("
        SELECT
            {$wpdb->prefix}frm_items.id
            , {$wpdb->prefix}frm_items.user_id
            , {$wpdb->prefix}frm_item_metas.field_id
            , {$wpdb->prefix}frm_item_metas.meta_value
        FROM {$wpdb->prefix}frm_items
        LEFT JOIN {$wpdb->prefix}frm_item_metas ON {$wpdb->prefix}frm_items.id = {$wpdb->prefix}frm_item_metas.item_id
        WHERE {$wpdb->prefix}frm_items.form_id = %d
        AND {$wpdb->prefix}frm_item_metas.field_id IN ($field_ids)
    ", $form_id));
}

function elda_extract_user_ids($entries, $entry_ids)
{
    $matching_user_ids = [];
    foreach ($entry_ids as $matching_entry_id) {
        foreach ($entries as $entry) {
            if ($entry->id === $matching_entry_id && !in_array($entry->user_id, $matching_user_ids)) {
                $matching_user_ids[] = $entry->user_id;
            }
        }
    }
    return implode(',', $matching_user_ids);
}

function elda_service_subscribers_1a($submitted, $provider_entries, &$matching_provider_entry_ids)
{
    foreach (array_unique(array_map(function ($answers) {
        return $answers->id;
    }, $provider_entries)) as $provider_entry_id) {
        $checked_states = [];

        /*
		$answer_727 = array_values(array_filter($provider_entries, function ($answers) use ($provider_entry_id) {
			return $provider_entry_id == $answers->id && 727 == $answers->field_id;
		}));
		if (isset($answer_727[0])) $checked_states = array_merge($checked_states, @unserialize($answer_727[0]->meta_value) ? unserialize($answer_727[0]->meta_value) : [$answer_727[0]->meta_value]);
		*/

        $answer_728 = array_values(array_filter($provider_entries, function ($answers) use ($provider_entry_id) {
            return $provider_entry_id == $answers->id && 728 == $answers->field_id;
        }));
        if (isset($answer_728[0])) $checked_states = array_merge($checked_states, @unserialize($answer_728[0]->meta_value) ? unserialize($answer_728[0]->meta_value) : [$answer_728[0]->meta_value]);

        if (isset($submitted[885])) if (in_array($submitted[885], $checked_states)) $matching_provider_entry_ids[] = $provider_entry_id;
        if (isset($submitted[884])) {
            if (is_array($submitted[884])) {
                if (!empty(array_intersect($submitted[884], $checked_states))) $matching_provider_entry_ids[] = $provider_entry_id;
            } else if (in_array($submitted[884], $checked_states)) $matching_provider_entry_ids[] = $provider_entry_id;
        }
    }
}

function elda_custom_matched_1c($submitted, $provider_entries, &$matching_provider_entry_ids)
{
    foreach (array_unique(array_map(function ($answers) {
        return $answers->id;
    }, $provider_entries)) as $provider_entry_id) {
        $answer_873 = array_values(array_filter($provider_entries, function ($answers) use ($provider_entry_id) {
            return $provider_entry_id == $answers->id && 873 == $answers->field_id;
        }));

        if (!isset($answer_873[0])) {
        } else {
            $answer_873 = $answer_873[0]->meta_value;
            if (!isset($submitted[2534])) {
            } else if (is_array($submitted[2534])) {
                if (in_array($answer_873, $submitted[2534])) $matching_provider_entry_ids = array_values(array_diff($matching_provider_entry_ids, [$provider_entry_id]));
            } else {
                if ($answer_873 == $submitted[2534]) {
                    $matching_provider_entry_ids = array_values(array_diff($matching_provider_entry_ids, [$provider_entry_id]));
                }
            }
        }
    }
}

function elda_custom_matched_1d($provider_entries, $matching_provider_entry_ids)
{
    $provider_emails = [];
    foreach ($matching_provider_entry_ids as $matching_entry_id) {
        $email = array_values(array_filter($provider_entries, function ($provider_entry) use ($matching_entry_id) {
            return $matching_entry_id == $provider_entry->id && 870 == $provider_entry->field_id;
        }));
        if (isset($email[0])) $provider_emails[] = $email[0]->meta_value;
    }

    return implode(';', $provider_emails);
}

function elda_custom_matched_1e($provider_entries, $matching_provider_entry_ids)
{
    $provider_emails = [];
    foreach ($matching_provider_entry_ids as $matching_entry_id) {
        $email = array_values(array_filter($provider_entries, function ($provider_entry) use ($matching_entry_id) {
            return $matching_entry_id == $provider_entry->id && 729 == $provider_entry->field_id;
        }));
        if (isset($email[0])) $provider_emails[] = $email[0]->meta_value;
    }

    return implode(';', $provider_emails);
}

function elda_check_2594_1f()
{
    return ["Yes"];
}

function elda_seller_service_subscriber_2a($submitted, $seller_entries, &$matching_seller_entry_ids)
{
    global $wpdb;
    $sellers_1841 = $wpdb->get_var($wpdb->prepare("
		SELECT meta_value
		FROM {$wpdb->prefix}frm_item_metas
		LEFT JOIN {$wpdb->prefix}frm_items ON {$wpdb->prefix}frm_item_metas.item_id = {$wpdb->prefix}frm_items.id
		WHERE {$wpdb->prefix}frm_items.user_id = %d AND {$wpdb->prefix}frm_item_metas.field_id = %d
	", $submitted[877], 1841));
    foreach (array_unique(array_map(function ($answers) {
        return $answers->id;
    }, $seller_entries)) as $seller_entry_id) {

        $is_1422_match_883 = false;
        $answer_1422 = array_values(array_filter($seller_entries, function ($answers) use ($seller_entry_id) {
            return $seller_entry_id == $answers->id && 1422 == $answers->field_id;
        }));
        if (isset($answer_1422[0])) {
            if (@unserialize($answer_1422[0]->meta_value)) {
                $answer_1422 = unserialize($answer_1422[0]->meta_value);
                $is_1422_match_883 = in_array($submitted[883], $answer_1422) || in_array('Check All', $answer_1422);
            } else {
                $is_1422_match_883 = $submitted[883] == $answer_1422[0]->meta_value || 'Check All' == $answer_1422[0]->meta_value;
            }
        }

        $is_1421_match_884_or_885 = false;
        $answer_1421 = array_values(array_filter($seller_entries, function ($answers) use ($seller_entry_id) {
            return $seller_entry_id == $answers->id && 1421 == $answers->field_id;
        }));
        if (isset($answer_1421[0])) $answer_1421 = @unserialize($answer_1421[0]->meta_value) ? unserialize($answer_1421[0]->meta_value) : $answer_1421[0]->meta_value;
        if (isset($submitted[885])) $is_1421_match_884_or_885 = in_array($submitted[885], $answer_1421) || in_array('International', $answer_1421);
        if (isset($submitted[884])) {
            if (is_array($submitted[884])) $is_1421_match_884_or_885 = !empty(array_intersect($submitted[884], $answer_1421)) || in_array('International', $answer_1421);
            else $is_1421_match_884_or_885 = in_array($submitted[884], $answer_1421) || in_array('International', $answer_1421);
        }

        $is_1858_match_1841 = false;
        if ($sellers_1841) {
            $answer_1858 = array_values(array_filter($seller_entries, function ($answers) use ($seller_entry_id) {
                return $seller_entry_id == $answers->id && 1858 == $answers->field_id;
            }));
            if (isset($answer_1858[0])) $is_1858_match_1841 = $sellers_1841 == $answer_1858[0]->meta_value;
        }

        if ($is_1422_match_883 || $is_1421_match_884_or_885 || $is_1858_match_1841) $matching_seller_entry_ids[] = $seller_entry_id;
    }
}

function seller_matching_seller_service_subscriber_2b($seller_entries, $matching_seller_entry_ids)
{
    $seller_emails = [];
    foreach ($matching_seller_entry_ids as $matching_entry_id) {
        $email = array_values(array_filter($seller_entries, function ($seller_entry) use ($matching_entry_id) {
            return $matching_entry_id == $seller_entry->id && 2535 == $seller_entry->field_id;
        }));
        if (isset($email[0])) $seller_emails[] = $email[0]->meta_value;
    }

    return implode(';', $seller_emails);
}

function seller_matching_check_2595_2c()
{
    return ["Yes"];
}
