<?php
namespace AmemberBatchDelete;

class Plugin
{
    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'registerPage' ], 999 );
    }

    public function registerPage()
    {
        add_submenu_page( 'am4-settings', __('Batch Delete', 'amember-batch-delete'), __('Batch Delete', 'amember-batch-delete'), 'manage_options', 'amember-batch-delete', [ $this, 'outputPage'] );
    }
}