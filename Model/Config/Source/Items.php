<?php
/**
 * Copyright © 2018 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Wlae\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Items implements ArrayInterface
{
    /**
     * var int
     */
    const ONLY_NEWLY_ADDED = 1;

    /**
     * var int
     */
    const WHOLE_WISHLIST = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ONLY_NEWLY_ADDED,
                'label' => __('Send Only Newly Added')
            ],
            [
                'value' => self::WHOLE_WISHLIST,
                'label' => __('Send Entire Wish List')
            ]
        ];
    }
}
