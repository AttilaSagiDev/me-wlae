<?php
/**
 * Copyright © 2018 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Wlae\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\View\LayoutInterface;
use Me\Wlae\Helper\Data as ExtensionHelper;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;

class SendEmailObserver implements ObserverInterface
{
    /**
     * @var ExtensionHelper
     */
    private $helper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * SendEmailObserver constructor.
     *
     * @param LoggerInterface $logger
     * @param ExtensionHelper $helper
     * @param CustomerSession $customerSession
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param LayoutInterface $layout
     */
    public function __construct(
        LoggerInterface $logger,
        ExtensionHelper $helper,
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        LayoutInterface $layout
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->layout = $layout;
    }

    /**
     * Send email when product added to wish list
     *
     * @param Observer $observer
     * @return self|bool
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $item = $observer->getItem();
            $wishList = $observer->getWishlist();

            try {
                /** @var Customer $customer */
                $customer = $this->customerSession->getCustomer();
                if ($this->helper->isSegmentationEnabled()) {
                    $eGroups = $this->helper->getEnabledCustomerGroups();
                    if (is_array($eGroups) && !empty($eGroups)) {
                        if (!in_array($customer->getGroupId(), $eGroups)) {
                            return false;
                        }
                    }
                }

                $itemsBlock = $this->layout
                    ->createBlock('Me\Wlae\Block\Email\Items')
                    ->setWishlist($wishList)
                    ->setItem($item)
                    ->setShowConfig(
                        $this->helper->getEmailItemsConfiguration()
                    )
                    ->toHtml();

                if ($itemsBlock !== null) {
                    $customerName = $customer->getFirstname() .
                        ' ' . $customer->getLastname();
                    $data = [
                        'customerName' => $customerName,
                        'customerEmail' => $customer->getEmail(),
                        'item' => $item,
                        'wish_list' => $wishList,
                        'items' => $itemsBlock
                    ];

                    $bcc = [];
                    if ($this->helper->getBccRecipient()) {
                        $bcc[] = $this->helper->getBccRecipient();
                    }

                    $storeScope = ScopeInterface::SCOPE_STORE;
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier(
                            $this->helper->getEmailTemplate()
                        )
                        ->setTemplateOptions(
                            [
                                'area' => Area::AREA_FRONTEND,
                                'store' => $this->storeManager->getStore()
                                    ->getId(),
                            ]
                        )
                        ->setTemplateVars($data)
                        ->setFrom($this->helper->getEmailSender())
                        ->addTo($this->helper->getEmailRecipient())
                        ->addBcc($bcc)
                        ->getTransport();

                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                }
            } catch (\Exception $e) {
                $this->inlineTranslation->resume();
                $this->logger->error($e->getMessage());
            }
        }
        return $this;
    }
}
