<div class="wrap">
    <h2><?php _e( 'Batch Delete', 'amember-batch-delete' ); ?></h2>
    <p><?php _e( 'Enter API Key and upload CSV and select a column that holds email to start the process', 'amember-batch-delete' ); ?></p>
  
    <table class="form-table" >
        <tr id="abd-api-key-container" >
            <th>
                <label for="abd-api-key" ><?php _e( 'API Key', 'amember-batch-delete' ); ?></label>
            </th>
            <td>
                <input id="abd-api-key" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'adb_api_key', '' ) ); ?>" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="abd-csv-upload" ><?php _e( 'CSV file', 'amember-batch-delete' ); ?></label>
            </th>
            <td>
                <input type="file" accept=".csv" id="abd-csv-upload" />
            </td>
        </tr>
        <tr id="abd-column-container" >
            <th>
                <label for="abd-column" ><?php _e( 'Email Column', 'amember-batch-delete' ); ?></label>
            </th>
            <td>
                <select id="abd-column" >
                    <option value="" ></option>
                </select>
                <p class="description" ><?php _e( 'Select column that contains email', 'amember-batch-delete' ); ?></p>
            </td>
        </tr>
        <tr id="abd-status-container" >
            <th><?php _e( 'Status', 'amember-batch-delete' ); ?></th>
            <td id="abd-status" ></td>
        </tr>
    </table>

    <p id="abd-button-container">
        <input id="abd-submit" type="button" class="button button-primary" value="<?php esc_attr_e( 'Check emails' , 'amember-batch-delete'); ?>" />
        <input id="abd-cancel" type="button" class="button button-primary" value="<?php esc_attr_e( 'Cancel' , 'amember-batch-delete'); ?>" />
        <span class="spinner is-active" id="abd-spinner"></span>
    </p>

</div>