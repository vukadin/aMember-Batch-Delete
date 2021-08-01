# Amember Batch Delete Wordpress Plugin
AmemberBatch Delete allows admins to delete users in batches from CSV from Amember.

## How to Install
Copy *amember-batch-delete* folder into *wp-content/plugins/* folder of your WP installation.

Aweber should be installed in root of WP installation in **amember** directory.

## How to Use
Navigate to *Amember->Batch Delete* page, enter your API key and select CSV file that contains user emails ( file must contain a header row ).

After that select a column that holds emails and click **Check Emails** to check if emails exists in Aweber.

List will be displayed with missing emails and then you can proceed with **Delete Emails** and delete found emails from Aweber.
