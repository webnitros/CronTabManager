<?php

/**
 * Вернет список задание
 * Class CronTabManagerShowCrontabsProcessor
 */
class CronTabManagerShowCrontabsProcessor extends modProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {

        /** @var CronTabManager $CronTabManager */
        $CronTabManager = $this->modx->getService('CronTabManager');
        /** @var modProcessorResponse $response */

        $list = $CronTabManager->loadManager()->getList();


        $out = implode('<br>', $list);
        exit('Готовый список с крон заданиями добавленных в crontab<br><br><pre style="background-color: #eee; overflow-x: scroll; padding: 5px 15px" contenteditable="true">'.'# modX component CronTabManager author Stapenko Andrey '.PHP_EOL.$out.PHP_EOL.PHP_EOL.'</pre>');
    }

}

return 'CronTabManagerShowCrontabsProcessor';