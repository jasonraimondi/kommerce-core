<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\Action\Shipment\OrderItemQtyDTO;
use inklabs\kommerce\Entity\Order;
use inklabs\kommerce\Entity\Pagination;
use inklabs\kommerce\EntityRepository\EntityNotFoundException;

interface OrderServiceInterface
{
    public function update(Order & $order);

    /**
     * @param int $id
     * @return Order
     * @throws EntityNotFoundException
     */
    public function findOneById($id);

    public function getLatestOrders(Pagination & $pagination = null);

    /**
     * @param int $userId
     * @return Order[]
     */
    public function getOrdersByUserId($userId);

    /**
     * @param int $orderId
     * @param OrderItemQtyDTO $orderItemQtyDTO
     * @param string $comment
     * @param string $rateExternalId
     * @param string $shipmentExternalId
     */
    public function buyShipmentLabel(
        $orderId,
        OrderItemQtyDTO $orderItemQtyDTO,
        $comment,
        $rateExternalId,
        $shipmentExternalId
    );

    /**
     * @param int $orderId
     * @param OrderItemQtyDTO $orderItemQtyDTO
     * @param string $comment
     * @param int $carrier ShipmentTracker::$carrier
     * @param string $trackingCode
     * @return
     */
    public function addShipmentTrackingCode(
        $orderId,
        OrderItemQtyDTO $orderItemQtyDTO,
        $comment,
        $carrier,
        $trackingCode
    );

    /**
     * @param int $orderId
     * @param int $orderStatus
     * @return mixed
     */
    public function setOrderStatus($orderId, $orderStatus);
}
