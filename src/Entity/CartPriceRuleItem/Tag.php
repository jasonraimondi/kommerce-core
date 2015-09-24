<?php
namespace inklabs\kommerce\Entity\CartPriceRuleItem;

use inklabs\kommerce\View;
use inklabs\kommerce\Entity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Tag extends AbstractItem
{
    /** @var Entity\Tag */
    protected $tag;

    public function __construct(Entity\Tag $tag, $quantity)
    {
        $this->setCreated();
        $this->tag = $tag;
        $this->quantity = $quantity;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        parent::loadValidatorMetadata($metadata);
    }

    public function matches(Entity\CartItem $cartItem)
    {
        foreach ($cartItem->getProduct()->getTags() as $tag) {
            if (($tag->getId() === $this->tag->getId())
                and ($cartItem->getQuantity() >= $this->quantity)
            ) {
                return true;
            }
        }

        return false;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getView()
    {
        return new View\CartPriceRuleItem\Tag($this);
    }
}
