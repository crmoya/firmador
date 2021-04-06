<?php

namespace app\models;

use Yii;
use yii\helpers\FileHelper;

class DocIndexer{

	public static function getNext()
	{
		$path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
		$files = FileHelper::findFiles($path);
		return count($files) + 1;
	}
}
