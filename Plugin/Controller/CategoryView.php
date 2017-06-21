<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Plugin\Controller;

use Magento\Catalog\Controller\Category\View;
use Konstanchuk\InfinityScroll\Helper\Data as Helper;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;


class CategoryView
{
    /** @var Helper */
    protected $_helper;

    /** @var \Magento\Framework\App\Response\Http */
    protected $_response;

    /** @var ResultRawFactory */
    protected $_resultRawFactory;

    public function __construct(Helper $helper,
                                ResponseInterface $response,
                                ResultRawFactory $resultRawFactory)
    {
        $this->_helper = $helper;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_response = $response;
    }

    public function afterExecute(View $subject, $result)
    {
        if ($this->_helper->isEnabled()
            && $this->_helper->isLoadingProducts()
            && $result instanceof ResultPage
            && $this->_response->getStatusCode() == 200
        ) {
            //sleep(10);
            $productList = $result->getLayout()->getBlock('category.products.list');
            if ($productList) {
                $resultRaw = $this->_resultRawFactory->create();
                $resultRaw->setContents($productList->toHtml());
                return $resultRaw;
            }
        }
        return $result;
    }
}