<?php
namespace inklabs\kommerce\EntityDTO;

class OrderItemDTO
{
    use IdDTOTrait, TimeDTOTrait;

    /** @var int */
    public $quantity;

    /** @var string */
    public $sku;

    /** @var string */
    public $name;

    /** @var string */
    public $discountNames;

    /** @var int (in ounces) */
    public $shippingWeight;

    /** @var boolean */
    public $areAttachmentsEnabled;

    /** @var PriceDTO */
    public $price;

    /** @var ProductDTO */
    public $product;

    /** @var OrderDTO */
    public $order;

    /** @var OrderItemOptionProductDTO[] */
    public $orderItemOptionProducts = [];

    /** @var OrderItemOptionValueDTO[] */
    public $orderItemOptionValues = [];

    /** @var OrderItemTextOptionValueDTO[] */
    public $orderItemTextOptionValues = [];

    /** @var CatalogPromotionDTO[] */
    public $catalogPromotions = [];

    /** @var ProductQuantityDiscountDTO[] */
    public $productQuantityDiscounts = [];

    /** @var AttachmentDTO[] */
    public $attachments = [];
}
