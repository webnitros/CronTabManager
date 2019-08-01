<?php

/**
 * Update an Item
 */
class CronTabManagerCategoryUpdateProcessor extends modObjectUpdateProcessor {
	/* @var CronTabManagerCategory $object*/
	public $object = 'CronTabManagerCategory';
	public $objectType = 'CronTabManagerCategory';
	public $classKey = 'CronTabManagerCategory';
	public $languageTopics = array('crontabmanager:manager');
	public $permission = 'crontabmanager_save';

	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return bool|string
	 */
	public function beforeSave() {
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			return $this->modx->lexicon('crontabmanager_category_err_ns');
		}
		return parent::beforeSet();
	}
}

return 'CronTabManagerCategoryUpdateProcessor';
