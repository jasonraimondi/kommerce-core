<?php
namespace inklabs\kommerce\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class ShipmentItem implements IdEntityInterface, ValidationInterface
{
    use IdTrait, TimeTrait;

    /** @var OrderItem */
    protected $orderItem;

    /** @var int */
    protected $quantityToShip;

    /** @var Shipment */
    protected $shipment;

    public function __construct(Shipment $shipment, OrderItem $orderItem, $quantityToShip)
    {
        $this->setId();
        $this->setCreated();
        $this->orderItem = $orderItem;
        $this->quantityToShip = (int) $quantityToShip;

        $shipment->addShipmentItem($this);
        $this->shipment = $shipment;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('quantityToShip', new Assert\NotNull);
        $metadata->addPropertyConstraint('quantityToShip', new Assert\Range([
            'min' => 0,
            'max' => 65535,
        ]));
    }

    public function getOrderItem()
    {
        return $this->orderItem;
    }

    public function getQuantityToShip()
    {
        return $this->quantityToShip;
    }
}
