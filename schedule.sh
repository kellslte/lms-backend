#!/bin/bash
/usr/bin/php /home/u759879241/public_html/lms_api && php artisan queue:run >> /dev/null 2>&1
