<?php
/**
 * Copyright © 2018 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Wlae\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

class Groups implements ArrayInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->collectionFactory
                ->create()->setIgnoreIdFilter([0])->toOptionArray();
        }
        return $this->options;
    }
}
