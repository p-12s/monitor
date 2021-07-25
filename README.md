# Попытка сделать сервис - мониторинг сайтов

Реализация на Yii2, запуск асинхронных проверок ресурсов достигается за счет curl_multi_add_handle.  
В целом, для пробы параллельной обработки запросов - норм. Но это не история для продакшена.  

### Cron-задачи
```
* проверка доступности сайтов
* задача: * * * * /usr/bin/php /path_to_site_root/yii site-checker/index >> /path_to_log_folder/monitoring_site_checker.txt 2>&1

* отправка уведомлений, если последние коды - не 2xx
* задача: * * * * /usr/bin/php /path_to_site_root/yii site-unavailability/index >> /path_to_log_folder/monitoring_site_unavailability.txt 2>&1

* удаление устаревших записей из таблицы History
* задача: 0 * * * /usr/bin/php /path_to_site_root/yii history-cleaner/index >> /path_to_log_folder/monitoring_history_cleaner.txt 2>&1
```
