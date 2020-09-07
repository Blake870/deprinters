<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\RichSnippets\Block;

class Organization extends LdJson
{
    /**
     * Get array of values for organization ld+json
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $organization = $this->getStoreConfig('richsnippets/organization');
        $social = $this->getStoreConfig('richsnippets/social');
        if (is_array($organization)) {
            $values += $organization;
        }

        if (is_array($social)) {
            $values += $social;
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getLdJson()
    {
        if (!$configValues = $this->getValues()) {
            return '';
        }

        // prepare general organization info
        $keysMap = [
            'url' => 'url',
            'name' => 'name',
            'phone' => 'telephone',
            'email' => 'email'
        ];
        $ldArray = $this->remapArray($keysMap, $configValues);

        // prepare address
        $addressKeysMap = [
            'street' => 'streetAddress',
            'locality' => 'addressLocality',
            'region' => 'addressRegion',
            'postal_code' => 'postalCode',
            'country' => 'addressCountry',
        ];
        $address = $this->remapArray($addressKeysMap, $configValues);

        if (!empty($address)) {
            $ldArray['address'] = ['@type' => 'PostalAddress'] + $address;
        }

        // prepare social links
        $social = [];
        $socialsMap = [
            'twitter' => 'https://twitter.com/',
            'facebook' => 'https://www.facebook.com/',
            'googleplus' => 'https://plus.google.com/',
            'linkedin' => 'https://www.linkedin.com/company/',
            'pinterest' => 'https://www.pinterest.com/',
            'instagram' => 'https://instagram.com/',
        ];
        foreach ($socialsMap as $configKey => $jsonKey) {
            if (isset($configValues[$configKey])) {
                $social[] = $jsonKey . $configValues[$configKey];
            }
        }

        if (!empty($social)) {
            $ldArray['sameAs'] = [$social];
        }

        // add content schema and type
        if (empty($ldArray)) {
            return '';
        }

        $ldArray = ['@context' => 'http://schema.org', '@type' => 'Organization']
            + $ldArray;

        return $this->prepareJsonString($ldArray);
    }
}
