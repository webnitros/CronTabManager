<?php


use Webnitros\CronTabManager\Misc;

class msAddFieldActiveGetListProcessor extends modObjectProcessor
{
    public $languageTopics = array('crontabmanager:manager');

    /** {@inheritDoc} */
    public function process()
    {

        $whens = Misc::when();
        $array = [];
        foreach ($whens as $when) {
            $array [] = [
                'value' => $when,
                'name' => $this->modx->lexicon('crontabmanager_when_' . $when)
            ];
        }

        $query = $this->getProperty('query');
        if (!empty($query)) {
            foreach ($array as $k => $format) {
                if (stripos($format['name'], $query) === false) {
                    unset($array[$k]);
                }
            }
            sort($array);
        }

        return $this->outputArray($array);
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        return parent::outputArray($array, $count);
    }

}

return 'msAddFieldActiveGetListProcessor';
