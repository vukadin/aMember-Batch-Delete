<?php
namespace AmemberBatchDelete;
/*
Plugin Name: aMember Batch Delete
Description: Adds batch delete functionality to aMember
Version: 1.0.0
Author: Njegos Vukadin
*/

define( 'AMEMBER_BATCH_DELETE_FILE', __FILE__ );
define( 'AMEMBER_BATCH_DELETE_DIR', dirname( __FILE__ ) );
define( 'AMEMBER_BATCH_DELETE_VERSION', '1.0.0' );

include AMEMBER_BATCH_DELETE_DIR . '/includes/plugin.php';

new Plugin();