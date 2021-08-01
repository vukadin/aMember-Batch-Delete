jQuery(document).ready(function($){

    var $column_container = $('#abd-column-container'),
        $column_select = $('#abd-column'),
        $api_key = $('#abd-api-key'),
        $status_container = $('#abd-status-container'),
        $status = $('#abd-status'),
        $submit_container = $('#abd-button-container'),
        $submit = $('#abd-submit'),
        $cancel = $('#abd-cancel'),
        $file = $('#abd-csv-upload'),
        csv_data = [],
        emails = [],
        found_emails = [],
        working = false,
        step = 0;

    $file.on('change', function(event){

        $column_container.hide();
        $column_select.children(':gt(0)').remove();
        $submit_container.hide();
        $status_container.hide();
        emails = [];
        step = 1;

        var files = event.target.files;
        var file = files[0];

        var reader = new FileReader();
        reader.readAsText(file);
        reader.onload = function(event){
            var csv = event.target.result;
            var data = $.csv.toArrays(csv);
            if(data && data.length > 0){
                csv_data = data.slice(1);
                $.each(data[0], function(index, header){
                    $('<option></option>').val(index).text(header).appendTo($column_select);
                });

                $column_container.show();
            }
        }

    });

    $column_select.on('change', function(){
        $status_container.hide();
        var email_column_index = $column_select.val();
        emails = [];

        if(email_column_index !== ''){
            email_column_index = parseInt(email_column_index);
            $.each(csv_data, function(row_index, row){
                emails.push(row[email_column_index]);
            });
            $submit.val(amemberBachDeleteConfig.labels.check_emails);
            $submit_container.show();
            step = 2;
        }
        else {
            step = 1;
            $submit_container.hide();
        }
    });

    $submit.on('click', function(){
        if( working ) return;
        working = true;
        $('body').addClass('abd-working');

        var postdata;

        $submit.hide();
        $cancel.hide();

        if( step === 2 ){
            postdata = {
                action : 'adb_check_emails',
                api_key : $api_key.val(),
                emails : emails
            };
        }
        else if( step === 3){
            postdata = {
                action : 'adb_delete_emails',
                api_key : $api_key.val(),
                emails : found_emails
            };
        }

        $.post(ajaxurl, postdata, function(response){

            $submit.show();
            $cancel.show();
            $('body').removeClass('abd-working');
            working = false;

            if(response.status === 'OK')
            {
                if( step === 2 ){
                    step = 3;
                    $submit.val(amemberBachDeleteConfig.labels.delete_emails);
                    $status.empty();
                    $status.append('<p>Found ' +response.found.length+ ' emails</p>');
                    var $not_found_list = $('<ol></ol>');
                    $.each(response.not_found, function(index, email){
                        $('<li></li>').text(email).appendTo($not_found_list);
                    });
                    $status.append('<p><strong>Missing Emails</strong></p>');
                    $not_found_list.appendTo($status);
                    $status_container.show();
                    found_emails = response.found;
                }
                else if( step === 3){
                    $status.empty();
                    $status.append('<p>Deleted ' +response.emails.length+ ' emails</p>');
                    var $deleted_list = $('<ol></ol>');
                    $.each(response.emails, function(index, email){
                        $('<li></li>').text(email).appendTo($deleted_list);
                    });
                    $status.append('<p><strong>Deleted Emails</strong></p>');
                    $deleted_list.appendTo($status);
                }
            }
            else {
                alert(response.error_message);
            }

        });
    });

    $cancel.on('click', function(){

        csv_data = [];
        emails = [];
        found_emails = [];

        step = 0;
        $file.val('');
        $column_container.hide();
        $column_select.children(':gt(0)').remove();
        $submit_container.hide();
        $status_container.hide();
    });

});