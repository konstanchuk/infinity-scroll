<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;


class LoadType implements ArrayInterface
{
    const ON_BUTTON = 1;
    const ON_SCROLL = 2;

    public function toOptionArray()
    {
        return [
            static::ON_BUTTON => __('on button'),
            static::ON_SCROLL => __('on scroll'),
        ];
    }
}
