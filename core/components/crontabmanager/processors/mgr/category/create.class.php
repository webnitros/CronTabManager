<?php
/**
 * Create an Item
 */
class CronTabManagerCategoryCreateProcessor extends modObjectCreateProcessor {
	/* @var CronTabManagerCategory $object*/
	public $object = 'CronTabManagerCategory';
	public $objectType = 'CronTabManagerCategory';
	public $classKey = 'CronTabManagerCategory';
	public $languageTopics = array('crontabmanager:manager');
	public $permission = 'crontabmanager_create';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/**
	 * @return bool
	 */
	public function beforeSet() {

		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('crontabmanager_category_err_name'));
		}
		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('crontabmanager_category_err_ae'));
		}
		return parent::beforeSet();
	}
}
return 'CronTabManagerCategoryCreateProcessor';