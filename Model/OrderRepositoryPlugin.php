<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;

class OrderRepositoryPlugin
{

    /**
     * @param OrderRepository      $repository
     * @param OrderInterface|Order $entity
     * @return void
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function beforeSave(
        OrderRepository $repository,
        OrderInterface $entity
    ) {
        if ($entity->getEntityId() && !$entity->getOrigData(Order::STATE)) {
            /** @var Order $entity */
            $order = $repository->get($entity->getEntityId());
            $entity->setOrigData(Order::STATE, $order->getData(Order::STATE));
            $entity->setOrigData(Order::STATUS, $order->getData(Order::STATUS));
        }
    }
}
