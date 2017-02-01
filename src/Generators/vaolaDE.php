<?php
namespace vaola\Generators;
use Plenty\Modules\DataExchange\Contracts\CSVGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use vaola\Helper\vaolaHelper;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Market\Helper\Contracts\MarketPropertyHelperRepositoryContract;
class vaolaDE extends CSVGenerator
{
	const PROPERTY_TYPE_ENERGY_CLASS       = 'energy_efficiency_class';
	const PROPERTY_TYPE_ENERGY_CLASS_GROUP = 'energy_efficiency_class_group';
	const PROPERTY_TYPE_ENERGY_CLASS_UNTIL = 'energy_efficiency_class_until';
	/*
	 * @var vaolaHelper
	 */
	private $vaolaHelper;
	/*
	 * @var ArrayHelper
	 */
	private $arrayHelper;
	/*
	 * @var array
	 */
	private $attributeName = array();
	/*
	 * @var array
	 */
	private $attributeNameCombination = array();
	/**
	 * MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
	 */
	private $marketPropertyHelperRepository;
	/**
	 * vaola constructor.
	 * @param vaolaHelper $vaolaHelper
	 * @param ArrayHelper $arrayHelper
	 * @param MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
	 */
	public function __construct(
		vaolaHelper $vaolaHelper,
		ArrayHelper $arrayHelper,
		MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
	)
	{
		$this->vaolaHelper = $vaolaHelper;
		$this->arrayHelper = $arrayHelper;
		$this->marketPropertyHelperRepository = $marketPropertyHelperRepository;
	}
	/**
	 * @param RecordList $resultData
	 * @param array $formatSettings
	 */
	protected function generateContent($resultData, array $formatSettings = [])
	{
		if($resultData instanceof RecordList)
		{
			$settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');
			$this->setDelimiter(";");
			
			//Kategorien
			
			/*$this->addCSVContent([
				'external_id',
				'external_parent_id',
				'categoryname',
				'active',
				'sort',
				'level',
			]);
			
			
			//Kategorien
			$this->addCSVContent([
				'',
			]);*/
			
			
			
				$this->addCSVContent([
                                    'p_nr',    
                                    'p_name',
                                    'p_text',
                                    'p_brand', 
                                    'p_catpri[vaola]', 
                                    'p_active[vaola]', 
                                    'p_active[msde]', 
                                    'a_comp[Primärfarbe]', 
                                    'p_tag[Sekundärfarbe]', 
                                    'p_tag[Größenart]', 
                                    'p_tag[Geschlecht]', 
                                    'p_tag[Sportart]', 
                                    'p_tag[Material]', 
                                    'a_nr', 
                                    'a_prodnr', 
                                    'a_ean', 
                                    'a_comp[Größe]', 
                                    'a_vk[msde]', 
                                    'a_uvp[msde]', 
                                    'a_mwst[msde]', 
                                    'a_media[image]{0}', 
                                    'a_media[image]{1}', 
                                    'a_media[image]{2}', 
                                    'a_media[image]{3}', 
                                    'a_media[image]{4}', 
                                    'a_active', 
                                    'a_stock', 
                                    'a_delivery', 
                                    'a_shipping_type', 
                                    
                                    /*
				'Produktname',
				'Artikelnummer',
				'ean',
				'Hersteller',
				'Steuersatz',
				'Preis',
				'Kurzbeschreibung',
				'Beschreibung',
				'Versandkosten',
				'Lagerbestand',
				'Kategoriestruktur',
				'Attribute',
				'Gewicht',
				'Lieferzeit',
				'Nachnahmegebühr',
				'MPN',
				'Bildlink',
				'Bildlink2',
				'Bildlink3',
				'Bildlink4',
				'Bildlink5',
				'Bildlink6',
				'Zustand',
				'Familienname1',
				'Eigenschaft1',
				'Familienname2',
				'Eigenschaft2',
				'ID',
				'Einheit',
				'Inhalt',
				'Freifeld1',
				'Freifeld2',
				'Freifeld3',
				'Freifeld4',
				'Freifeld5',
				'Freifeld6',
				'Freifeld7',
				'Freifeld8',
				'Freifeld9',
				'Freifeld10',
				'baseid',
				'basename',
				'level',
				'status',
				'external_categories',
				'base',
				'dealer_price',
				'link'		 ,
				'ASIN',
				'Mindestabnahme',
				'Maximalabnahme',
				'Abnahmestaffelung',
				'Energieefiizienz',
				'Energieefiizienzbild',
				'UVP',
				'EVP',
			*/]);
			$currentItemId = null;
			$previousItemId = null;
			$variations = array();
			foreach($resultData as $variation)
			{
				// Case first variation
				if ($currentItemId === null)
				{
					$previousItemId = $variation->itemBase->id;
                                        
				}
				$currentItemId = $variation->itemBase->id;
				// Check if it's the same item
				if ($currentItemId == $previousItemId)
				{
					$variations[] = $variation;
				}
				else
				{
					$this->buildRows($settings, $variations);
					$variations = array();
					$variations[] = $variation;
					$previousItemId = $variation->itemBase->id;
				}
			}
			// Write the las batch of variations
			if (is_array($variations) && count($variations) > 0)
			{
				$this->buildRows($settings, $variations);
			}
		}
	}
	/**
	 * @param $settings
	 * @param RecordList $variations
	 */
	private function buildRows($settings, $variations)
	{
		if (is_array($variations) && count($variations) > 0)
		{
			$primaryVariationKey = null;
			foreach($variations as $key => $variation)
			{
				/**
				 * Select and save the attribute name order for the first variation of each item with attributes,
				 * if the variation has attributes
				 */
				if (is_array($variation->variationAttributeValueList) &&
					count($variation->variationAttributeValueList) > 0 &&
					!array_key_exists($variation->itemBase->id, $this->attributeName) &&
					!array_key_exists($variation->itemBase->id, $this->attributeNameCombination))
				{
					$this->attributeName[$variation->itemBase->id] = $this->vaolaHelper->getAttributeName($variation, $settings);
					foreach ($variation->variationAttributeValueList as $attribute)
					{
						$attributeNameCombination[$variation->itemBase->id][] = $attribute->attributeId;
					}
				}
				// note key of primary variation
				if($variation->variationBase->primaryVariation === true)
				{
					$primaryVariationKey = $key;
                                        
				}
			}
			// change sort of array and add primary variation as first entry
			if(!is_null($primaryVariationKey))
			{
				$primaryVariation = $variations[$primaryVariationKey];
				unset($variations[$primaryVariationKey]);
				array_unshift($variations, $primaryVariation);
			}
			$i = 1;
			foreach($variations as $key => $variation)
			{
				/**
				 * gets the attribute value name of each attribute value which is linked with the variation in a specific order,
				 * which depends on the $attributeNameCombination
				 */
				$attributeValue = $this->vaolaHelper->getAttributeValueSetShortFrontendName($variation, $settings, '|', $this->attributeNameCombination[$variation->itemBase->id]);
				if(count($variations) == 1)
				{
					$this->buildParentWithoutChildrenRow($variation, $settings);
				}
				elseif($variation->variationBase->primaryVariation === false && $i == 1)
				{
					//$this->buildParentWithChildrenRow($variation, $settings, $this->attributeName);
					$this->buildChildRow($variation, $settings, $attributeValue);
				}
				elseif($variation->variationBase->primaryVariation === true && strlen($attributeValue) > 0)
				{
					//$this->buildParentWithChildrenRow($variation, $settings, $this->attributeName);
					$this->buildChildRow($variation, $settings, $attributeValue);
				}
				elseif($variation->variationBase->primaryVariation === true && strlen($attributeValue) == 0)
				{
					//$this->buildParentWithChildrenRow($variation, $settings, $this->attributeName);
				}
				else
				{
					$this->buildChildRow($variation, $settings, $attributeValue);
				}
				$i++;
			}
		}
	}
	/**
	 * @param Record $item
	 * @param KeyValue $settings
	 * @return void
	 */
	private function buildParentWithoutChildrenRow(Record $item, KeyValue $settings)
	{
            
            
            $sattributes = $this->vaolaHelper->getAttributeValueSetShortFrontendName($item, $settings);
            $aattributes = explode(",", $sattributes);            
            $sattributenames = $this->vaolaHelper->getAttributeName($item, $settings);
            $aattributenames = explode(" ", $sattributenames);            
            $primarycolor = "";
            $size = "";
            $sizetype = "";
            
            for($i = 0; $i < count($aattributenames); $i++){                
                if($aattributenames[$i] == "Farbe"){
                    $primarycolor = $aattributes[$i];
                }
                elseif($aattributenames[$i] == "Rahmengröße"){
                    $size = $aattributes[$i];
                    $sizetype = "Rahmengröße";
                    
                }
                 elseif($aattributenames[$i] == "Größe"){
                    $size = $aattributes[$i];
                    $sizetype = "Größe";
                    
                }
            }
            
            if($size == ""){
                $sizetype = "Rahmengröße";
                $size = $this->vaolaHelper->getSize($item, $settings);                   
            }
            
            
            if($primarycolor == ""){                
                $primarycolor = $this->vaolaHelper->getColor($item, $settings);                
            }     
            
            $sportart = "";
            $sportart = $item->itemBase->free8;
            if($sportart == "" || $sportart == 0){
                $sportart = "Radsport";
            }
            
            
            $vk = number_format($this->vaolaHelper->getPrice($item), 2, '.', '');
            $uvp = number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', '');
            if($uvp == "0.00"){
                $uvp = $vk;
            }
            
            if($size == ""){
                $size = "Unisize";
            }
            
                
                            
            
            
            
            
            
        $vat = $this->getVatClassId($item);
        $stockList = $this->getStockList($item);
        $priceList = $this->getPriceList($item, $settings);
        $basePriceComponentList = $this->getBasePriceComponentList($item);
		$data = [
                    'ID'				=> $item->itemBase->id,
                    'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
                    'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
                    'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
                    'p_catpri[vaola]'                   => $item->itemBase->free7, 
                    'p_active[vaola]'                   => '1', 
                    'p_active[msde]'                    => '1', 
                    'a_comp[Primärfarbe]'               => $primarycolor, 
                    'p_tag[Sekundärfarbe]'              => '', 
                    'p_tag[Größenart]'                  => $sizetype, 
                    'p_tag[Geschlecht]'                 => $this->vaolaHelper->getGender($item, $settings), 
                    'p_tag[Sportart]'                   => $sportart, 
                    'p_tag[Material]'                   => '', 
                    'a_nr'                              => $item->variationBase->id,
                    'a_prodnr'                          => $item->variationBase->id,
                    'a_ean'                             => $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
                    'a_comp[Größe]'                     => $size, 
                    'a_vk[msde]'                        => $vk,
                    'a_uvp[msde]'                       => $uvp,
                    'a_mwst[msde]'                      => '2', 
                    'a_media[image]{0}'                 => $this->getImageByNumber($item, $settings, 0),
                    'a_media[image]{1}'                 => $this->getImageByNumber($item, $settings, 1),
                    'a_media[image]{2}'                 => $this->getImageByNumber($item, $settings, 2), 
                    'a_media[image]{3}'                 => $this->getImageByNumber($item, $settings, 3), 
                    'a_media[image]{4}'                 => $this->getImageByNumber($item, $settings, 4), 
                    'a_active'                          => '1', 
                    'a_stock'                           => $stockList['stock'],
                    'a_delivery'                        => $this->vaolaHelper->getAvailability($item, $settings, false),
                    'a_shipping_type'                   => 'SPED', 
                    
                    
                    
                    
			/*'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
			'Artikelnummer'			=> $item->itemBase->id,
			'ean'				=> $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
			'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
			'Steuersatz'			=> $item->variationRetailPrice->vatValue,
			'Preis'				=> number_format($this->vaolaHelper->getPrice($item), 2, '.', ''),
			'Kurzbeschreibung'		=> '',
			'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
			'Versandkosten'			=> '',
			'Lagerbestand'			=> $stockList['stock'],
			'Kategoriestruktur'		=> '',
			'Attribute'			=> '',
			'Gewicht'			=> $item->variationBase->weightG,
			'Lieferzeit'			=> $this->vaolaHelper->getAvailability($item, $settings, false),
			'Nachnahmegebühr'		=> '',
			'MPN'				=> $item->variationBase->model,
			'Bildlink'			=> $this->getImageByNumber($item, $settings, 0),
			'Bildlink2'			=> $this->getImageByNumber($item, $settings, 1),
			'Bildlink3'			=> $this->getImageByNumber($item, $settings, 2),
			'Bildlink4'			=> $this->getImageByNumber($item, $settings, 3),
			'Bildlink5'			=> $this->getImageByNumber($item, $settings, 4),
			'Bildlink6'			=> $this->getImageByNumber($item, $settings, 5),
			'Zustand'			=> 'neu',
			'Familienname1'			=> '',
			'Eigenschaft1'			=> '',
			'Familienname2'			=> '',
			'Eigenschaft2'			=> '',
			'ID'				=> $item->variationBase->id, //$item->itemBase->id,
			'Einheit'			=> $basePriceComponentList['unit'],
			'Inhalt'			=> strlen($basePriceComponentList['unit']) ? number_format((float)$basePriceComponentList['content'],3,',','') : '',
			'Freifeld1'			=> $item->itemBase->free1,
			'Freifeld2'			=> $item->itemBase->free2,
			'Freifeld3'			=> $item->itemBase->free3,
			'Freifeld4'			=> $item->itemBase->free4,
			'Freifeld5'			=> $item->itemBase->free5,
			'Freifeld6'			=> $item->itemBase->free6,
			'Freifeld7'			=> $item->itemBase->free7,
			'Freifeld8'			=> $item->itemBase->free8,
			'Freifeld9'			=> $item->itemBase->free9,
			'Freifeld10'			=> $item->itemBase->free10,
			'baseid'			=> 'BASE-'.$item->itemBase->id,
			'basename'			=> $attributeValue,
			'level'				=> '0',
			'status'			=> $variationAvailable,
			'external_categories'		=> '1', //$item->variationStandardCategory->categoryId,
			'base'				=> '3',
			'dealer_price'			=> '',
			'link'				=> '',
			'ASIN'				=> '',
			'Mindestabnahme'		=> '',
			'Maximalabnahme'		=> '',
			'Abnahmestaffelung'		=> '',
			'Energieefiizienz'		=> $this->getItemPropertyByExternalComponent($item, 106.00, self::PROPERTY_TYPE_ENERGY_CLASS),
			'Energieefiizienzbild'		=> '',
			'UVP'				=> number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', ''),
			'EVP'				=> number_format($this->vaolaHelper->getSpecialPrice($item, $settings), 2, '.', ''),*/
		];
                
                
                
		$this->addCSVContent(array_values($data));
	}
	/**
	 * @param Record $item
	 * @param KeyValue $settings
     * @param array $attributeName
	 * @return void
	 */
	private function buildParentWithChildrenRow(Record $item, KeyValue $settings, array $attributeName)
	{
            
            
            $sattributes = $this->vaolaHelper->getAttributeValueSetShortFrontendName($item, $settings);
            $aattributes = explode(",", $sattributes);            
            $sattributenames = $this->vaolaHelper->getAttributeName($item, $settings);
            $aattributenames = explode(" ", $sattributenames);            
            $primarycolor = "";
            $size = "";
            $sizetype = "";
            
            for($i = 0; $i < count($aattributenames); $i++){                
                if($aattributenames[$i] == "Farbe"){
                    $primarycolor = $aattributes[$i];
                }
                elseif($aattributenames[$i] == "Rahmengröße"){
                    $size = $aattributes[$i];
                    $sizetype = "Rahmengröße";                    
                }
                 elseif($aattributenames[$i] == "Größe"){
                    $size = $aattributes[$i];
                    $sizetype = "Größe";                    
                }
            }
            
            if($primarycolor == ""){                
                $primarycolor = $this->vaolaHelper->getColor($item, $settings);                
            }     
            
            $sportart = "";
            $sportart = $item->itemBase->free8;
            if($sportart == "" || $sportart == 0){
                $sportart = "Radsport";
            }
            
            
            $vk = number_format($this->vaolaHelper->getPrice($item), 2, '.', '');
            $uvp = number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', '');
            if($uvp == "0.00"){
                $uvp = $vk;
            }
            
            if($size == ""){
                $size = "Unisize";
            }
            
            
            
        $vat = $this->getVatClassId($item);
        $stockList = $this->getStockList($item);
		$data = [
                    'ID'				=> $item->itemBase->id,
                    'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
                    'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
                    'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
                    'p_catpri[vaola]'                   => $item->itemBase->free7, 
                    'p_active[vaola]'                   => '1', 
                    'p_active[msde]'                    => '1', 
                    'a_comp[Primärfarbe]'               => $primarycolor, 
                    'p_tag[Sekundärfarbe]'              => '', 
                    'p_tag[Größenart]'                  => $sizetype, 
                    'p_tag[Geschlecht]'                 => $this->vaolaHelper->getGender($item, $settings), 
                    'p_tag[Sportart]'                   => $sportart, 
                    'p_tag[Material]'                   => '', 
                    'a_nr'                              => '',
                    'a_prodnr'                          => $item->variationBase->id,
                    'a_ean'                             => $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
                    'a_comp[Größe]'                     => $size, 
                    'a_vk[msde]'                        => $vk,
                    'a_uvp[msde]'                       => $uvp,
                    'a_mwst[msde]'                      => '2', 
                    'a_media[image]{0}'                 => $this->getImageByNumber($item, $settings, 0),
                    'a_media[image]{1}'                 => $this->getImageByNumber($item, $settings, 1),
                    'a_media[image]{2}'                 => $this->getImageByNumber($item, $settings, 2), 
                    'a_media[image]{3}'                 => $this->getImageByNumber($item, $settings, 3), 
                    'a_media[image]{4}'                 => $this->getImageByNumber($item, $settings, 4), 
                    'a_active'                          => '1', 
                    'a_stock'                           => $stockList['stock'],
                    'a_delivery'                        => $this->vaolaHelper->getAvailability($item, $settings, false),
                    'a_shipping_type'                   => 'SPED', 
                    
                    
                    
                    
                    
                    /*
			'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
			'Artikelnummer'			=> $item->itemBase->id,
			'ean'				=> $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
			'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
			'Steuersatz'			=> $item->variationRetailPrice->vatValue,
			'Preis'				=> number_format($this->vaolaHelper->getPrice($item), 2, '.', ''),
			'Kurzbeschreibung'		=> '',
			'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
			'Versandkosten'			=> '',
			'Lagerbestand'			=> '0',
			'Kategoriestruktur'		=> '',
			'Attribute'			=> '',
			'Gewicht'			=> $item->variationBase->weightG,
			'Lieferzeit'			=> $this->vaolaHelper->getAvailability($item, $settings, false),
			'Nachnahmegebühr'		=> '',
			'MPN'				=> $item->variationBase->model,
			'Bildlink'			=> $this->getImageByNumber($item, $settings, 0),
			'Bildlink2'			=> $this->getImageByNumber($item, $settings, 1),
			'Bildlink3'			=> $this->getImageByNumber($item, $settings, 2),
			'Bildlink4'			=> $this->getImageByNumber($item, $settings, 3),
			'Bildlink5'			=> $this->getImageByNumber($item, $settings, 4),
			'Bildlink6'			=> $this->getImageByNumber($item, $settings, 5),
			'Zustand'			=> 'neu',
			'Familienname1'			=> '',
			'Eigenschaft1'			=> '',
			'Familienname2'			=> '',
			'Eigenschaft2'			=> '',
			'ID'				=> '',
			'Einheit'			=> '',
			'Inhalt'			=> '',
			'Freifeld1'			=> $item->itemBase->free1,
			'Freifeld2'			=> $item->itemBase->free2,
			'Freifeld3'			=> $item->itemBase->free3,
			'Freifeld4'			=> $item->itemBase->free4,
			'Freifeld5'			=> $item->itemBase->free5,
			'Freifeld6'			=> $item->itemBase->free6,
			'Freifeld7'			=> $item->itemBase->free7,
			'Freifeld8'			=> $item->itemBase->free8,
			'Freifeld9'			=> $item->itemBase->free9,
			'Freifeld10'			=> $item->itemBase->free10,
			'baseid'			=> '',
			'basename'			=> '',
			'level'				=> '0',
			'status'			=> '0',
			'external_categories'		=> '1', //$item->variationStandardCategory->categoryId,
			'base'				=> '1',
			'dealer_price'			=> '',
			'link'				=> '',
			'ASIN'				=> '',
			'Mindestabnahme'		=> '',
			'Maximalabnahme'		=> '',
			'Abnahmestaffelung'		=> '',
			'Energieefiizienz'		=> $this->getItemPropertyByExternalComponent($item, 106.00, self::PROPERTY_TYPE_ENERGY_CLASS),
			'Energieefiizienzbild'		=> '',
			'UVP'				=> number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', ''),
			'EVP'				=> number_format($this->vaolaHelper->getSpecialPrice($item, $settings), 2, '.', ''),*/
		];
		
 
                $this->addCSVContent(array_values($data));
                
	}
	/**
	 * @param Record $item
	 * @param KeyValue $settings
     * @param string $attributeValue
	 * @return void
	 */
	private function buildChildRow(Record $item, KeyValue $settings, string $attributeValue = '')
	{
            $sattributes = $this->vaolaHelper->getAttributeValueSetShortFrontendName($item, $settings);
            $aattributes = explode(",", $sattributes);            
            $sattributenames = $this->vaolaHelper->getAttributeName($item, $settings);
            $aattributenames = explode(" ", $sattributenames);            
            $primarycolor = "";
            $size = "";
            $sizetype = "";
            
            for($i = 0; $i < count($aattributenames); $i++){                
                if($aattributenames[$i] == "Farbe"){
                    $primarycolor = $aattributes[$i];
                }
                elseif($aattributenames[$i] == "Rahmengröße"){
                    $size = $aattributes[$i];
                    $sizetype = "Rahmengröße";                    
                }
                 elseif($aattributenames[$i] == "Größe"){
                    $size = $aattributes[$i];
                    $sizetype = "Größe";                    
                }
            }
            
            if($size == ""){
                $sizetype = "Rahmengröße";
                $size = $this->vaolaHelper->getSize($item, $settings);                   
            }
            
            if($primarycolor == ""){                
                $primarycolor = $this->vaolaHelper->getColor($item, $settings);                
            }     
            
            $sportart = "";
            $sportart = $item->itemBase->free8;
            if($sportart == "" || $sportart == 0){
                $sportart = "Radsport";
            }
            
             $vk = number_format($this->vaolaHelper->getPrice($item), 2, '.', '');
            $uvp = number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', '');
            if($uvp == "0.00"){
                $uvp = $vk;
            }
            
            if($size == ""){
                $size = "Unisize";
            }
           
            
        $stockList = $this->getStockList($item);
        
		$data = [                    
                    'ID'				=> $item->itemBase->id,
                    'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
                    'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
                    'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
                    'p_catpri[vaola]'                   => $item->itemBase->free7, 
                    'p_active[vaola]'                   => '1', 
                    'p_active[msde]'                    => '1', 
                    'a_comp[Primärfarbe]'               => $primarycolor, 
                    'p_tag[Sekundärfarbe]'              => '', 
                    'p_tag[Größenart]'                  => $sizetype, 
                    'p_tag[Geschlecht]'                 => $this->vaolaHelper->getGender($item, $settings), 
                    'p_tag[Sportart]'                   => $sportart,
                    'p_tag[Material]'                   => '', 
                    'a_nr'                              => $item->variationBase->id,
                    'a_prodnr'                          => $item->variationBase->id,
                    'a_ean'                             => $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
                    'a_comp[Größe]'                     => $size, 
                    'a_vk[msde]'                        => $vk,
                    'a_uvp[msde]'                       => $uvp,
                    'a_mwst[msde]'                      => '2', 
                    'a_media[image]{0}'                 => $this->getImageByNumber($item, $settings, 0),
                    'a_media[image]{1}'                 => $this->getImageByNumber($item, $settings, 1),
                    'a_media[image]{2}'                 => $this->getImageByNumber($item, $settings, 2), 
                    'a_media[image]{3}'                 => $this->getImageByNumber($item, $settings, 3), 
                    'a_media[image]{4}'                 => $this->getImageByNumber($item, $settings, 4), 
                    'a_active'                          => '1', 
                    'a_stock'                           => $stockList['stock'],
                    'a_delivery'                        => $this->vaolaHelper->getAvailability($item, $settings, false),
                    'a_shipping_type'                   => 'SPED', 
                    
                    
                    
                    
                    
                    
                    
                    
                    /*
			'Produktname'			=> $this->vaolaHelper->getName($item, $settings, 150),
			'Artikelnummer'			=> $item->itemBase->id,
			'ean'				=> $this->vaolaHelper->getBarcodeByType($item, $settings->get('barcode')),
			'Hersteller'			=> $this->vaolaHelper->getExternalManufacturerName($item->itemBase->producerId),
			'Steuersatz'			=> $item->variationRetailPrice->vatValue,
			'Preis'				=> number_format($this->vaolaHelper->getPrice($item), 2, '.', ''),
			'Kurzbeschreibung'		=> '',
			'Beschreibung'			=> $this->vaolaHelper->getDescription($item, $settings, 5000),
			'Versandkosten'			=> '',
			'Lagerbestand'			=> $stockList['stock'],
			'Kategoriestruktur'		=> '',
			'Attribute'			=> '',
			'Gewicht'			=> $item->variationBase->weightG,
			'Lieferzeit'			=> $this->vaolaHelper->getAvailability($item, $settings, false),
			'Nachnahmegebühr'		=> '',
			'MPN'				=> $item->variationBase->model,
			'Bildlink'			=> $this->getImageByNumber($item, $settings, 0),
			'Bildlink2'			=> $this->getImageByNumber($item, $settings, 1),
			'Bildlink3'			=> $this->getImageByNumber($item, $settings, 2),
			'Bildlink4'			=> $this->getImageByNumber($item, $settings, 3),
			'Bildlink5'			=> $this->getImageByNumber($item, $settings, 4),
			'Bildlink6'			=> $this->getImageByNumber($item, $settings, 5),
			'Zustand'			=> 'neu',
			'Familienname1'			=> '',
			'Eigenschaft1'			=> '',
			'Familienname2'			=> '',
			'Eigenschaft2'			=> '',
			'ID'				=> $item->variationBase->id,
			'Einheit'			=> $basePriceComponentList['unit'],
			'Inhalt'			=> strlen($basePriceComponentList['unit']) ? number_format((float)$basePriceComponentList['content'],3,',','') : '',
			'Freifeld1'			=> $item->itemBase->free1,
			'Freifeld2'			=> $item->itemBase->free2,
			'Freifeld3'			=> $item->itemBase->free3,
			'Freifeld4'			=> $item->itemBase->free4,
			'Freifeld5'			=> $item->itemBase->free5,
			'Freifeld6'			=> $item->itemBase->free6,
			'Freifeld7'			=> $item->itemBase->free7,
			'Freifeld8'			=> $item->itemBase->free8,
			'Freifeld9'			=> $item->itemBase->free9,
			'Freifeld10'			=> $item->itemBase->free10,
			'baseid'			=> 'BASE-'.$item->itemBase->id,
			'basename'			=> $attributeValue, 
			'level'				=> '0',
			'status'			=> $variationAvailable,
			'external_categories'		=> '1', //$item->variationStandardCategory->categoryId,
			'base'				=> '3',
			'dealer_price'			=> '',
			'link'				=> '',
			'ASIN'				=> '',
			'Mindestabnahme'		=> '',
			'Maximalabnahme'		=> '',
			'Abnahmestaffelung'		=> '',
			'Energieefiizienz'		=> $this->getItemPropertyByExternalComponent($item, 106.00, self::PROPERTY_TYPE_ENERGY_CLASS),
			'Energieefiizienzbild'		=> '',
			'UVP'				=> number_format($this->vaolaHelper->getRecommendedRetailPrice($item, $settings), 2, '.', ''),
			'EVP'				=> number_format($this->vaolaHelper->getSpecialPrice($item, $settings), 2, '.', ''),
		*/];
		$this->addCSVContent(array_values($data));
	}
	/**
	 * @param Record $item
	 * @param KeyValue $settings
	 * @param int $number
	 * @return string
	 */
	private function getImageByNumber(Record $item, KeyValue $settings, int $number):string
	{
		$imageList = $this->vaolaHelper->getImageList($item, $settings);
		if(count($imageList) > 0 && array_key_exists($number, $imageList))
		{
			return (string)$imageList[$number];
		}
		else
		{
			return '';
		}
	}
	/**
	 * Returns the unit, if there is any unit configured, which is allowed
	 * for the vaola.de API.
	 *
	 * @param  Record   $item
	 * @return string
	 */
	private function getUnit(Record $item):string
	{
		switch((int) $item->variationBase->unitId)
		{
			case '32':
				return 'ml'; // Milliliter
			case '5':
				return 'l'; // Liter
			case '3':
				return 'g'; // Gramm
			case '2':
				return 'kg'; // Kilogramm
			case '51':
				return 'cm'; // Zentimeter
			case '31':
				return 'm'; // Meter
			case '38':
				return 'm²'; // Quadratmeter
			default:
				return '';
		}
	}
    /**
     * Get id for vat
     * @param Record $item
     * @return int
     */
	private function getVatClassId(Record $item):int
    {
        $vat = $item->variationRetailPrice->vatValue;
        if($vat == '10,7')
        {
            $vat = 4;
        }
        else if($vat == '7')
        {
            $vat = 2;
        }
        else if($vat == '0')
        {
            $vat = 3;
        }
        else
        {
            //bei anderen Steuersaetzen immer 19% nehmen
            $vat = 1;
        }
        return $vat;
    }
	/**
	 * Get item characters that match referrer from settings and a given component id.
	 * @param  Record   $item
	 * @param  float    $marketId
	 * @param  string  $externalComponent
	 * @return string
	 */
	private function getItemPropertyByExternalComponent(Record $item, float $marketId, $externalComponent):string
	{
		$marketProperties = $this->marketPropertyHelperRepository->getMarketProperty($marketId);
		foreach($item->itemPropertyList as $property)
		{
			foreach($marketProperties as $marketProperty)
			{
				if(is_array($marketProperty) && count($marketProperty) > 0 && $marketProperty['character_item_id'] == $property->propertyId)
				{
					if (strlen($externalComponent) > 0 && strpos($marketProperty['external_component'], $externalComponent) !== false)
					{
						$list = explode(':', $marketProperty['external_component']);
						if (isset($list[1]) && strlen($list[1]) > 0)
						{
							return $list[1];
						}
					}
				}
			}
		}
		return '';
	}
    /**
     * Get necessary components to enable vaola to calculate a base price for the variation
     * @param Record $item
     * @return array
     */
	private function getBasePriceComponentList(Record $item):array
    {
        $unit = $this->getUnit($item);
        $content = (float)$item->variationBase->content;
        $convertBasePriceContentTag = $this->vaolaHelper->getConvertContentTag($content, 3);
        if ($convertBasePriceContentTag == true && strlen($unit))
        {
            $content = $this->vaolaHelper->getConvertedBasePriceContent($content, $unit);
            $unit = $this->vaolaHelper->getConvertedBasePriceUnit($unit);
        }
        return array(
            'content'   =>  $content,
            'unit'      =>  $unit,
        );
    }
    /**
     * Get all informations that depend on stock settings and stock volume
     * (inventoryManagementActive, $variationAvailable, $stock)
     * @param Record $item
     * @return array
     */
    private function getStockList(Record $item):array
    {
        $inventoryManagementActive = 0;
        $variationAvailable = 0;
        $stock = 0;
        if($item->variationBase->limitOrderByStockSelect == 2)
        {
            $variationAvailable = 1;
            $inventoryManagementActive = 0;
            $stock = 999;
        }
        elseif($item->variationBase->limitOrderByStockSelect == 1 && $item->variationStock->stockNet > 0)
        {
            $variationAvailable = 1;
            $inventoryManagementActive = 1;
            if($item->variationStock->stockNet > 999)
            {
                $stock = 999;
            }
            else
            {
                $stock = $item->variationStock->stockNet;
            }
        }
        elseif($item->variationBase->limitOrderByStockSelect == 0)
        {
            $variationAvailable = 1;
            $inventoryManagementActive = 0;
            if($item->variationStock->stockNet > 999)
            {
                $stock = 999;
            }
            else
            {
                if($item->variationStock->stockNet > 0)
                {
                    $stock = $item->variationStock->stockNet;
                }
                else
                {
                    $stock = 0;
                }
            }
        }
        return array (
            'stock'                     =>  $stock,
            'variationAvailable'        =>  $variationAvailable,
            'inventoryManagementActive' =>  $inventoryManagementActive,
        );
    }
	
	/**
     * Get a List of price, reduced price and the reference for the reduced price.
     * @param Record $item
     * @param KeyValue $settings
     * @return array
     */
    private function getPriceList(Record $item, KeyValue $settings):array
    {
        $variationPrice = $this->vaolaHelper->getPrice($item);
        $variationRrp = $this->vaolaHelper->getRecommendedRetailPrice($item, $settings);
        $variationSpecialPrice = $this->vaolaHelper->getSpecialPrice($item, $settings);
        //setting retail price as selling price without a reduced price
        $price = $variationPrice;
        $reducedPrice = '';
        $referenceReducedPrice = '';
        if ($price != '' || $price != 0.00)
        {
            //if recommended retail price is set and higher than retail price...
            if ($variationRrp > 0 && $variationRrp > $variationPrice)
            {
                //set recommended retail price as selling price
                $price = $variationRrp;
                //set retail price as reduced price
                $reducedPrice = $variationPrice;
                //set recommended retail price as reference
                $referenceReducedPrice = 'UVP';
            }
            // if special offer price is set and lower than retail price and recommended retail price is already set as reference...
            if ($variationSpecialPrice > 0 && $variationPrice > $variationSpecialPrice && $referenceReducedPrice == 'UVP')
            {
                //set special offer price as reduced price
                $reducedPrice = $variationSpecialPrice;
            }
            //if recommended retail price is not set as reference then ...
            elseif ($variationSpecialPrice > 0 && $variationPrice > $variationSpecialPrice)
            {
                //set special offer price as reduced price and...
                $reducedPrice = $variationSpecialPrice;
                //set retail price as reference
                $referenceReducedPrice = 'VK';
            }
        }
        return array(
            'price'                     =>  $price,
            'reducedPrice'              =>  $reducedPrice,
            'referenceReducedPrice'     =>  $referenceReducedPrice
        );
    }
}