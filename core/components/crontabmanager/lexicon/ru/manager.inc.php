<?php
include_once 'setting.inc.php';

$_lang['crontabmanager'] = 'CronTabManager';
$_lang['crontabmanager_menu_desc'] = 'Управление крон заданиями';

$_lang['crontabmanager_intro_msg'] = 'Вы можете выделять сразу несколько заданий при помощи Shift или Ctrl.';

$_lang['crontabmanager_grid_search'] = 'Поиск';
$_lang['crontabmanager_grid_actions'] = 'Действия';


# Tab
$_lang['crontabmanager_task'] = 'Задание';
$_lang['crontabmanager_task_log'] = 'Логи';
$_lang['crontabmanager_task_setting'] = 'Настройки';
$_lang['crontabmanager_task_tab_code'] = 'Свой код';

# Taks

$_lang['crontabmanager_tasks'] = 'Расписание крон';
$_lang['crontabmanager_task_id'] = 'Id';
$_lang['crontabmanager_task_path_task_your'] = 'Свой файл для исполнения';
$_lang['crontabmanager_task_path_task_your_desc'] = 'Укажите абсолютный путь к файл который должен исполниться';
$_lang['crontabmanager_task_message'] = 'Сообщение в письмо уведомления';
$_lang['crontabmanager_task_message_desc'] = 'В начале письма с уведомлением добавиться это сообщение';
$_lang['crontabmanager_task_name'] = 'Наименование';
$_lang['crontabmanager_task_name_desc'] = 'Короткое наименование';
$_lang['crontabmanager_task_add_output_email'] = 'Добавить вывод в сообщение на email';
$_lang['crontabmanager_task_add_output_email_desc'] = 'В сообщение на email добавиться вывод из лог файла';
$_lang['crontabmanager_task_createdon'] = 'Дата создания';
$_lang['crontabmanager_task_updatedon'] = 'Дата обновления';
$_lang['crontabmanager_task_date_start'] = 'Дата запуска';
$_lang['crontabmanager_task_description'] = 'Описание задания';
$_lang['crontabmanager_task_path_task'] = 'Путь к файлу';
$_lang['crontabmanager_task_path_task_desc'] = '<em>Укажите путь к контроллеру в планировщик в виде: report/count.php. Все контроллеры расположены в: scheduler/Controllers/</em>';
$_lang['crontabmanager_task_lock_file'] = 'Файл блокировки';
$_lang['crontabmanager_task_last_run'] = 'Последний запуск';
$_lang['crontabmanager_task_end_run'] = 'Завершен';
$_lang['crontabmanager_task_status'] = 'Статус';
$_lang['crontabmanager_task_time'] = 'Время запуска';
$_lang['crontabmanager_task_start_task'] = 'Запустить задание';
$_lang['crontabmanager_task_minutes'] = 'Минуты';
$_lang['crontabmanager_task_hours'] = 'Часы';
$_lang['crontabmanager_task_days'] = 'Дни';
$_lang['crontabmanager_task_months'] = 'Месяца';
$_lang['crontabmanager_task_weeks'] = 'Месяца';
$_lang['crontabmanager_task_processor'] = 'Путь к процессору';
$_lang['crontabmanager_task_reboot'] = 'Перезапустить';
$_lang['crontabmanager_task_disable'] = 'Отключить задание';
$_lang['crontabmanager_task_remove'] = 'Удалить задание';
$_lang['crontabmanager_task_active'] = 'Включено';
$_lang['crontabmanager_task_category_name'] = 'Категория';
$_lang['crontabmanager_task_completed'] = 'Успешно';
$_lang['crontabmanager_task_notification_enable'] = 'Отправлять уведомление в случае ошибки завершения';
$_lang['crontabmanager_task_notification_emails'] = 'Доп. email для уведомлений';
$_lang['crontabmanager_task_notification_emails_desc'] = 'Через запятую. Адреса администраторов по которым будет отправлено сообщени о превышении неудачных попыток завершения выполнения задания';
$_lang['crontabmanager_task_max_number_attempts'] = 'Макс. число неудачных попыток';
$_lang['crontabmanager_task_max_number_attempts_desc'] = 'Оставить 0 для отключения. После наступления максимального числа неудачных попыток, администратору сайта отправляется письма с уведомление';
$_lang['crontabmanager_task_is_blocked'] = 'Заблокировано';
$_lang['crontabmanager_task_is_blocked_time'] = 'Заблокировано по времени';
$_lang['crontabmanager_task_mode_develop'] = 'Включить режим разработки для задания';
$_lang['crontabmanager_task_mode_develop_desc'] = 'При включении блокировка задания не будет производиться. Так же не будут писать логи и отправлять уведомления';
$_lang['crontabmanager_task_log_storage_time'] = 'Срок хранения логов';
$_lang['crontabmanager_task_log_storage_time_desc'] = 'Укажите срок хранения логов, все логи старше указанного количества минут будет автоматически удалены. Укажите "0" чтобы не удалять логи';
$_lang['crontabmanager_task_blockup_minutes_add'] = 'the task is blocked for "[[+minutes]]" minutes';
$_lang['crontabmanager_task_err_add_crontab'] = 'Не удалось добавить задание контроллер [[+task_path]] в crontab';
$_lang['crontabmanager_task_err_remove_crontab'] = 'Не удалось удалить задание контроллер [[+task_path]] из crontab';
$_lang['CronTabManagerTask_err_remove'] = 'Не удалось удалить задание так как крон задание не удалилось';
$_lang['crontabmanager_show_crontabs'] = 'Список заданий';



// Task Log
$_lang['crontabmanager_task_log_id'] = 'id';
$_lang['crontabmanager_task_log_last_run'] = 'Запуск';
$_lang['crontabmanager_task_log_end_run'] = 'Остановка';
$_lang['crontabmanager_task_log_completed'] = 'Завершено';
$_lang['crontabmanager_task_log_notification'] = 'Уведомление';
$_lang['crontabmanager_task_log_createdon'] = 'Создан';
$_lang['crontabmanager_task_log_updatedon'] = 'Обновлен';
$_lang['crontabmanager_task_un_look'] = 'Task unlook';
$_lang['crontabmanager_task_err_ae_controller'] = 'Не удалось найти файл контроллера по указанному пути: [[+controller]]';
$_lang['crontabmanager_task_year_err_ae_controller'] = 'Не удалось найти файл контроллера по указанному пути: [[+controller]]. Вы используете собственный путь к контроллеру по этому нужно добавить абсолютный путь до файла';
$_lang['crontabmanager_task_removeLog'] = 'Удалить лог файл crontab';
$_lang['crontabmanager_task_removelog_confirm'] = 'Вы уверены что хотите удалить лог файл crontab?';
$_lang['crontabmanager_time_server'] = 'Время на сервере';


// Action
$_lang['crontabmanager_task_create'] = 'Создать задание';
$_lang['crontabmanager_task_update'] = 'Изменить Задание';
$_lang['crontabmanager_task_enable'] = 'Включить Задание';
$_lang['crontabmanager_tasks_enable'] = 'Включить Задание';
$_lang['crontabmanager_tasks_disable'] = 'Отключить Задание';
$_lang['crontabmanager_tasks_remove'] = 'Удалить Задание';
$_lang['crontabmanager_task_unlock'] = 'Снять блокировку';
$_lang['crontabmanager_task_start'] = 'Запустить задание';
$_lang['crontabmanager_task_unblockup'] = 'Сбросить время блокировки';
$_lang['crontabmanager_task_readlog'] = 'Лог последнего запуска crontab';
$_lang['crontabmanager_task_manualstop'] = 'Ручное прерывать задание';
$_lang['crontabmanager_task_manualstop_confirm'] = 'Вы уверены что хотите в ручную остановить выполнение задания?';

$_lang['crontabmanager_task_unblockup_confirm'] = 'Вы уверены, что хотите сбросить время на которое было заблокировано это Задание?';
$_lang['crontabmanager_task_starttask_confirm'] = 'Вы уверены, что хотите запустить это Задание?';
$_lang['crontabmanager_task_unlock_confirm'] = 'Вы уверены, что хотите снять блокировку с этого Задание?';
$_lang['crontabmanager_task_remove_confirm'] = 'Вы уверены, что хотите удалить это Задание?';

$_lang['crontabmanager_task_err_path_task'] = 'Вы должны указать путь к контроллеру.';
$_lang['crontabmanager_task_err_ae'] = 'Задание с таким именем уже существует.';
$_lang['crontabmanager_task_err_nf'] = 'Задание не найден.';
$_lang['crontabmanager_task_err_ns'] = 'Задание не указан.';
$_lang['crontabmanager_task_err_remove'] = 'Ошибка при удалении Задания.';
$_lang['crontabmanager_task_err_save'] = 'Ошибка при сохранении Задания.';
$_lang['crontabmanager_task_err_ns_minutes'] = 'Укажите количество минут для блокировки задания';
$_lang['crontabmanager_task_err_ns_max_minuts_blockup'] = 'Максимальное количество минут для блокировки задания, не должно превышать: [[+max_minuts_blockup]] мин.';
$_lang['crontabmanager_task_err_ns_allow_blocking_tasks'] = 'Блокировка заданий отключена!';



#  Category category
$_lang['crontabmanager_categories'] = 'Категории';
$_lang['crontabmanager_categories_intro_msg'] = 'Категории для заданий используются для фильтрации';

$_lang['crontabmanager_category_id'] = 'Id';
$_lang['crontabmanager_category_name'] = 'Наименование';
$_lang['crontabmanager_category_description'] = 'Описание';
$_lang['crontabmanager_category_active'] = 'Включена';
$_lang['crontabmanager_category_err_sub_id'] = 'Вы должны выбрать подписчика.';
$_lang['crontabmanager_category_err_nf'] = 'Предмет не найден.';
$_lang['crontabmanager_category_err_ns'] = 'Предмет не указан.';
$_lang['crontabmanager_category_err_remove'] = 'Ошибка при удалении Предмета.';
$_lang['crontabmanager_category_err_save'] = 'Ошибка при сохранении Предмета.';

$_lang['crontabmanager_category_disable'] = 'Отключить категорию';
$_lang['crontabmanager_category_create'] = 'Создать категорию';
$_lang['crontabmanager_category_update'] = 'Изменить Категорию';
$_lang['crontabmanager_category_enable'] = 'Включить Категорию';
$_lang['crontabmanager_categories_enable'] = 'Включить Категории';
$_lang['crontabmanager_categories_disable'] = 'Отключить Категории';
$_lang['crontabmanager_categories_remove'] = 'Удалить Категории';
$_lang['crontabmanager_category_remove'] = 'Удалить Категорию';

$_lang['crontabmanager_category_remove_confirm'] = 'Вы уверены, что хотите удалить эту Категорию?';
$_lang['crontabmanager_categories_remove_confirm'] = 'Вы уверены, что хотите удалить эти Категории?';


# Filter
$_lang['crontabmanager_task_parent'] = 'Категория';
$_lang['crontabmanager_task_parent_empty'] = 'Выберите категорию';
$_lang['crontabmanager_task_filter_active'] = 'Активные';
$_lang['crontabmanager_task_filter_completed'] = 'Не завершенные';


$_lang['crontabmanager_task_log_remove'] = 'Удалить лог';
$_lang['crontabmanager_task_logs_remove'] = 'Удалить логи';
