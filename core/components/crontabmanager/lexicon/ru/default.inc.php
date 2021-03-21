<?php
include_once 'setting.inc.php';

$_lang['crontabmanager'] = 'CronTabManager';
$_lang['crontabmanager_menu_desc'] = 'Управление крон заданиями';


$_lang['crontabmanager_not_log_content'] = 'Еще не было логов';


$_lang['crontabmanager_email_notifications_subject'] = '[Crontab] превышен лимит ошибок task_id:[[+task_id]] controller: [[+task_path_task]]';
$_lang['crontabmanager_email_notifications_message'] = 'Требуется внимание администратора для исправления запуска cron задания<br>
[[+add_output]]
<h2>Task Id [[+task_id]]</h2>
Категория: <em style="background-color: #eee; padding: 3px"><b>[[+task_category_name]]</b></em><br>
Путь к контроллеру: <em style="background-color: #eee; padding: 3px"><b>[[+task_path_task]]</b></em><br>
Описание задания: <em style="background-color: #eee; padding: 3px"><b>[[+task_description]]</b></em><br>
Время запуска: <em style="background-color: #eee; padding: 3px"><b>[[+task_time]]</b></em><br>
Последняя удачная попытака завершена в: <em style="background-color: #eee; padding: 3px"><b>[[+task_end_run]]</b></em><br>
Максимальное количество попыток: <em style="background-color: #eee; padding: 3px"><b>[[+task_max_number_attempts]]</b></em><br>
Лог файл cron: <em style="background-color: #eee; padding: 3px"><b>[[+task_file_log]]</b></em><br>
Логи: <a href="[[+log_url]]?task_id=[[+task_id]]">открыть в браузере</a><br>

<h4>Временная блокировка задания</h4>
Вы можете заблокировать исполнение задания на выбранный срок:<br>
<em>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=1">1 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=5">5 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=10">10 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=15">15 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=20">20 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=30">30 минута</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=60">1 час</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=120">2 часа</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=180">3 часа</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=360">6 часа</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=720">12 часа</a><br>
<a href="[[+blockup_url]]?task_id=[[+task_id]]&minutes=1440">24 часа</a><br>
</em>
<br><br>
для снятия времени блокировки используйте ссылку:
<a href="[[+blockup_url]]?task_id=[[+task_id]]&reset=1">Сброс времени блокировки задания</a><br>
';
