<?php

namespace Craft;

class EmbeddedAssetsModel extends BaseComponentModel
{
	public static function populateFromAsset(AssetFileModel $asset)
	{
		if($asset->kind === 'json' && strpos($asset->filename, EmbeddedAssetsPlugin::getFileNamePrefix(), 0) === 0)
		{
			try
			{
				$url = $asset->getUrl();

				if(!UrlHelper::isAbsoluteUrl($url))
				{
					$protocol = craft()->request->isSecureConnection() ? 'https' : 'http';
					$url = UrlHelper::getUrlWithProtocol($url, $protocol);
				}

				// See http://stackoverflow.com/questions/272361/how-can-i-handle-the-warning-of-file-get-contents-function-in-php
				$rawData = @file_get_contents($url);

				if($rawData)
				{
					$data = JsonHelper::decode($rawData);

					if($data['__embeddedasset__'])
					{
						unset($data['__embeddedasset__']);

						$embed = new EmbeddedAssetsModel();
						$embed->id = $asset->id;

						foreach($data as $key => $value)
						{
							$embed->$key = $value;
						}

						return $embed;
					}
				}
			}
			catch(\Exception $e)
			{
				return null;
			}
		}

		return null;
	}

	protected function defineAttributes()
	{
		return array_merge(parent::defineAttributes(), array(
			'type'            => AttributeType::String,
			'version'         => AttributeType::String,
			'url'             => AttributeType::String,
			'title'           => AttributeType::String,
			'description'     => AttributeType::String,
			'authorName'      => AttributeType::String,
			'authorUrl'       => AttributeType::String,
			'providerName'    => AttributeType::String,
			'providerUrl'     => AttributeType::String,
			'cacheAge'        => AttributeType::String,
			'thumbnailUrl'    => AttributeType::String,
			'thumbnailWidth'  => AttributeType::Number,
			'thumbnailHeight' => AttributeType::Number,
			'html'            => AttributeType::String,
			'safeHtml'        => AttributeType::String,
			'width'           => AttributeType::Number,
			'height'          => AttributeType::Number,
		));
	}
}
