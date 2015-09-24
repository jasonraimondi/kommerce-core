<?php
namespace inklabs\kommerce\View\CartPriceRuleItem;

use inklabs\kommerce\Entity;
use inklabs\kommerce\View;

class Product extends AbstractItem
{
    /** @var View\Product */
    public $product;

    public function __construct(Entity\CartPriceRuleItem\AbstractItem $item)
    {
        parent::__construct($item);
    }

    public function withProduct()
    {
        $product = $this->item->getProduct();
        if (! empty($product)) {
            $this->product = $product->getView()
                ->export();
        }
        return $this;
    }

    public function withAllData()
    {
        return $this
            ->withProduct()
            ->export();
    }
}
