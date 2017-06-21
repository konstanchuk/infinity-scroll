<?php

/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\InfinityScroll\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;


class Data extends AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'catalog/konstanchuk_infinity_scroll/active';
    const XML_PATH_LOADING_TYPE = 'catalog/konstanchuk_infinity_scroll/loading_type';

    protected $_loadingProductsStatus = false;

    public function setLoadingProductStatus($status)
    {
        $this->_loadingProductsStatus = (bool)$status;
    }

    public function isLoadingProducts()
    {
        return $this->_loadingProductsStatus;
    }

    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getLoadingType()
    {
        return (int)$this->scopeConfig->getValue(
            static::XML_PATH_LOADING_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function removeParamsFromUrl($url, $params)
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['query'])) {
            return $url;
        }
        $queryParams = array();
        parse_str($parsedUrl['query'], $queryParams);
        if (is_array($params)) {
            foreach ($params as $param) {
                if (isset($queryParams[$param])) {
                    unset($queryParams[$param]);
                }
            }
        } else {
            unset($queryParams[(string)$params]);
        }
        if (count($queryParams)) {
            $parsedUrl['query'] = http_build_query($queryParams);
        } else {
            unset($parsedUrl['query']);
        }
        return $this->unparseUrl($parsedUrl);
    }

    public function unparseUrl($parsedUrl)
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}