<?php
namespace inklabs\kommerce\Action\Shipment;

use inklabs\kommerce\EntityDTO\OrderItemQtyDTO;
use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\Uuid;
use inklabs\kommerce\Lib\UuidInterface;

final class BuyShipmentLabelCommand implements CommandInterface
{
    /** @var UuidInterface */
    private $orderId;

    /** @var OrderItemQtyDTO */
    private $orderItemQtyDTO;

    /** @var string */
    private $comment;

    /** @var string */
    private $shipmentExternalId;

    /** @var string */
    private $rateExternalId;

    /**
     * @param string $orderId
     * @param OrderItemQtyDTO $orderItemQtyDTO
     * @param string $comment
     * @param string $shipmentExternalId
     * @param string $rateExternalId
     */
    public function __construct(
        $orderId,
        OrderItemQtyDTO $orderItemQtyDTO,
        $comment,
        $shipmentExternalId,
        $rateExternalId
    ) {
        $this->orderId = Uuid::fromString($orderId);
        $this->orderItemQtyDTO = $orderItemQtyDTO;
        $this->comment = (string) $comment;
        $this->shipmentExternalId = (string) $shipmentExternalId;
        $this->rateExternalId = (string) $rateExternalId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getShipmentExternalId()
    {
        return $this->shipmentExternalId;
    }

    public function getRateExternalId()
    {
        return $this->rateExternalId;
    }

    public function getOrderItemQtyDTO()
    {
        return $this->orderItemQtyDTO;
    }

    public function getComment()
    {
        return $this->comment;
    }
}
