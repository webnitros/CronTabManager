<?php
if (!class_exists('CrontabManagerManual')) {
    include_once dirname(dirname(dirname(__FILE__))) . '/lib/crontab/CrontabManagerManual.php';
}
class CrontabManagerManualBin extends CrontabManagerManual
{

}
