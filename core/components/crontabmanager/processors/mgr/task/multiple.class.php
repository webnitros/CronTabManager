<?php
class CronTabManagerTaskMultipleProcessor extends modProcessor
{
    /**
     * @return array|string
     */
    public function process()
    {
        
        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }

        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->success();
        }

        /** @var CronTabManager $CronTabManager */
        $CronTabManager = $this->modx->getService('CronTabManager');
        /** @var modProcessorResponse $response */

        foreach ($ids as $id) {
            $response = $CronTabManager->runProcessor('mgr/task/'.$method, array('id' => $id));
            if ($response->isError()) {
                return $response->getResponse();
            }
        }
        return $this->success();
    }

}

return 'CronTabManagerTaskMultipleProcessor';