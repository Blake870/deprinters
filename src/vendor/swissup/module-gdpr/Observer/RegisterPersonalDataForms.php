<?php

namespace Swissup\Gdpr\Observer;

class RegisterPersonalDataForms implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Prepare forms
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getCollection()
            ->addItem(
                new \Swissup\Gdpr\Model\PersonalDataForm([
                    'id' => 'magento:customer-registration',
                    'name' => 'Magento: Customer Registration',
                    'action' => 'customer_account_createpost',
                    'js_config' => [
                        'async' => '.actions-toolbar',
                    ]
                ])
            )
            ->addItem(
                new \Swissup\Gdpr\Model\PersonalDataForm([
                    'id' => 'magento:newsletter-subscription',
                    'name' => 'Magento: Newsletter Subscription',
                    'action' => 'newsletter_subscriber_new',
                    'js_config' => [
                        'destination' => '> .field:last',
                        'method' => 'after',
                    ]
                ])
            )
            ->addItem(
                new \Swissup\Gdpr\Model\PersonalDataForm([
                    'id' => 'magento:newsletter-subscription-management',
                    'name' => 'Magento: Newsletter Subscription Management',
                    'action' => 'newsletter_manage_save',
                    'sync_with' => 'is_subscribed',
                    'is_revokable' => true,
                    'js_config' => [
                        'checkbox' => false,
                        'destination' => '> fieldset:last > .field:last .label',
                    ]
                ])
            )
            ->addItem(
                new \Swissup\Gdpr\Model\PersonalDataForm([
                    'id' => 'magento:contact-us',
                    'name' => 'Magento: Contact Us',
                    'action' => 'contact_index_post'
                ])
            )
            ->addItem(
                new \Swissup\Gdpr\Model\PersonalDataForm([
                    'id' => 'magento:product-review',
                    'name' => 'Magento: Product Review',
                    'action' => 'review_product_post',
                    'client_identity_field' => 'nickname',
                ])
            );
    }
}
