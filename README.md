# Cron-задачи #

* проверка доступности сайтов
* задача: * * * * /usr/bin/php /path_to_site_root/yii site-checker/index >> /path_to_log_folder/monitoring_site_checker.txt 2>&1

* отправка уведомлений, если последние коды - не 2xx
* задача: * * * * /usr/bin/php /path_to_site_root/yii site-unavailability/index >> /path_to_log_folder/monitoring_site_unavailability.txt 2>&1

* удаление устаревших записей из таблицы History
* задача: 0 * * * /usr/bin/php /path_to_site_root/yii history-cleaner/index >> /path_to_log_folder/monitoring_history_cleaner.txt 2>&1
