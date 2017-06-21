<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Observer\RenderLayout;

use Magento\Framework\Event\ObserverInterface;
use Konstanchuk\InfinityScroll\Helper\Data as Helper;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;
use Magento\Framework\App\ViewInterface;


abstract class AbstractRenderLayout implements ObserverInterface
{
    /** @var Helper */
    protected $_helper;

    /** @var \Magento\Framework\App\Response\Http */
    protected $_response;

    /** @var ViewInterface */
    protected $_view;

    /** @var ResultRawFactory */
    protected $_resultRawFactory;

    public function __construct(Helper $helper,
                                ResponseInterface $response,
                                ViewInterface $view,
                                ResultRawFactory $resultRawFactory)
    {
        $this->_helper = $helper;
        $this->_response = $response;
        $this->_view = $view;
        $this->_resultRawFactory = $resultRawFactory;
    }

    protected function sendBlock($blockName)
    {
        if ($this->_helper->isEnabled()
            && $this->_helper->isLoadingProducts()
            && $this->_response->getStatusCode() == 200
        ) {
            $searchResultList = $this->_view->getLayout()->getBlock($blockName);
            if ($searchResultList) {
                $this->_response->setContent($searchResultList->toHtml())->send();
            }
        }
    }
}