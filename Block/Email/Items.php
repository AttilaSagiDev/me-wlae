<?php
/**
 * Copyright © 2018 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Wlae\Block\Email;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as ItemCollection;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Wishlist\Model\Item;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Image;
use Me\Wlae\Model\Config\Source\Items as ShowConfig;
use Magento\Framework\Pricing\Render as PriceRenderer;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Tax\Model\Config;

/**
 * @method Wishlist getWishlist()
 * @method Item getItem()
 * @method string getShowConfig()
 */
class Items extends Template
{
    /**
     * @var Wishlist
     */
    private $wishlist;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var ItemCollection
     */
    private $itemsCollection;

    /**
     * @var WishlistHelper
     */
    private $wishlistHelper;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * Tax configuration object
     *
     * @var Config
     */
    private $taxConfig;

    /**
     * Items constructor.
     *
     * @param Context $context
     * @param Wishlist $wishlist
     * @param Item $item
     * @param ItemCollection $itemCollection
     * @param WishlistHelper $wishlistHelper
     * @param ImageBuilder $imageBuilder
     * @param Config $taxConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Wishlist $wishlist,
        Item $item,
        ItemCollection $itemCollection,
        WishlistHelper $wishlistHelper,
        ImageBuilder $imageBuilder,
        Config $taxConfig,
        array $data = []
    ) {
        $this->wishlist = $wishlist;
        $this->item = $item;
        $this->itemsCollection = $itemCollection;
        $this->wishlistHelper = $wishlistHelper;
        $this->imageBuilder = $imageBuilder;
        $this->taxConfig = $taxConfig;
        parent::__construct($context, $data);
        $this->setTemplate('email/items.phtml');
    }

    /**
     * Retrieve wishlist loaded items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        $this->wishlist = $this->getWishlist();
        $this->item = $this->getItem();

        return $this->wishlist->getItemsCount();
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return ItemCollection
     */
    public function getWishlistItems()
    {
        if ($this->itemsCollection === null) {
            $this->itemsCollection = $this->wishlist->getItemCollection();
        }

        return $this->itemsCollection;
    }

    /**
     * Get currently added item
     *
     * @return Item
     */
    public function getCurrentItem()
    {
        return $this->item;
    }

    /**
     * Get show all items
     *
     * @return bool
     */
    public function getShowAllItems()
    {
        $showConfig = $this->getShowConfig();
        if (ShowConfig::WHOLE_WISHLIST == $showConfig) {
            return true;
        } else {
            if (ShowConfig::ONLY_NEWLY_ADDED == $showConfig) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  Item $item
     * @return string
     */
    public function getProductUrl($item)
    {
        return $this->wishlistHelper->getProductUrl($item);
    }

    /**
     * Return HTML block with tier price
     *
     * @param Product $product
     * @return string
     */
    public function getProductPriceHtml(Product $product)
    {
        /** @var PriceRenderer $priceRender */
        $priceRender = $this->_layout->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender =$this->_layout->createBlock(
                'Magento\Framework\Pricing\Render',
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price'  => true,
                    'use_link_for_as_low_as' => false,
                    'include_container' => false,
                    'zone' => PriceRenderer::ZONE_EMAIL
                ]
            );
        }

        return $price;
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Get price label
     *
     * @return string
     */
    public function getPriceLabel()
    {
        $priceLabel = '';

        $taxDisplay = $this->taxConfig->getPriceDisplayType();
        if ($taxDisplay == Config::DISPLAY_TYPE_EXCLUDING_TAX) {
            $priceLabel = __('Price (Excl. Tax)');
        } elseif ($taxDisplay == Config::DISPLAY_TYPE_INCLUDING_TAX) {
            $priceLabel = __('Price (Incl. Tax)');
        } elseif ($taxDisplay == Config::DISPLAY_TYPE_BOTH) {
            $priceLabel = __('Price (Both)');
        }

        return $priceLabel;
    }
}
