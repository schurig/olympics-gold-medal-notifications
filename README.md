#Olympic Gold Medal Notifications
This script checks if there is a golden olympics medal for a specific country and notifies you via Pushover or E-Mail.

##How to use
The script is pretty easy to use. The only thing you will have to do is to adjust your settings and run it as a cronjob. In the following example it checks each minute if there is a new medal.

```bash
*/1 * * * * php /path/to/file/Gold.class.php
```