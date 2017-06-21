<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Plugin\Block;

use Konstanchuk\InfinityScroll\Helper\Data as Helper;
use Magento\Catalog\Block\Product\ListProduct;


class ProductList
{
    /** @var Helper */
    protected $_helper;

    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
    }

    public function aroundGetAdditionalHtml(ListProduct $subject, \Closure $proceed)
    {
        if ($this->_helper->isEnabled() && $this->_helper->isLoadingProducts()) {
            return '';
        }
        return $proceed();
    }

    public function aroundGetToolbarHtml(ListProduct $subject, \Closure $proceed)
    {
        if ($this->_helper->isEnabled() && $this->_helper->isLoadingProducts()) {
            return '';
        }
        return $proceed();
    }
}