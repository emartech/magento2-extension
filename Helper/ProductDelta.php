<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ProductDeltaInterface;
use Emartech\Emarsys\Api\ProductDeltaRepositoryInterface;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ProductDelta extends AbstractHelper
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ProductDeltaRepositoryInterface
     */
    private $productDeltaRepository;

    /**
     * ProductDelta constructor.
     *
     * @param ProductRepositoryInterface      $productRepository
     * @param ConfigReader                    $configReader
     * @param ProductDeltaRepositoryInterface $productDeltaRepository
     * @param Context                         $context
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ConfigReader $configReader,
        ProductDeltaRepositoryInterface $productDeltaRepository,
        Context $context
    ) {
        parent::__construct($context);

        $this->productRepository = $productRepository;
        $this->configReader = $configReader;
        $this->productDeltaRepository = $productDeltaRepository;
    }

    /**
     * @param Product|int $product
     *
     * @return ProductDeltaInterface|bool
     */
    public function createDelta($product)
    {
        try {
            $canSave = false;

            if (!($product instanceof Product)) {
                $product = $this->productRepository->getById($product);
            }

            foreach ($product->getWebsiteIds() as $websiteId) {
                if ($this->configReader->isEnabledForWebsite(
                    ConfigInterface::PRODUCT_DELTA_SYNC,
                    $websiteId
                )) {
                    $canSave = true;
                    break;
                }
            }

            if ($canSave) {
                return $this->productDeltaRepository->create(
                    $product->getSku(),
                    $product->getEntityId(),
                    $product->getId()
                );
            }

        } catch (Exception $e) {
            return false;
        }
    }
}
