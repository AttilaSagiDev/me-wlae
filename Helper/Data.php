<?php
/**
 * Copyright © 2018 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Wlae\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Store config is extension enabled
     */
    const XML_PATH_ENABLED = 'wlae/basic/active';

    /**
     * Store config is customer segmentation enabled
     */
    const XML_PATH_SEGMENTATION = 'wlae/basic/segmentation';

    /**
     * Store config enabled customer groups
     */
    const XML_PATH_GROUPS= 'wlae/basic/groups';

    /**
     * Wish list notification items config path
     */
    const XML_PATH_EMAIL_ITEMS = 'wlae/email/items';

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'wlae/email/recipient_email';

    /**
     * Recipient bcc email config path
     */
    const XML_PATH_BCC_RECIPIENT = 'wlae/email/bcc_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'wlae/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'wlae/email/email_template';

    /**
     * Check if enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if customer segmentation enabled
     *
     * @return bool
     */
    public function isSegmentationEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEGMENTATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get enabled customer groups
     *
     * @return array|bool
     */
    public function getEnabledCustomerGroups()
    {
        $customerGroups = $this->scopeConfig->getValue(
            self::XML_PATH_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        if (!empty($customerGroups)) {
            return explode(',', $customerGroups);
        }
        return false;
    }

    /**
     * Get email items configuration
     *
     * @return mixed
     */
    public function getEmailItemsConfiguration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ITEMS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email template identifier
     *
     * @return mixed
     */
    public function getEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email sender
     *
     * @return mixed
     */
    public function getEmailSender()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email recipient
     *
     * @return mixed
     */
    public function getEmailRecipient()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_RECIPIENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get bcc email recipient
     *
     * @return mixed
     */
    public function getBccRecipient()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BCC_RECIPIENT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
