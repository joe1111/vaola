<?php
namespace vaola\ResultFields;
use Plenty\Modules\DataExchange\Contracts\ResultFields;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Services\ArrayHelper;
/**
 * Class vaolaDE
 * @package vaola\ResultFields
 */
class vaolaDE extends ResultFields
{
	/*
	 * @var ArrayHelper
	 */
	private $arrayHelper;
	/**
	 * vaola constructor.
	 * @param ArrayHelper $arrayHelper
	 */
	public function __construct(ArrayHelper $arrayHelper)
	{
		$this->arrayHelper = $arrayHelper;
	}
	/**
	 * Generate result fields.
	 * @param  array $formatSettings = []
	 * @return array
	 */
	public function generateResultFields(array $formatSettings = []):array
	{
		$settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');
            $this->setOrderByList(['orderBy.itemId' => 'asc']);
        $itemDescriptionFields = ['urlContent'];
        $itemDescriptionFields[] = ($settings->get('nameId')) ? 'name' . $settings->get('nameId') : 'name1';
		if($settings->get('descriptionType') == 'itemShortDescription'
            || $settings->get('previewTextType') == 'itemShortDescription')
        {
            $itemDescriptionFields[] = 'shortDescription';
        }
        if($settings->get('descriptionType') == 'itemDescription'
            || $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
            || $settings->get('previewTextType') == 'itemDescription'
            || $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
        {
            $itemDescriptionFields[] = 'description';
        }
        if($settings->get('descriptionType') == 'technicalData'
            || $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
            || $settings->get('previewTextType') == 'technicalData'
            || $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
        {
            $itemDescriptionFields[] = 'technicalData';
        }
		$fields = [
			'itemBase'=> [
				'id',
				'producerId',
				'free1',
				'free2',
				'free3',
				'free4',
				'free5',
				'free6',
				'free7',
				'free8',
				'free9',
				'free10',
				'free11',
				'free12',
				'free13',
				'free14',
				'free15',
				'free16',
				'free17',
				'free18',
				'free19',
				'free20',
				'tradoriaCategory'
			],
			'itemDescription' => [
                'params' => [
                    'language' => $settings->get('lang') ? $settings->get('lang') : 'de',
                ],
                'fields' => $itemDescriptionFields,
            ],
			'itemPropertyList' => [
				'params' => [],
				'fields' => [
					'propertyId',
					'propertyValue',
				]
			],
			'variationImageList' => [
				'params' => [
					'type' => 'all',
				],
				'referenceMarketplace' => $settings->get('referrerId') ? $settings->get('referrerId') : 106,
				'fields' => [
					'type',
					'path',
					'position',
				]
			],
			'variationBase' => [
				'availability',
				'attributeValueSetId',
				'content',
				'id',
				'limitOrderByStockSelect',
				'model',
				'unitId',
				'vatId',
                'primaryVariation',
			],
			'variationRecommendedRetailPrice' => [
				'params' => [
					'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : 106,
				],
				'fields' => [
					'price',
				],
			],
            'variationRetailPrice' => [
				'params' => [
					'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : 106,
				],
				'fields' => [
					'price',
				],
            ],
			'variationSpecialOfferRetailPrice' => [
				'params' => [
					'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : 106,
				],
				'fields' => [
					'retailPrice',
				],
			],
			'variationStandardCategory' => [
				'params' => [
					'plentyId' => $settings->get('plentyId'),
				],
				'fields' => [
					'categoryId',
				],
			],
			'variationBarcodeList' => [
				'params' => [
					'barcodeType' => $settings->get('barcode') ? $settings->get('barcode') : 'EAN',
				],
				'fields' => [
					'code',
					'barcodeType',
				]
			],
			'variationMarketStatus' => [
				'params' => [
					'marketId' => 106
				],
				'fields' => [
					'sku'
				]
			],
			'variationStock' => [
				'params' => [
					'type' => 'virtual'
				],
				'fields' => [
					'stockNet'
				]
			],
			'variationAttributeValueList' => [
				'attributeId',
				'attributeValueId'
			]
		];
		return $fields;
	}
}