<?php
namespace AmemberBatchDelete;

if(!defined('ABSPATH')) exit;

class Plugin
{
    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'registerPage' ], 999 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminAssets' ], 999 );
        add_action( 'wp_ajax_adb_check_emails', [ $this, 'checkEmails' ] );
        add_action( 'wp_ajax_adb_delete_emails', [ $this, 'deleteEmails' ] );
    }

    public function registerPage()
    {
        add_submenu_page( 'am4-settings', __('Batch Delete', 'amember-batch-delete'), __('Batch Delete', 'amember-batch-delete'), 'manage_options', 'amember-batch-delete', [ $this, 'outputPage'] );
    }

    public function enqueueAdminAssets()
    {
        $screen = get_current_screen();
        
        if( $screen->id !== 'amember_page_amember-batch-delete' ) return;

        wp_enqueue_script( 'jquery-csv', plugins_url( 'assets/js/jquery.csv.min.js', AMEMBER_BATCH_DELETE_FILE ), [ 'jquery' ], AMEMBER_BATCH_DELETE_VERSION );
        wp_enqueue_script( 'amember-batch-delete', plugins_url( 'assets/js/script.js', AMEMBER_BATCH_DELETE_FILE ), [ 'jquery', 'jquery-csv' ], AMEMBER_BATCH_DELETE_VERSION );
        wp_localize_script( 'amember-batch-delete', 'amemberBachDeleteConfig', [
            'labels' => [
                'check_emails' => __( 'Check emails' , 'amember-batch-delete'),
                'delete_emails' => __( 'Delete emails' , 'amember-batch-delete'),
                'cancel' => __( 'Cancel' , 'amember-batch-delete'),
            ]
        ]);

        wp_enqueue_style( 'amember-batch-delete', plugins_url( 'assets/css/style.css', AMEMBER_BATCH_DELETE_FILE ), null, AMEMBER_BATCH_DELETE_VERSION );
    }

    public function outputPage()
    {
        include AMEMBER_BATCH_DELETE_DIR . '/templates/delete-page.php';
    }

    public function checkEmails()
    {
        if( !current_user_can( 'manage_options' ) ) exit;

        set_time_limit(0);

        $api_key = $_POST['api_key'] ?? '';
        $emails = $_POST['emails'] ?? [];

        update_option( 'adb_api_key', $api_key );

        $found_emails = [];
        $not_found_emails = [];

        foreach($emails as $email) :

            $email = trim( $email );

            if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) continue;

            $url = site_url('/amember/api/users');

            $fields = array(
                '_key' => $api_key,
                '_count' => 1,
                '_filter' => [
                    'email' => $email
                ]
            );
            
            $ch = curl_init();

            $url .= '?' . http_build_query($fields);

            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            $result = json_decode( curl_exec($ch), true );

            if($result) :
                $error_message = $result['message'] ?? false;
                $total = $result['_total'] ?? 0;
                $user = $result[0] ?? false;

                if($error_message) :
                    
                    wp_send_json([
                        'status' => 'error',
                        'error_message' => $error_message
                    ]);

                elseif( $total > 0 ):

                    $found_emails[] = [
                        'id' => $user['user_id'],
                        'email' => $email
                    ];

                else : 

                    $not_found_emails[] = $email;

                endif;
            endif;

            curl_close($ch);

        endforeach;

        wp_send_json([
            'status' => 'OK',
            'found' => $found_emails,
            'not_found' => $not_found_emails
        ]);
        exit;
    }

    public function deleteEmails()
    {
        if( !current_user_can( 'manage_options' ) ) exit;

        set_time_limit(0);

        $api_key = $_POST['api_key'] ?? '';
        $objects = $_POST['emails'] ?? [];

        update_option( 'adb_api_key', $api_key );

        $deleted_emails = [];

        foreach($objects as $object) :

            $user_id = $object['id'] ?? 0;
            $email = $object['email'] ?? '';

            $url = site_url('/amember/api/users/'.$user_id);

            $fields = array(
                '_key' => $api_key,
                '_method' => "DELETE"
            );
            
            $ch = curl_init();

            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded") );

            $result = json_decode( curl_exec($ch), true );

            if($result) :
                $error_message = $result['message'] ?? false;
                $total = $result['_total'] ?? 0;
                $user = $result[0] ?? false;

                if($error_message) :
                    if( $error_message === 'API Error 10002 - [key] is not found or disabled' ) :
                        wp_send_json([
                            'status' => 'error',
                            'error_message' => $error_message
                        ]);
                    endif;
                else :
                    $deleted_emails[] = $email;
                endif;

            endif;

            curl_close($ch);

        endforeach;

        wp_send_json([
            'status' => 'OK',
            'emails' => $deleted_emails
        ]);
        exit;
    }
}