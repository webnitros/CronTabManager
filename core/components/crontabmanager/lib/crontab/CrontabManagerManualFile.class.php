<?php
if (!class_exists('CrontabManagerManual')) {
    include_once dirname(dirname(dirname(__FILE__))) . '/lib/crontab/CrontabManagerManual.php';
}

class CrontabManagerManualFile extends CrontabManagerManual
{
    /**
     * Replaces cron contents
     *
     * @throws \UnexpectedValueException
     * @return CrontabManagerManual
     */
    protected function _replaceCronContents()
    {
        $ret = file_put_contents($this->file_crontab_path, $this->cronContent);
        if (!$ret) {
            throw new \UnexpectedValueException(
                'Не удалось записать' . "\n" . $this->cronContent, $ret
            );
        }
        return $this;
    }


    /**
     * List current cron jobs
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    public function listJobs()
    {

        $content = '';
        if (file_exists($this->file_crontab_path)) {
            $content = file_get_contents($this->file_crontab_path);
        }
        return $content;
    }

}
