<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Observer;

use Magento\Framework\Event\ObserverInterface;
use Konstanchuk\InfinityScroll\Helper\Data as Helper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Framework\Controller\Result\RawFactory as ResultRawFactory;


class InitLoading implements ObserverInterface
{
    /** @var Helper */
    protected $_helper;

    /** @var \Magento\Framework\App\Request\Http */
    protected $_request;

    /** @var \Magento\Framework\App\Response\Http */
    protected $_response;

    /** @var $_urlBuilder UrlInterface */
    protected $_urlBuilder;

    public function __construct(Helper $helper,
                                RequestInterface $request,
                                ResponseInterface $response,
                                UrlInterface $urlBuilder)
    {
        $this->_helper = $helper;
        $this->_request = $request;
        $this->_response = $response;
        $this->_urlBuilder = $urlBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $ajaxLoad = $this->_request->getParam('load_products', false);
        if ($this->_request->isXmlHttpRequest() && $ajaxLoad) {
            $this->_request->setParam('load_products', null);
            $this->_helper->setLoadingProductStatus(true);
        } else {
            $this->_helper->setLoadingProductStatus(false);
            $page = $this->_request->getParam(Toolbar::PAGE_PARM_NAME, null);
            if (!is_null($page) && $this->_request->isGet()) {
                $currentUrl = $this->_urlBuilder->getCurrentUrl();
                $newUrl = $this->_helper->removeParamsFromUrl($currentUrl, Toolbar::PAGE_PARM_NAME);
                $this->_response->setRedirect($newUrl)->send();
            }
        }
    }
}