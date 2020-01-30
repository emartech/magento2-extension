<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\Data\ProductDeltaInterface;
use Emartech\Emarsys\Api\Data\ProductDeltaInterfaceFactory;
use Emartech\Emarsys\Api\ProductDeltaRepositoryInterface;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta as ProductDeltaResourceModel;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductDeltaRepository implements ProductDeltaRepositoryInterface
{
    /**
     * @var ProductDeltaInterfaceFactory
     */
    private $productDeltaFactory;

    /**
     * @var ProductDeltaResourceModel
     */
    private $productDeltaResourceModel;

    /**
     * ProductDeltaRepository constructor.
     *
     * @param ProductDeltaInterfaceFactory $productDeltaFactory
     * @param ProductDeltaResourceModel    $productDeltaResourceModel
     */
    public function __construct(
        ProductDeltaInterfaceFactory $productDeltaFactory,
        ProductDeltaResourceModel $productDeltaResourceModel
    ) {
        $this->productDeltaFactory = $productDeltaFactory;
        $this->productDeltaResourceModel = $productDeltaResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        /** @var ProductDeltaInterface $productDelta */
        $productDelta = $this->productDeltaFactory->create();
        $this->productDeltaResourceModel->load($productDelta, $id);
        if (!$productDelta->getId()) {
            throw new NoSuchEntityException(__('The ProductDelta with the "%1" ID doesn\'t exist.', $id));
        }
        return $productDelta;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ProductDeltaInterface $productDelta)
    {
        try {
            $this->productDeltaResourceModel->save($productDelta);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $productDelta;
    }

    /**
     * {@inheritdoc}
     */
    public function create($sku)
    {
        /** @var ProductDeltaInterface $productDelta */
        $productDelta = $this->productDeltaFactory->create();
        $productDelta->setSku($sku);

        return $this->save($productDelta);
    }

    /**
     * {@inheritdoc}
     */
    public function isSinceIdIsHigherThanAutoIncrement($sinceId)
    {
        return $this->productDeltaResourceModel
            ->isSinceIdIsHigherThanAutoIncrement($sinceId);
    }
}