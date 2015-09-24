<?php
namespace inklabs\kommerce\View;

use inklabs\kommerce\Entity;

class Coupon extends AbstractPromotion
{
    public $code;
    public $flagFreeShipping;
    public $minOrderValue;
    public $maxOrderValue;
    public $canCombineWithOtherCoupons;

    public function __construct(Entity\Coupon $coupon)
    {
        parent::__construct($coupon);

        $this->code             = $coupon->getCode();
        $this->flagFreeShipping = $coupon->getFlagFreeShipping();
        $this->minOrderValue    = $coupon->getMinOrderValue();
        $this->maxOrderValue    = $coupon->getMaxOrderValue();
        $this->canCombineWithOtherCoupons = $coupon->getCanCombineWithOtherCoupons();
    }
}
