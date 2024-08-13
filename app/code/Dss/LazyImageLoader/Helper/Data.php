<?php

declare(strict_types=1);

/**
* Digit Software Solutions.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
*
* @category  Dss
* @package   Dss_LazyImageLoader
* @author    Extension Team
* @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
*/
namespace Dss\LazyImageLoader\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Serialize;

/**
 * Visitor Observer
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serializer
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected RequestInterface $request,
        protected Serialize $serializer,
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Is Enable
     *
     * @param bool $html
     * @return bool
     */
    public function isEnabled($html = false): bool
    {
        $active =  $this->scopeConfig
            ->getValue('lazyimageloader/general/active', ScopeInterface::SCOPE_STORE);
        if ($active != 1) {
            return false;
        }

        $full_action = $this->_request->getFullActionName();
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();
        if ($full_action == '__') {
            $conditionalJsPattern = '#(<\!--\s*LAZYIMAGE.*LAZYIMAGE\s*-->)#isU';
            preg_match_all($conditionalJsPattern, $html, $_matches);
            $actions = implode('', $_matches[0]);
            $actions = explode('|', $actions);
            if (count($actions) > 1) {
                $full_action = $actions[1];
                $actions = explode('_', $full_action);
                $module = $actions[0];
                $controller = $actions[1];
                $action = $actions[2];
            }
        }

        //check home page
        $active =  $this->scopeConfig
            ->getValue('lazyimageloader/general/home_page', ScopeInterface::SCOPE_STORE);
        if ($active == 1 && $full_action == 'cms_index_index') {
            return false;
        }
        //end

        //check controller
        if ($this->regexMatchSimple(
            $this->scopeConfig
                ->getValue('lazyimageloader/general/controller', ScopeInterface::SCOPE_STORE),
            "{$module}_{$controller}_{$action}",
            1
        )) {
            return false;
        }

        //check path
        if ($this->regexMatchSimple(
            $this->scopeConfig
                ->getValue('lazyimageloader/general/path', ScopeInterface::SCOPE_STORE),
            $this->_request->getRequestUri(),
            2
        )) {
            return false;
        }
        return true;
    }

    /**
     * Get Threshold
     *
     * @return int|string
     */
    public function getThreshold(): int|string
    {
        return $this->scopeConfig
            ->getValue('lazyimageloader/general/threshold', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Loading Width
     *
     * @return int|string
     */
    public function getLoadingWidth(): int|string
    {
        return $this->scopeConfig
            ->getValue('lazyimageloader/general/loading_width', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Lazy Laod
     *
     * @param mixed $html
     * @return mixed
     */
    public function lazyLoad($html)
    {
        $userAgent = $this->request->getHeader('User-Agent');
        if (isset($userAgent)) {
            $regex = '#<img class="product-image-photo([^>]*) src="([^"/]*/?[^".]*\.[^"]*)"(?!.*?notlazy)([^>]*)>#';
            // if (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
            if (preg_match('/MSIE/i', $userAgent)) {
                $replace = '<noscript><img$1 src="$2" $3></noscript>';
                $replace .= '<img class="product-image-photo lazy$1
	             src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="$2"$3>';
            } else {
                $replace = '<img class="product-image-photo lazy$1
	   src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" srcset="" data-src="$2"$3>';
            }
            $html = preg_replace($regex, $replace, $html);
        }
        return $html;
    }

    /**
     * Get Lazylaod Image
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLazyImage(): string
    {
        $img =  $this->scopeConfig
            ->getValue('lazyimageloader/general/loading', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$img || $img == '') {
            return $this->getLazyImg();
        }
        return $this->storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'lazyimage'.DIRECTORY_SEPARATOR.$img;
    }

    /**
     * Regex Match Simple
     *
     * @param mixed $regex
     * @param mixed $matchTerm
     * @param mixed $type
     * @return bool
     */
    public function regexMatchSimple($regex, $matchTerm, $type): bool
    {
        if (!$regex) {
            return false;
        }
        if (empty($regex)) {
            return false;
        }
        $rules = $this->serializer->unserialize($regex);

        if (empty($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            $regex = trim($rule['lazyimage'], '#');
            if ($type == 1) {
                $regexs = explode('_', $regex);
                switch ($this->countArray($regexs)) {
                    case 1:
                        $regex = $regex.'_index_index';
                        break;
                    case 2:
                        $regex = $regex.'_index';
                        break;
                    default:
                        break;
                }
            }

            $regexp = '#' . $regex . '#';
            if (preg_match($regexp, $matchTerm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Conut Array
     *
     * @param array $array
     * @return int
     */
    public function countArray($array): int
    {
        return count($array);
    }

    /**
     * Get Lazy Image
     *
     * @return string
     */
    protected function getLazyImg(): string
    {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
    
    /**
     * Get FullActionName
     *
     * @return string
     */
    public function getFullActionName(): string
    {
        return '<!-- LAZYIMAGE |'.$this->_request->getFullActionName().'| LAZYIMAGE -->';
    }
}
