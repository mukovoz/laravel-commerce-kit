<?php

namespace Lantera\ExtensionFramework\Enums;

enum Platform: string
{
    case Shopify = 'shopify';
    case BigCommerce = 'bigcommerce';
    case Custom = 'custom';

    public function label(): string
    {
        return match($this) {
            self::Shopify => 'Shopify',
            self::BigCommerce => 'BigCommerce',
            self::Custom => 'Custom',
        };
    }
}
