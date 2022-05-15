<?php

class CronTabManagerAutoPause extends xPDOSimpleObject
{

    /**
     * {@inheritdoc}
     */
    public function save($cacheFlag = null)
    {
        if ($this->isNew()) {
            if (empty($this->get('createdon'))) {
                $this->set('createdon', time());
            }
        } else {
            $this->set('updatedon', time());
        }
        return parent::save();
    }

    public function str()
    {
        return $this->get('when') . ': ' . $this->get('from') . ' to ' . $this->get('to');
    }


}
