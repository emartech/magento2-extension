<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Observers;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Emartech\Emarsys\Helper\Json;
use Emartech\Emarsys\Model\Event as EventModel;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Store\Model\StoreManagerInterface;

class CreateCustomEvent implements ObserverInterface
{
    const EVENT_TYPE_PREFIX = 'custom/';

    /**
     * @var string[]
     */
    private $requireAttributes = [
        'event_data',
        'event_id',
    ];

    /**
     * @var Json
     */
    private $json;

    /**
     * @var EmarsysEventFactory
     */
    private $eventFactory;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CreateCustomEvent constructor.
     *
     * @param Json $json
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository $eventRepository
     */
    public function __construct(
        Json $json,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->json = $json;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     * @throws AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $this->validate($observer->getEvent());

        $data = $observer->getEvent()->getData('event_data');
        $eventId = $observer->getEvent()->getData('event_id');
        $storeId = $observer->getEvent()->getData('store_id');

        if (empty($storeId)) {
            $store = $this->storeManager->getStore();
        } else {
            $store = $this->storeManager->getStore($storeId);
        }

        /** Need to validate the data */
        if (is_string($data)) {
            $data = $this->json->unserialize($data);
        }

        if (empty($data['customer_email'])) {
            throw new LocalizedException(
                __('customer_email is required in event_data')
            );
        }

        $data = $this->json->serialize($data);
        $type = self::EVENT_TYPE_PREFIX . $eventId;

        $entityId = (string)time() . (string)strlen($data);
        $entityId = substr($entityId, -10);

        /** @var EventModel $eventModel */
        $eventModel = $this->eventFactory
            ->create()
            ->setEntityId($entityId)
            ->setWebsiteId($store->getWebsiteId())
            ->setStoreId($store->getId())
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);
    }

    /**
     * @param Event $event
     *
     * @return bool
     * @throws LocalizedException
     */
    private function validate($event)
    {
        $errors = [];

        foreach ($this->requireAttributes as $requireAttribute) {
            if (!($data = $event->getData($requireAttribute))) {
                $errors[] = $requireAttribute;
            }
        }

        if ($errors) {
            throw new LocalizedException(
                __("Need to specify data: %1", implode(', ', $errors))
            );
        }

        return true;
    }
}
