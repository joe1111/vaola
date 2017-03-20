<?php
namespace vaola\Helper;

use Plenty\Modules\Item\VariationImage\Contracts\VariationImageRepositoryContract;
use Plenty\Modules\Item\ItemImage\Contracts\ItemImageAvailabilityRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
use Plenty\Modules\Category\Contracts\CategoryBranchMarketplaceRepositoryContract;
use Plenty\Modules\Category\Contracts\CategoryBranchRepositoryContract;
use Plenty\Modules\Category\Models\CategoryBranchMarketplace;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Category\Models\CategoryBranch;
use Plenty\Modules\Item\Property\Models\PropertyName;
use Plenty\Modules\Market\Helper\Contracts\MarketItemHelperRepositoryContract;
use Plenty\Modules\Market\Helper\Contracts\MarketCategoryHelperRepositoryContract;
use Plenty\Modules\Market\Helper\Contracts\MarketPropertyHelperRepositoryContract;
use Plenty\Modules\Market\Helper\Contracts\MarketAttributeHelperRepositoryContract;
use Plenty\Modules\Item\Unit\Contracts\UnitNameRepositoryContract;
use Plenty\Modules\Item\Unit\Models\UnitName;
use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Helper\Contracts\UrlBuilderRepositoryContract;
use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Modules\Category\Models\Category;
use Plenty\Modules\Order\Shipping\Models\DefaultShipping;
use Plenty\Modules\Order\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Item\DefaultShippingCost\Contracts\DefaultShippingCostRepositoryContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
use Plenty\Modules\System\Models\Webstore;
/**
 * Class vaolaHelper
 * @package vaolaHelper\Helper
 */
class vaolaHelper
{
    const SHIPPING_COST_TYPE_FLAT = 'flat';
    const SHIPPING_COST_TYPE_CONFIGURATION = 'configuration';
    const IMAGE_POSITION0 = 'position0';
    const IMAGE_FIRST = 'firstImage';
    const REMOVE_HTML_TAGS = 1;
    const KEEP_HTML_TAGS = 0;
    const ITEM_URL_NO = 0;
    const ITEM_URL_YES = 1;
    const TRANSFER_ITEM_AVAILABILITY_NO = 0;
    const TRANSFER_ITEM_AVAILABILITY_YES = 1;
    const TRANSFER_OFFER_PRICE_NO = 0;
    const TRANSFER_OFFER_PRICE_YES = 1;
    const TRANSFER_RRP_YES = 1;
    const TRANSFER_RRP_NO = 0;
    const BARCODE_EAN = 'EAN_13';
    const BARCODE_ISBN = 'ISBN';
    
    
    private $itemImageAvailabilityRepositoryContract;
    
    private $variationImageRepositoryContract;
    
    private $propertyRepositoryContract;
    
    private $variationRepositoryContract;
    
    /**
     * CategoryBranchRepositoryContract $categoryBranchRepository
     */
    private $categoryBranchRepository;
    /**
     * UnitNameRepositoryContract $unitNameRepository
     */
    private $unitNameRepository;
    /**
     * PropertyItemNameRepositoryContract $propertyItemNameRepository
     */
    private $propertyNameRepository;
        /**
     * CategoryBranchMarketplaceRepositoryContract $categoryBranchMarketplaceRepository
     */
    private $categoryBranchMarketplaceRepository;
    /**
     * UrlBuilderRepositoryContract $urlBuilderRepository
     */
    private $urlBuilderRepository;
    /**
     * CategoryRepositoryContract $categoryRepository
     */
    private $categoryRepository;
    /**
     * @var PaymentMethodRepositoryContract $paymentMethodRepository
     */
    private $paymentMethodRepository;
    /**
     * @var DefaultShippingCostRepositoryContract $defaultShippingCostRepository
     */
    private $defaultShippingCostRepository;
    /**
     * ConfigRepository $configRepository
     */
    private $configRepository;
    /**
     * CountryRepositoryContract $countryRepository
     */
    private $countryRepository;
    /**
     * WebstoreRepositoryContract $webstoreRepository
     */
    private $webstoreRepository;
	/**
	 * MarketItemHelperRepositoryContract $marketItemHelperRepository
	 */
	private $marketItemHelperRepository;
	/**
	 * MarketCategoryHelperRepositoryContract $marketCategoryHelperRepository
	 */
	private $marketCategoryHelperRepository;
	/**
	 * MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
	 */
	private $marketPropertyHelperRepository;
	/**
	 * MarketAttributeHelperRepositoryContract $marketAttributeHelperRepository
	 */
	private $marketAttributeHelperRepository;
    /**
     * vaolaHelper constructor.
     *
     * @param CategoryBranchRepositoryContract $categoryBranchRepository
     * @param UnitNameRepositoryContract $unitNameRepository
     * @param PropertyNameRepositoryContract $propertyNameRepository
     * @param CategoryBranchMarketplaceRepositoryContract $categoryBranchMarketplaceRepository
     * @param UrlBuilderRepositoryContract $urlBuilderRepository
     * @param CategoryRepositoryContract $categoryRepository
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param ConfigRepository $configRepository
     * @param CountryRepositoryContract $countryRepository
     * @param WebstoreRepositoryContract $webstoreRepository
	 * @param MarketItemHelperRepositoryContract $marketItemHelperRepository
	 * @param MarketCategoryHelperRepositoryContract $marketCategoryHelperRepository
	 * @param MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository
	 * @param MarketAttributeHelperRepositoryContract $marketAttributeHelperRepository
     */
    public function __construct(
            VariationImageRepositoryContract $variationImageRepositoryContract,
            ItemImageAvailabilityRepositoryContract $itemImageAvailabilityRepositoryContract,
            VariationRepositoryContract $variationRepositoryContract,
            PropertyRepositoryContract $propertyRepositoryContract,
                                CategoryBranchRepositoryContract $categoryBranchRepository,
                                UnitNameRepositoryContract $unitNameRepository,
                                PropertyNameRepositoryContract $propertyNameRepository,                                
                                CategoryBranchMarketplaceRepositoryContract $categoryBranchMarketplaceRepository,
                                UrlBuilderRepositoryContract $urlBuilderRepository,
                                CategoryRepositoryContract $categoryRepository,
                        		PaymentMethodRepositoryContract $paymentMethodRepository,
                                DefaultShippingCostRepositoryContract $defaultShippingCostRepository,
                                ConfigRepository $configRepository,
                                CountryRepositoryContract $countryRepository,
                                WebstoreRepositoryContract $webstoreRepository,
								MarketItemHelperRepositoryContract $marketItemHelperRepository,
								MarketCategoryHelperRepositoryContract $marketCategoryHelperRepository,
								MarketPropertyHelperRepositoryContract $marketPropertyHelperRepository,
								MarketAttributeHelperRepositoryContract $marketAttributeHelperRepository
    )
    {
        $this->variationImageRepositoryContract = $variationImageRepositoryContract;
        $this->itemImageAvailabilityRepositoryContract = $itemImageAvailabilityRepositoryContract;
        $this->variationRepositoryContract = $variationRepositoryContract;
        $this->propertyRepositoryContract = $propertyRepositoryContract;
        $this->categoryBranchRepository = $categoryBranchRepository;
		$this->unitNameRepository = $unitNameRepository;
		$this->propertyNameRepository = $propertyNameRepository;                
        $this->categoryBranchMarketplaceRepository = $categoryBranchMarketplaceRepository;
        $this->urlBuilderRepository = $urlBuilderRepository;
        $this->categoryRepository = $categoryRepository;
		$this->paymentMethodRepository = $paymentMethodRepository;
        $this->defaultShippingCostRepository = $defaultShippingCostRepository;
        $this->configRepository = $configRepository;
        $this->countryRepository = $countryRepository;
        $this->webstoreRepository = $webstoreRepository;
		$this->marketItemHelperRepository = $marketItemHelperRepository;
		$this->marketCategoryHelperRepository = $marketCategoryHelperRepository;
		$this->marketPropertyHelperRepository = $marketPropertyHelperRepository;
		$this->marketAttributeHelperRepository = $marketAttributeHelperRepository;
    }
    /**
     * Get name.
     *
     * @param  Record    $item
     * @param  KeyValue  $settings
     * @param  int $defaultNameLength
     * @return string
     */
    public function getName(Record $item, KeyValue $settings, int $defaultNameLength = 0):string
	{
		$name = '';
		switch($settings->get('nameId'))
		{
			case 3:
				$name = (string)$item->itemDescription->name3;
                break;
			case 2:
				$name = (string)$item->itemDescription->name2;
                break;
			case 1:
			default:
				$name = (string)$item->itemDescription->name1;
                break;
		}
                
                $name = $this->cleanName($name, (int)$settings->get('nameMaxLength') ? (int)$settings->get('nameMaxLength') : (int)$defaultNameLength);
                
                
                
        return $name;
    }
    /**
     * Clean name to a defined length. If maxLength is 0 than named is returned intact.
     * @param  string 	$name
     * @param  int 		$maxLength
     * @return string
     */
    public function cleanName(string $name, int $maxLength = 0):string
    {
        $name = html_entity_decode($name);
        if($maxLength <= 0)
        {
            return $name;
        }
        return substr($name, 0, $maxLength);
    }
    /**
     * Get preview text.
     *
     * @param  Record        $item
     * @param  KeyValue      $settings
     * @param  int           $defaultPreviewTextLength
     * @return string
     */
    public function getPreviewText(Record $item, KeyValue $settings, int $defaultPreviewTextLength = 0):string
    {
        switch($settings->get('previewTextType'))
        {
            case 'itemShortDescription':
                $previewText = $item->itemDescription->shortDescription;
                break;
            case 'technicalData':
                $previewText = $item->itemDescription->technicalData;
                break;
            case 'itemDescriptionAndTechnicalData':
                $previewText = $item->itemDescription->description . ' ' . $item->itemDescription->technicalData;
                break;
            case 'itemDescription':
                $previewText = $item->itemDescription->description;
                break;
            case 'dontTransfer':
            default:
                $previewText = '';
                break;
        }
        $previewTextLength = $settings->get('previewTextMaxLength') ? $settings->get('previewTextMaxLength') : $defaultPreviewTextLength;
        if($settings->get('previewTextRemoveHtmlTags') == self::REMOVE_HTML_TAGS)
        {
            $previewText = strip_tags($previewText, str_replace([',', ' '], '', $settings->get('previewTextAllowHtmlTags')));
        }
        if($previewTextLength <= 0)
        {
            return $previewText;
        }
        return substr($previewText, 0, $previewTextLength);
    }
    /**
     * Get description.
     *
     * @param  Record        $item
     * @param  KeyValue      $settings
     * @param  int           $defaultDescriptionLength
     * @return string
     */
    public function getDescription(Record $item, KeyValue $settings, int $defaultDescriptionLength = 0):string
    {
        switch($settings->get('descriptionType'))
        {
            case 'itemShortDescription':
                $description = $item->itemDescription->shortDescription;
                break;
            case 'technicalData':
                $description = $item->itemDescription->technicalData;
                break;
            case 'itemDescriptionAndTechnicalData':
                $description = $item->itemDescription->description . ' ' . $item->itemDescription->technicalData;
                break;
            case 'itemDescription':
            default:
                $description = $item->itemDescription->description;
                break;
        }
        $descriptionLength = $settings->get('descriptionMaxLength') ? $settings->get('descriptionMaxLength') : $defaultDescriptionLength;
        if($settings->get('descriptionRemoveHtmlTags') == self::REMOVE_HTML_TAGS)
        {
            $description = strip_tags($description, str_replace([',', ' '], '', $settings->get('previewTextAllowHtmlTags')));
        }
        $description = html_entity_decode($description);
        if($descriptionLength <= 0)
        {
            return $description;
        }
        return substr($description, 0, $descriptionLength);
    }
    /**
	 * Get variation availability days.
	 * @param  Record   $item
	 * @param  KeyValue $settings
	 * @param  bool 	$returnAvailabilityName = true
	 * @return string
	 */
	public function getAvailability(Record $item, KeyValue $settings, bool $returnAvailabilityName = true):string
	{
        if($settings->get('transferItemAvailability') == self::TRANSFER_ITEM_AVAILABILITY_YES)
		{
            $availabilityIdString = 'itemAvailability' . $item->variationBase->availability;
		    return (string)$settings->get($availabilityIdString);
		}
		return $this->marketItemHelperRepository->getAvailability($item->variationBase->availability, $settings->get('lang'), $returnAvailabilityName);
	}
    /**
     * Get the item URL.
     * @param  Record $item
     * @param  KeyValue $settings
     * @param  bool $addReferrer = true  Choose if referrer id should be added as parameter.
     * @param  bool $useIntReferrer = false Choose if referrer id should be used as integer.
     * @param  bool $useHttps = true Choose if https protocol should be used.
     * @return string Item url.
     */
    public function getUrl(Record $item, KeyValue $settings, bool $addReferrer = true, bool $useIntReferrer = false, bool $useHttps = true):string
	{
        if($settings->get('itemUrl') == self::ITEM_URL_NO)
        {
            return '';
        }
		$urlParams = [];
        $link = $this->urlBuilderRepository->getItemUrl($item->itemBase->id, $settings->get('plentyId'), $item->itemDescription->urlContent, $settings->get('lang') ? $settings->get('lang') : 'de');
		if($addReferrer && $settings->get('referrerId'))
		{
            $urlParams[] = 'ReferrerID=' . ($useIntReferrer ? (int) $settings->get('referrerId') : $settings->get('referrerId'));
		}
		if(strlen($settings->get('urlParam')))
		{
			$urlParams[] = $settings->get('urlParam');
		}
		if (is_array($urlParams) && count($urlParams) > 0)
		{
			$link .= '?' . implode('&', $urlParams);
		}
		return $link;
	}
    /**
     * Get category branch for a custom category id.
     * @param  int $categoryId
     * @param  string $lang
     * @param  int $plentyId
     * @param  string $separator = ' > '
     * @return string
     */
    public function getCategory(int $categoryId, string $lang, int $plentyId, string $separator = ' > '):string
	{
		return $this->marketCategoryHelperRepository->getCategoryBranchName($categoryId, $lang, $plentyId, $separator);
	}
    public function getCategoryBranch(Record $item, KeyValue $settings, int $categoryLevel):string
    {
		if($item->variationStandardCategory->categoryId <= 0)
		{
			return '';
		}
        $categoryBranch = $this->categoryBranchRepository->find($item->variationStandardCategory->categoryId);
        $category = null;
        $lang = $settings->get('lang') ? $settings->get('lang') : 'de';
        if(!is_null($categoryBranch) && $categoryBranch instanceof CategoryBranch)
        {
            switch($categoryLevel)
            {
                case 1:
                    $category = $this->categoryRepository->get($categoryBranch->category1Id, $lang);
                    break;
                case 2:
                    $category = $this->categoryRepository->get($categoryBranch->category2Id, $lang);
                    break;
                case 3:
                    $category = $this->categoryRepository->get($categoryBranch->category3Id, $lang);
                    break;
                case 4:
                    $category = $this->categoryRepository->get($categoryBranch->category4Id, $lang);
                    break;
                case 5:
                    $category = $this->categoryRepository->get($categoryBranch->category5Id, $lang);
                    break;
                case 6:
                    $category = $this->categoryRepository->get($categoryBranch->category6Id, $lang);
                    break;
            }
        }
        if($category instanceof Category)
        {
            foreach($category->details as $categoryDetails)
            {
                if($categoryDetails->lang == $lang)
                {
                    return (string)$categoryDetails->name;
                }
            }
        }
        return '';
    }
	/**
	 * Get category branch marketplace for a custom branch id.
	 * @param  int $categoryId
	 * @param  int $plentyId
	 * @param  int $marketplaceId
	 * @param  float $marketplaceSubId
	 * @return string
	 */
	public function getCategoryMarketplace(int $categoryId, int $plentyId, int $marketplaceId, float $marketplaceSubId = 0.0):string
	{
		if($categoryId > 0)
		{
			$webstoreId = $this->getWebstoreId($plentyId);
			$categoryBranchMarketplace = $this->categoryBranchMarketplaceRepository->findCategoryBranchMarketplace($categoryId, $webstoreId, $marketplaceId, $marketplaceSubId);
			if($categoryBranchMarketplace instanceof CategoryBranchMarketplace)
			{
				return (string)$categoryBranchMarketplace->plenty_category_branch_marketplace_value1;
			}
		}
		return '';
	}
    /**
     * Get shipping cost.
     * @param  Record   $item
     * @param  KeyValue $settings
     * @param  int|null  $mobId
     * @return float|null
     */
    public function getShippingCost(Record $item, KeyValue $settings, int $mopId = null)
    {
        if($settings->get('shippingCostType') == self::SHIPPING_COST_TYPE_FLAT)
        {
            return (float) $settings->get('shippingCostFlat');
        }
        if($settings->get('shippingCostType') == self::SHIPPING_COST_TYPE_CONFIGURATION)
        {
            $defaultShipping = $this->getDefaultShipping($settings);
            if( $defaultShipping instanceof DefaultShipping &&
                $defaultShipping->shippingDestinationId)
            {
                if(!is_null($mopId) && $mopId == $defaultShipping->paymentMethod2)
                {
                    $paymentMethodId = $defaultShipping->paymentMethod2;
                    return $this->calculateShippingCost($item->itemBase->id, $defaultShipping->shippingDestinationId, $defaultShipping->referrerId, $paymentMethodId);
                }
                if(!is_null($mopId) && $mopId == $defaultShipping->paymentMethod3)
                {
                    $paymentMethodId = $defaultShipping->paymentMethod3;
                    return $this->calculateShippingCost($item->itemBase->id, $defaultShipping->shippingDestinationId, $defaultShipping->referrerId, $paymentMethodId);
                }
                $paymentMethodId = $defaultShipping->paymentMethod2;
                // 0 - is always "payment in advance" so we use always the second and third payment methods from the default shipping
                if($settings->get('shippingCostMethodOfPayment') == 2)
                {
                    $paymentMethodId = $defaultShipping->paymentMethod3;
                }
                if(!is_null($mopId) && $mopId >= 0)
                {
                    if($mopId == $paymentMethodId)
                    {
                        return $this->calculateShippingCost($item->itemBase->id, $defaultShipping->shippingDestinationId, $defaultShipping->referrerId, $paymentMethodId);
                    }
                }
                elseif(is_null($mopId))
                {
                    return $this->calculateShippingCost($item->itemBase->id, $defaultShipping->shippingDestinationId, $defaultShipping->referrerId, $paymentMethodId);
                }
            }
        }
        return null;
    }
    /**
     * Calculate default shipping cost.
     * @param int $itemId
     * @param int $shippingDestinationId
     * @param float $referrerId
     * @param int $paymentMethodId
     * @return float|null
     */
    public function calculateShippingCost(int $itemId, int $shippingDestinationId, float $referrerId, int $paymentMethodId)
    {
        return $this->defaultShippingCostRepository->findShippingCost($itemId, $referrerId, $shippingDestinationId, $paymentMethodId);
    }
    /**
     * returns the price of the given variation
     * @param  Record   $item
     * @return float|null
     */
    public function getPrice(Record $item)
    {
            return $item->variationRetailPrice->price;
    }
    
    public function getAllPrices(Record $item)
    {
            return $item->variationRetailPriceList;
    }
    
    
    /**
     * returns the recommendedRetailPrice of the given variation if transferRrp is set
     * @param Record $item
     * @param KeyValue $settings
     * @return float
     */
    public function getRecommendedRetailPrice(Record $item, KeyValue $settings):float
    {
        if($settings->get('transferRrp') == self::TRANSFER_RRP_YES)
        {
            return (float)$item->variationRecommendedRetailPrice->price;
        }
        return 0.00;
    }
    /**
     * returns the specialOfferPrice of the given variation if transferOfferPrice is set
     * @param Record $item
     * @param KeyValue $settings
     * @return float
     */
    public function getSpecialPrice(Record $item, KeyValue $settings):float
    {
        if($settings->get('transferOfferPrice') == self::TRANSFER_OFFER_PRICE_YES)
        {
            return (float)$item->variationSpecialOfferRetailPrice->retailPrice;
        }
        return 0.00;
    }
    /**
     * Get the attributeNames
     * @param Record    $item
     * @param KeyValue  $settings
     * @param string    $delimiter
     * @return string
     */
    public function getAttributeName(Record $item, KeyValue $settings, string $delimiter = '|'):string
    {
        $values = [];
        if(!is_null($item->variationBase->attributeValueSetId))
        {
            foreach($item->variationAttributeValueList as $attribute)
            {
                $attributeName = $this->marketAttributeHelperRepository->getAttributeName($attribute->attributeId, $settings->get('lang') ? $settings->get('lang') : 'de');
                if(strlen($attributeName) > 0)
                {
                    $values[] = $attributeName;
                }
            }
        }
        $valuedata = implode($delimiter, $values);
	return str_replace("|"," ",$valuedata);
    }
    /**
     * Get the attribute value set short frontend name. Ex. blue, XL
     * @param  Record   $item
     * @param  KeyValue $settings
     * @param  string $delimiter
     * @param  array $attributeNameCombination
     * @return string
     */
    public function getAttributeValueSetShortFrontendName(Record $item, KeyValue $settings, string $delimiter = ', ', array $attributeNameCombination = null):string
    {
        $values = [];
        $unsortedValues = [];
        if($item->variationBase->attributeValueSetId)
        {
            $i = 0;
            foreach($item->variationAttributeValueList as $attribute)
            {
                $attributeValueName = $this->marketAttributeHelperRepository->getAttributeValueName($attribute->attributeId, $attribute->attributeValueId, $settings->get('lang') ? $settings->get('lang') : 'de');
                if(strlen($attributeValueName) > 0)
                {
                    $unsortedValues[$attribute->attributeId] = $attributeValueName;
                    $i++;
                }
            }
            /**
             * sort the attribute value names depending on the order of the $attributeNameCombination
             */
            if(is_array($attributeNameCombination) && count($attributeNameCombination) > 0)
            {
                $j = 0;
                while($i > 0)
                {
                    $values[] = $unsortedValues[$attributeNameCombination[$j]];
                    $j++;
                    $i--;
                }
            }
            else
            {
                $values = $unsortedValues;
            }
        }
        $valuedata = implode($delimiter, $values);
 	return str_replace("|"," ",$valuedata);
    }
    /**
     * getAttributeNameAndValueCombination
     * @param string $attributeNames
     * @param string $attributeValues
     * @param string $delimiter
     * @return string
     */
    public function getAttributeNameAndValueCombination(string $attributeNames, string $attributeValues, string $delimiter = ','):string
    {
        $attributes='';
        $attributeNameList = array();
        $attributeValueList = array();
        if (strlen($attributeNames) && strlen($attributeValues))
        {
            $attributeNameList = explode(',', $attributeNames);
            $attributeValueList = explode(',', $attributeValues);
        }
        if (count($attributeNameList) && count($attributeValueList))
        {
            foreach ($attributeNameList as $index => $attributeName)
            {
                if ($index==0)
                {
                    $attributes .= $attributeNameList[$index]. ': ' . $attributeValueList[$index];
                }
                else
                {
                    $attributes .= $delimiter. ' ' . $attributeNameList[$index]. ': ' . $attributeValueList[$index];
                }
            }
        }
        return $attributes;
    }
    /**
     * Get base price.
     * @param  Record   $item
     * @param  KeyValue $settings
     * @param  string   $separator	= '/'
     * @param  bool     $compact    = false
     * @param  bool     $dotPrice   = false
     * @param  string   $currency   = ''
     * @param  float    $price      = 0.0
     * @param  bool     $addUnit    = true
     * @return string
     */
    public function getBasePrice(
        Record $item,
        KeyValue $settings,
        string $separator = '/',
        bool $compact = false,
        bool $dotPrice = false,
        string $currency = '',
        float $price = 0.0,
        bool $addUnit = true
    ):string
	{
        $currency = strlen($currency) ? $currency : $this->getDefaultCurrency();
		$price = $price > 0 ? $price : (float) $item->variationRetailPrice->price;
        $lot = (int) $item->variationBase->content;
        $unitLang = $this->unitNameRepository->findByUnitId((int) $item->variationBase->unitId);
        if($unitLang instanceof UnitName)
        {
            $unitShortcut = $unitLang->unit->unitOfMeasurement;
            $unitName = $unitLang->name;
        }
        else
        {
            $unitShortcut = '';
            $unitName = '';
        }
		$basePriceDetails = $this->getBasePriceDetails($lot, $price, $unitShortcut);
		if((float) $basePriceDetails['price'] <= 0 || ((int) $basePriceDetails['lot'] <= 1 && $basePriceDetails['unit'] == 'C62'))
		{
			return '';
		}
		if ($dotPrice == true)
		{
			$basePriceDetails['price'] = number_format($basePriceDetails['price'], 2, '.', '');
		}
		else
		{
			$basePriceDetails['price'] = number_format($basePriceDetails['price'], 2, ',', '');
		}
		if ($addUnit == true)
        {
            if ($compact == true)
            {
                return	'(' . (string) $basePriceDetails['price'] . $currency . $separator . (string) $basePriceDetails['lot'] . $unitShortcut . ')';
            }
            else
            {
                return	(string) $basePriceDetails['price'] . ' ' . $currency . $separator . (string) $basePriceDetails['lot'] . ' ' . $unitName;
            }
        }
        else
        {
            return	(string) $basePriceDetails['price'];
        }
	}
	/**
	 * Get base price.
	 *
	 * @param  Record   $item
	 * @param  KeyValue $settings
	 * @return array
	 */
	public function getBasePriceList(Record $item, KeyValue $settings):array
	{
		$price = (float)$item->variationRetailPrice->price;
		$lot = (int)$item->variationBase->content;
		$unitLang = $this->unitNameRepository->findByUnitId((int)$item->variationBase->unitId);
		if($unitLang instanceof UnitName)
		{
            $unitShortcut = $unitLang->unit->unitOfMeasurement;
			$unitName = $unitLang->name;
		}
		else
		{
            $unitShortcut = '';
			$unitName = '';
		}
		$basePriceDetails = $this->getBasePriceDetails($lot, $price, $unitShortcut);
		$basePriceDetails['price'] = number_format($basePriceDetails['price'], 2, '.', '');
        return [
        	'lot' => (int)$basePriceDetails['lot'],
			'price' => (float)$basePriceDetails['price'],
			'unit' => (string)$unitName
		];
	}
    /**
     * Get main image.
     * @param  Record   $item
     * @param  KeyValue $settings
	 * @param  string 	$imageType
     * @return string
     */
    public function getMainImage(Record $item, KeyValue $settings, string $imageType = 'normal'):string
    {
        foreach($item->variationImageList as $image)
        {
            if($settings->get('imagePosition') == self::IMAGE_FIRST)
            {
                return (string)$this->urlBuilderRepository->getImageUrl($image->path, $settings->get('plentyId'), $imageType, $image->fileType, $image->type == 'external');
            }
            elseif($settings->get('imagePosition')== self::IMAGE_POSITION0 && $image->position == 0)
            {
                return (string)$this->urlBuilderRepository->getImageUrl($image->path, $settings->get('plentyId'), $imageType, $image->fileType, $image->type == 'external');
            }
        }
        return '';
    }
    /**
     * Get list of images for current item.
     * @param Record $item
     * @param KeyValue $settings
     * @param string $imageType = 'normal'
     * @return array
     */
    public function getImageList(Record $item, KeyValue $settings, string $imageType = 'normal'):array
    {
        
        $list = [];
        foreach($item->variationImageList as $image)
        {
            
            
            $linkedvariations = $this->variationImageRepositoryContract->findByImageId($image->imageId);
            foreach($linkedvariations as $linkedvariation){
                if($linkedvariation->variationId == $item->variationBase->id){
                    
                    $availabilitylist = $this->itemImageAvailabilityRepositoryContract->findByImageId($image->imageId);            
                    foreach($availabilitylist as $availability){
                        if($availability->type == "marketplace" && $availability->value == "10.00" ){
                            $list[] = $this->urlBuilderRepository->getImageUrl($image->path, $settings->get('plentyId'), $imageType, $image->fileType, $image->type == 'external');
                        } 
                        elseif($availability->type == "marketplace" && $availability->value == "-1.00" ){
                            $list[] = $this->urlBuilderRepository->getImageUrl($image->path, $settings->get('plentyId'), $imageType, $image->fileType, $image->type == 'external');
                        }                
                    }
                    
                    
                }
                    
                
                
            } 
            
            
            
                
            
            
        }
        
        return $list;
    }
	/**
	 * Get item characters that match referrer from settings and a given component id.
	 * @param  Record   $item
	 * @param  float    $marketId
	 * @param  string  $externalComponent
	 * @return string
	 */
	public function getItemPropertyByExternalComponent(Record $item, float $marketId, $externalComponent):string
	{
		$marketProperties = $this->marketPropertyHelperRepository->getMarketProperty($marketId);
		foreach($item->itemPropertyList as $property)
		{
			foreach($marketProperties as $marketProperty)
			{
				if(is_array($marketProperty) && count($marketProperty) > 0 && $marketProperty['character_item_id'] == $property->propertyId)
				{
					if (strlen($externalComponent) > 0 && $marketProperty['external_component'] == $externalComponent)
					{
						return $property->propertyValue;
					}
				}
			}
		}
		return '';
	}
    /**
     * Get item character value by backend name.
     * @param  Record $item
     * @param KeyValue $settings
     * @param  string $backendName
     * @return string
     */
    public function getItemCharacterByBackendName(Record $item, KeyValue $settings, string $backendName):string
    {
        foreach($item->itemPropertyList as $property)
        {
            $propertyName = $this->propertyNameRepository->findOne($property->propertyId, $settings->get('lang')? $settings->get('lang') : 'de');
            if($propertyName instanceof PropertyName &&
				$propertyName->name == $backendName)
            {
                return (string) $property->propertyValue;
            }
        }
        return '';
    }
    
    
    public function getSize(Record $item, KeyValue $settings)
    {
        
        
        foreach($item->itemPropertyList as $property)
        {
            $prop = $this->propertyRepositoryContract->findById($property->propertyId);
            $propgroup =  $prop->propertyGroupId;
            $backendname = $prop->backendName;
            
            if($propgroup == 4){
                return $backendname . "cm";
            } 
            
        }
        
        return "";
        
        
    }
    
    
    
    public function getGender(Record $item, KeyValue $settings)
    {
       
        foreach($item->itemPropertyList as $property)
        {
            $prop = $this->propertyRepositoryContract->findById($property->propertyId);
            $propgroup =  $prop->propertyGroupId;
            $backendname = $prop->backendName;
            
            if($propgroup == 2){                
                return $backendname;
            } 
        }
        
        return "Unisex";
        
    }
    
    
    public function getMaterial(Record $item)
    {
       
        foreach($item->itemPropertyList as $property)
        {
            $prop = $this->propertyRepositoryContract->findById($property->propertyId);
            $propgroup =  $prop->propertyGroupId;
            $backendname = $prop->backendName;
            
            if($propgroup == 5){                
                return $backendname;
            } 
        }
        
        return "Material";
        
    }
    
    
    
    
    public function getColor(Record $item, KeyValue $settings)
    {
        
        
        foreach($item->itemPropertyList as $property)
        {
            //$props = $props . $this->propertyNameRepository->findOne($property->propertyId, $settings->get('lang')? $settings->get('lang') : 'de') . " / ";
            $prop = $this->propertyRepositoryContract->findById($property->propertyId);
            $propgroup =  $prop->propertyGroupId;
            $backendname = $prop->backendName;
            
            if($propgroup == 3){
                return $backendname;
            } 
            
        }
        
        return "";
        
        
    }
    
    
    public function getVariationNumber(Record $item ){        
        
        $variation = $this->variationRepositoryContract->findById($item->variationBase->id);       
        return $variation->number;
        
    }
    
    
    
    
	/**
	 * Get item characters that match referrer from settings and a given component id.
	 * @param  Record   $item
	 * @param  float    $marketId
	 * @param  int     $componentId  = null
	 * @return array
	 */
	public function getItemCharactersByComponent(Record $item, float $marketId, int $componentId = null):array
	{
		$marketProperties = $this->marketPropertyHelperRepository->getMarketProperty($marketId);
		$list = array();
		foreach($item->itemPropertyList as $property)
		{
			foreach($marketProperties as $marketProperty)
			{
				if(is_array($marketProperty) && count($marketProperty) > 0 && $marketProperty['character_item_id'] == $property->propertyId)
				{
					if (!is_null($componentId) && $marketProperty['component_id'] != $componentId)
					{
						continue;
					}
					$list[] = [
						'itemCharacterId' 	 => $property->itemPropertyId,
						'characterId' 		 => $property->propertyId,
						'characterValue' 	 => $property->propertyValue,
						'characterValueType' => $property->propertyValueType,
						'characterItemId' 	 => $marketProperty['character_item_id'],
						'componentId' 		 => $marketProperty['component_id'],
						'referrerId' 		 => $marketId,
						'externalComponent'  => $marketProperty['external_component'],
					];
				}
			}
		}
		return $list;
	}
    /**
     * Get barcode by a given type.
     * @param  Record   $item
     * @param  string   $barcodeType
     * @return string
     */
    public function getBarcodeByType(Record $item, string $barcodeType):string
    {
        foreach($item->variationBarcodeList as $variationBarcode)
        {
            if($variationBarcode->barcodeType == $barcodeType || $barcodeType == 'FirstBarcode')
            {
                return (string) $variationBarcode->code;
            }
        }
        return '';
    }
    /**
     * Get base price details.
     * @param  int    $lot
     * @param  float  $price
     * @param  string $unit
     * @return array
     */
    public function getBasePriceDetails(int $lot, float $price, string $unit):array
    {
        $lot = $lot == 0 ? 1 : $lot; // TODO  PlentyStringUtils::numberFormatLot($lot, true);
		$basePrice = 0;
		$basePriceLot = 1;
        $unit = strlen($unit) ? $unit : 'C62';
        $basePriceUnit = $unit;
		$factor = 1.0;
		if($unit == 'LTR' || $unit == 'KGM')
		{
			$basePriceLot = 1;
		}
		elseif($unit == 'GRM' || $unit == 'MLT')
		{
			if($lot <= 250)
			{
				$basePriceLot = 100;
			}
			else
			{
				$factor = 1000.0;
				$basePriceLot = 1;
				$basePriceUnit = $unit =='GRM' ? 'KGM' : 'LTR';
			}
		}
		else
		{
			$basePriceLot = 1;
		}
		$endLot = ($basePriceLot/$lot);
		return [
			'lot' => (int) $basePriceLot,
			'price' => (float) $price * $factor * $endLot,
			'unit' => (string) $basePriceUnit
		];
    }
    /**
     * getConvertContentTag
     * is used to check for too high values and to tag the necessity of converting
     * content and unit to one unit lower
     * @param float $content
     * @param int   $maxPreDecimals
     * @return bool
     */
    public function getConvertContentTag(float $content, int $maxPreDecimals):bool
    {
        if (strlen(number_format($content))>$maxPreDecimals)
        {
            return true;
        }
        return false;
    }
    /**
     * @param $content
     * @param $unit
     * @return float|int
     */
    public function getConvertedBasePriceContent (float $content, string $unit):float
    {
        if ($unit == 'ml' || $unit == 'g')
        {
            return $content*0.001;
        }
        return $content;
    }
    /**
     * @param string $unit
     * @return string
     */
    public function getConvertedBasePriceUnit (string $unit):string
    {
        if ($unit == 'ml')
        {
            return 'l';
        }
        if ($unit == 'g')
        {
            return 'kg';
        }
        return $unit;
    }
    /**
     * Get default currency from configuration.
     * @return string
     */
    public function getDefaultCurrency():string
    {
        $config = [];
		// TODO load config
//		$config = $this->getConfig('cfgCurrency');
        if(is_array($config) && is_string($config['cfgCurrency']))
        {
            return $config['cfgCurrency'];
        }
        return 'EUR';
    }
    /**
     * Get list of payment methods.
     * @param KeyValue $settings
     * @return array
     */
    public function getPaymentMethods(KeyValue $settings):array
    {
        $paymentMethods = $this->paymentMethodRepository->getPaymentMethods($settings->get('destination'), $settings->get('plentyId'), $settings->get('lang'));
        $list = array();
        foreach($paymentMethods as $paymentMethod)
        {
            $list[$paymentMethod->id] = $paymentMethod;
        }
        return $list;
    }
    /**
	 * Get the default shipping.
	 * @param  KeyValue $settings
	 * @return DefaultShipping|null
	 */
	public function getDefaultShipping(KeyValue $settings)
	{
        $defaultShippingProfiles = $this->getConfig('plenty.order.shipping.default_shipping');
        foreach($defaultShippingProfiles as $defaultShippingProfile)
        {
            if($defaultShippingProfile instanceof DefaultShipping && $defaultShippingProfile->id == $settings->get('shippingCostConfiguration'))
            {
                return $defaultShippingProfile;
            }
        }
        return null;
	}
    /**
     * Get the default shipping list.
     * @return array|null
     */
    public function getDefaultShippingList()
    {
        $defaultShippingProfiles = $this->getConfig('plenty.order.shipping.default_shipping');
        $list = [];
        foreach($defaultShippingProfiles as $defaultShippingProfile)
        {
            if($defaultShippingProfile instanceof DefaultShipping)
            {
                $list[$defaultShippingProfile->id] = $defaultShippingProfile;
            }
        }
        if(is_array($list) && count($list) > 0)
        {
            return $list;
        }
        return null;
    }
    /**
     * Get custom configuration.
     * @param  string $key
     * @param  mixed  $default = null
     * @return array
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->configRepository->get($key, $default);
    }
    /**
     * @param KeyValue $settings
     * @param string $isoCodeType
     * @return string
     */
    public function getCountry(KeyValue $settings, string $isoCodeType):string
    {
        $country = $this->countryRepository->findIsoCode($settings->get('destination'), $isoCodeType);
        return $country;
    }
    /**
     * @param int $plentyId
     * @return int
     */
    public function getWebstoreId(int $plentyId):int
    {
        $webstore = $this->webstoreRepository->findByPlentyId($plentyId);
        if($webstore instanceof Webstore)
        {
            $webstoreId = $webstore->id;
			if(!is_null($webstoreId))
			{
				return $webstoreId;
			}
        }
        return 0;
    }
    /**
     * @param Record 	  $item
     * @param float 	  $marketId
     * @param null|string $sku
     * @param int 		  $accountId
     * @param bool 		  $setLastExportedTimestamp
     * @return string
     */
    public function generateSku(Record $item,
								float $marketId,
								string $sku = null,
								int $accountId = 0,
								bool $setLastExportedTimestamp = true
	):string
    {
		return $this->marketItemHelperRepository->generateSku(
			$item->variationBase->id,
			$marketId,
			$accountId,
			$setLastExportedTimestamp
		);
    }
	/**
	 * Selects the external manufacturer name by id.
	 *
	 * @param int $manufacturerId
	 * @return string
	 */
    public function getExternalManufacturerName(int $manufacturerId):string
	{
		if($manufacturerId > 0)
		{
			return $this->marketItemHelperRepository->getExternalManufacturerName($manufacturerId);
		}
		return '';
	}
}