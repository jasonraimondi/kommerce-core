<?php
namespace inklabs\kommerce\EntityDTO\Builder;

use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\Lib\PricingInterface;
use inklabs\kommerce\Lib\Slug;

class ProductDTOBuilder implements DTOBuilderInterface
{
    use IdDTOBuilderTrait, TimeDTOBuilderTrait;

    /** @var Product */
    protected $entity;

    /** @var ProductDTO */
    protected $entityDTO;

    /** @var DTOBuilderFactoryInterface */
    protected $dtoBuilderFactory;

    public function __construct(Product $product, DTOBuilderFactoryInterface $dtoBuilderFactory)
    {
        $this->entity = $product;
        $this->dtoBuilderFactory = $dtoBuilderFactory;

        $this->initializeProductDTO();
        $this->setId();
        $this->setTime();
        $this->entityDTO->slug                = Slug::get($this->entity->getName());
        $this->entityDTO->sku                 = $this->entity->getSku();
        $this->entityDTO->name                = $this->entity->getName();
        $this->entityDTO->unitPrice           = $this->entity->getUnitPrice();
        $this->entityDTO->quantity            = $this->entity->getQuantity();
        $this->entityDTO->isInventoryRequired = $this->entity->isInventoryRequired();
        $this->entityDTO->isPriceVisible      = $this->entity->isPriceVisible();
        $this->entityDTO->isVisible           = $this->entity->isVisible();
        $this->entityDTO->isActive            = $this->entity->isActive();
        $this->entityDTO->isTaxable           = $this->entity->isTaxable();
        $this->entityDTO->isShippable         = $this->entity->isShippable();
        $this->entityDTO->shippingWeight      = $this->entity->getShippingWeight();
        $this->entityDTO->description         = $this->entity->getDescription();
        $this->entityDTO->rating              = $this->entity->getRating();
        $this->entityDTO->defaultImage        = $this->entity->getDefaultImage();

        $this->entityDTO->isInStock = $this->entity->inStock();
    }

    public static function createFromDTO(ProductDTO $productDTO)
    {
        $product = new Product;
        self::setFromDTO($product, $productDTO);
        return $product;
    }

    public static function setFromDTO(Product & $product, ProductDTO $productDTO)
    {
        $product->setName($productDTO->name);
        $product->setUnitPrice($productDTO->unitPrice);
        $product->setQuantity($productDTO->quantity);
        $product->setIsInventoryRequired($productDTO->isInventoryRequired);
        $product->setIsPriceVisible($productDTO->isPriceVisible);
        $product->setIsActive($productDTO->isActive);
        $product->setIsVisible($productDTO->isVisible);
        $product->setIsTaxable($productDTO->isTaxable);
        $product->setIsShippable($productDTO->isShippable);
        $product->setShippingWeight($productDTO->shippingWeight);
        $product->setDescription($productDTO->description);
        $product->setRating($productDTO->rating);
        $product->setDefaultImage($productDTO->defaultImage);
    }

    protected function initializeProductDTO()
    {
        $this->entityDTO = new ProductDTO;
    }

    public function withTags()
    {
        foreach ($this->entity->getTags() as $tag) {
            $this->entityDTO->tags[] = $this->dtoBuilderFactory
                ->getTagDTOBuilder($tag)
                ->withImages()
                ->build();
        }

        return $this;
    }

    public function withTagsAndOptions(PricingInterface $pricing)
    {
        foreach ($this->entity->getTags() as $tag) {
            $this->entityDTO->tags[] = $this->dtoBuilderFactory
                ->getTagDTOBuilder($tag)
                ->withImages()
                ->withOptions($pricing)
                ->withTextOptions()
                ->build();
        }

        return $this;
    }

    public function withImages()
    {
        foreach ($this->entity->getImages() as $image) {
            $this->entityDTO->images[] = $this->dtoBuilderFactory
                ->getImageDTOBuilder($image)
                ->build();
        }

        foreach ($this->entity->getTags() as $tag) {
            foreach ($tag->getImages() as $image) {
                $this->entityDTO->tagImages[] = $this->dtoBuilderFactory
                    ->getImageDTOBuilder($image)
                    ->build();
            }
        }

        return $this;
    }

    public function withPrice(PricingInterface $pricing)
    {
        $this->entityDTO->price = $this->dtoBuilderFactory
            ->getPriceDTOBuilder($this->entity->getPrice($pricing))
            ->withAllData()
            ->build();

        return $this;
    }

    public function withProductQuantityDiscounts(Pricing $pricing)
    {
        $productQuantityDiscounts = $this->entity->getProductQuantityDiscounts();
        $pricing->setProductQuantityDiscounts($productQuantityDiscounts);

        foreach ($productQuantityDiscounts as $productQuantityDiscount) {
            $this->entityDTO->productQuantityDiscounts[] = $this->dtoBuilderFactory
                ->getProductQuantityDiscountDTOBuilder($productQuantityDiscount)
                ->withPrice($pricing)
                ->build();
        }

        return $this;
    }

    public function withOptionProducts()
    {
        foreach ($this->entity->getOptionProducts() as $optionProduct) {
            $this->entityDTO->optionProducts[] = $this->dtoBuilderFactory
                ->getOptionProductDTOBuilder($optionProduct)
                ->withOption()
                ->build();
        }

        return $this;
    }

    public function withProductAttributes()
    {
        foreach ($this->entity->getProductAttributes() as $productAttribute) {
            $this->entityDTO->productAttributes[] = $this->dtoBuilderFactory
                ->getProductAttributeDTOBuilder($productAttribute)
                ->withAttribute()
                ->withAttributeValue()
                ->build();
        }

        return $this;
    }

    public function withAllData(Pricing $pricing)
    {
        return $this
            ->withTagsAndOptions($pricing)
            ->withProductQuantityDiscounts($pricing)
            ->withPrice($pricing)
            ->withImages()
            ->withProductAttributes()
            ->withOptionProducts();
    }

    protected function preBuild()
    {
    }

    public function build()
    {
        $this->preBuild();
        unset($this->entity);
        return $this->entityDTO;
    }
}
