<?php
namespace inklabs\kommerce\EntityDTO;

class AttributeDTO
{
    use IdDTOTrait, TimeDTOTrait;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var int */
    public $sortOrder;

    /** @var AttributeValueDTO[] */
    public $attributeValues = [];

    /** @var ProductAttributeDTO[] */
    public $productAttributes = [];
}
