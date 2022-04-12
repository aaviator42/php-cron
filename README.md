# php-cron
A hacky implementation of cron jobs in PHP.  

`v1.0`: `2022-04-12`

### Setup
1. Put `php-cron.php` and `php-cron-stop.php` in a folder. You can rename both files if you want.
2. Choose a name for your cron job and configure it in both scripts.
3. Make sure `StorX.php` is in the same folder as the two scripts. Get it [here](https://github.com/aaviator42/StorX).
4. Put your cron job commands in `cronMaster()` in `php-cron.php`.
5. Configure a time interval in `php-cron.php`.
6. Run the cron job by making a GET request to `php-cron.php` (the easiest way to do this is by just navigating to it in your web browser).
7. Stop the cron job by making a GET request to `php-cron-stop.php`.

### What it does
The script will execute `cronMaster()`, and then sleep for `$cronInterval`. Ad infinitum.

The cron job script won't allow multiple instances to run at the same time, and the cron job should continue running on the server even if you disconnect from `php-cron.php` (i.e., close the page in your browser).

It creates a DB file `$cronName.cron.db` and a log file `$cronName.cron.log` in the same folder as the scripts. The former is used primarily to terminate cron jobs and ensure only one instance is running at a time.

You can safely have multiple sets of `php-cron.php` and `php-cron-stop.php`, just rename the two scripts and use a unique name for each cron job.

### Why?
Because my shared hosting provider disabled cron jobs for some stupid reason.

### Disclaimer
Don't use this unless you're sure you know what you're doing.
