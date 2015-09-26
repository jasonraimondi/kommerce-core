<?php
namespace inklabs\kommerce\tests\Helper;

use inklabs\kommerce\Entity\CatalogPromotion;
use inklabs\kommerce\Entity\Option;
use inklabs\kommerce\Entity\OptionProduct;
use inklabs\kommerce\Entity\OptionValue;
use inklabs\kommerce\Entity\OrderItem;
use inklabs\kommerce\Entity\OrderItemOptionProduct;
use inklabs\kommerce\Entity\OrderItemOptionValue;
use inklabs\kommerce\Entity\OrderItemTextOptionValue;
use inklabs\kommerce\Entity\Price;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\ProductQuantityDiscount;
use inklabs\kommerce\Entity\TextOption;
use inklabs\kommerce\Service\Kommerce;
use inklabs\kommerce\Entity;
use inklabs\kommerce\Lib;
use Doctrine;

abstract class DoctrineTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Kommerce */
    protected $kommerce;

    /** @var CountSQLLogger */
    protected $countSQLLogger;

    /** @var string[] */
    protected $metaDataClassNames;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if ($this->metaDataClassNames !== null) {
            $this->setupEntityManager();
        }
    }

    protected function setupEntityManager()
    {
        $this->getConnection();
        $this->setupTestSchema();
    }

    private function getConnection()
    {
        $this->kommerce = new Kommerce(new Doctrine\Common\Cache\ArrayCache());
        $this->kommerce->addSqliteFunctions();
        $this->kommerce->setup([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $this->entityManager = $this->kommerce->getEntityManager();
    }

    private function setupTestSchema()
    {
        $this->entityManager->clear();

        if (empty($this->metaDataClassNames)) {
            $classes = $this->entityManager->getMetaDataFactory()->getAllMetaData();
        } else {
            $classes = [];
            foreach ($this->metaDataClassNames as $className) {
                $classes[] = $this->entityManager->getMetaDataFactory()->getMetadataFor($className);
            }
        }

        $tool = new Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        // $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }

    public function setEchoLogger()
    {
        $this->kommerce->setSqlLogger(new Doctrine\DBAL\Logging\EchoSQLLogger);
    }

    public function setCountLogger()
    {
        $this->countSQLLogger = new CountSQLLogger;
        $this->kommerce->setSqlLogger($this->countSQLLogger);
    }

    protected function getDummyProduct($num = 1)
    {
        $product = new Entity\Product;
        $product->setSku($num);
        $product->setName('Test Product #' . $num);
        $product->setIsInventoryRequired(true);
        $product->setIsPriceVisible(true);
        $product->setIsActive(true);
        $product->setIsVisible(true);
        $product->setIsTaxable(true);
        $product->setIsShippable(true);
        $product->setShippingWeight(16);
        $product->setQuantity(10);
        $product->setUnitPrice(1200);

        return $product;
    }

    protected function getDummyTag($num = 1)
    {
        $tag = new Entity\Tag;
        $tag->setName('Test Tag #' . $num);
        $tag->setCode('TT' . $num);
        $tag->setDescription('Test Description');
        $tag->setDefaultImage('http://lorempixel.com/400/200/');
        $tag->setSortOrder(0);
        $tag->setIsActive(true);
        $tag->setIsVisible(true);

        return $tag;
    }

    protected function getDummyImage()
    {
        $image = new Entity\Image;
        $image->setPath('http://lorempixel.com/400/200/');
        $image->setWidth(400);
        $image->setHeight(200);
        $image->setSortOrder(0);

        return $image;
    }

    protected function getDummyOrderAddress()
    {
        $orderAddress = new Entity\OrderAddress;
        $orderAddress->firstName = 'John';
        $orderAddress->lastName = 'Doe';
        $orderAddress->company = 'Acme Co.';
        $orderAddress->address1 = '123 Any St';
        $orderAddress->address2 = 'Ste 3';
        $orderAddress->city = 'Santa Monica';
        $orderAddress->state = 'CA';
        $orderAddress->zip5 = '90401';
        $orderAddress->zip4 = '3274';
        $orderAddress->phone = '555-123-4567';
        $orderAddress->email = 'john@example.com';

        return $orderAddress;
    }

    protected function getDummyOrderItem(Entity\Product $product, Entity\Price $price)
    {
        $orderItem = new Entity\OrderItem;
        $orderItem->setProduct($product);
        $orderItem->setQuantity(1);
        $orderItem->setPrice($price);

        return $orderItem;
    }

    protected function getDummyOrderItemOptionProduct(Entity\OptionProduct $optionProduct)
    {
        $orderItemOptionProduct = new Entity\OrderItemOptionProduct;
        $orderItemOptionProduct->setOptionProduct($optionProduct);
        return $orderItemOptionProduct;
    }

    protected function getDummyOrderItemOptionValue(Entity\OptionValue $optionValue)
    {
        $orderItemOptionValue = new Entity\OrderItemOptionValue;
        $orderItemOptionValue->setOptionValue($optionValue);
        return $orderItemOptionValue;
    }

    /**
     * @param Entity\TextOption $textOption
     * @param string $textOptionValue
     * @return Entity\OrderItemTextOptionValue
     */
    protected function getDummyOrderItemTextOptionValue(Entity\TextOption $textOption, $textOptionValue)
    {
        $orderItemTextOptionValue = new Entity\OrderItemTextOptionValue;
        $orderItemTextOptionValue->setTextOption($textOption);
        $orderItemTextOptionValue->setTextOptionValue($textOptionValue);
        return $orderItemTextOptionValue;
    }

    /**
     * @param Entity\CartTotal $total
     * @param array $orderItems
     * @return Entity\Order
     */
    protected function getDummyOrder(Entity\CartTotal $total, array $orderItems = null)
    {
        $orderAddress = $this->getDummyOrderAddress();

        $order = new Entity\Order;
        $order->setTotal($total);
        $order->setShippingAddress($orderAddress);
        $order->setBillingAddress($orderAddress);

        if ($orderItems !== null) {
            foreach ($orderItems as $orderItem) {
                $order->addOrderItem($orderItem);
            }
        }

        return $order;
    }

    protected function getDummyCashPayment($amount = 100)
    {
        $payment = new Entity\CashPayment($amount);
        return $payment;
    }

    protected function getDummyPrice()
    {
        $price = new Entity\Price;
        $price->origUnitPrice = 100;
        $price->unitPrice = 100;
        $price->origQuantityPrice = 100;
        $price->quantityPrice = 100;

        return $price;
    }

    protected function getDummyCartTotal()
    {
        $cartTotal = new Entity\CartTotal;
        $cartTotal->origSubtotal = 1;
        $cartTotal->subtotal = 1;
        $cartTotal->taxSubtotal = 1;
        $cartTotal->discount = 1;
        $cartTotal->shipping = 1;
        $cartTotal->shippingDiscount = 1;
        $cartTotal->tax = 1;
        $cartTotal->total = 1;
        $cartTotal->savings = 1;

        return $cartTotal;
    }

    protected function getDummyCoupon($num = 1)
    {
        $coupon = new Entity\Coupon;
        $coupon->setName('20% OFF Test Coupon #' . $num);
        $coupon->setCode('20PCT' . $num);
        $coupon->setType(Entity\AbstractPromotion::TYPE_PERCENT);
        $coupon->setValue(20);

        return $coupon;
    }

    protected function getDummyCatalogPromotion($num = 1)
    {
        $catalogPromotion = new Entity\CatalogPromotion;
        $catalogPromotion->setName('Test Catalog Promotion #' . $num);
        $catalogPromotion->setCode('20PCTOFF');
        $catalogPromotion->setValue(20);

        return $catalogPromotion;
    }

    protected function getDummyUser($num = 1)
    {
        $user = new Entity\User;
        $user->setExternalId($num);
        $user->setStatus(Entity\User::STATUS_ACTIVE);
        $user->setEmail('test' . $num . '@example.com');
        $user->setPassword('xxxx');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        return $user;
    }

    protected function getDummyUserLogin()
    {
        $userLogin = new Entity\UserLogin;
        $userLogin->setEmail('john@example.com');
        $userLogin->setIp4('8.8.8.8');
        $userLogin->setResult(Entity\UserLogin::RESULT_SUCCESS);

        return $userLogin;
    }

    protected function getDummyUserRole()
    {
        $userRole = new Entity\UserRole;
        $userRole->setName('Administrator');
        $userRole->setDescription('Admin account. Access to everything');

        return $userRole;
    }

    protected function getDummyUserToken()
    {
        $userToken = new Entity\UserToken;
        $userToken->setUserAgent('SampleBot/1.1');
        $userToken->settoken('xxxx');
        $userToken->setexpires(new \DateTime);
        $userToken->setType(Entity\UserToken::TYPE_FACEBOOK);

        return $userToken;
    }

    protected function getDummyProductQuantityDiscount()
    {
        $productQuantityDiscount = new Entity\ProductQuantityDiscount;
        $productQuantityDiscount->setCustomerGroup(null);
        $productQuantityDiscount->setQuantity(6);
        $productQuantityDiscount->setFlagApplyCatalogPromotions(true);

        return $productQuantityDiscount;
    }

    protected function getDummyCartPriceRule()
    {
        $cartPriceRule = new Entity\CartPriceRule;
        $cartPriceRule->setName('Test Cart Price Rule');
        $cartPriceRule->setType(Entity\AbstractPromotion::TYPE_FIXED);
        $cartPriceRule->setValue(0);

        return $cartPriceRule;
    }

    protected function getDummyFullCartItem()
    {
        $product = new Entity\Product;
        $product->setSku('P1');
        $product->setUnitPrice(100);
        $product->setShippingWeight(10);

        $product2 = new Entity\Product;
        $product2->setSku('OP1');
        $product2->setUnitPrice(100);
        $product2->setShippingWeight(10);

        $option1 = new Entity\Option;
        $option1->setname('Option 1');

        $optionProduct = new Entity\OptionProduct;
        $optionProduct->setOption($option1);
        $optionProduct->setProduct($product2);

        $option2 = new Entity\Option;
        $option2->setname('Option 2');

        $optionValue = new Entity\OptionValue;
        $optionValue->setOption($option2);
        $optionValue->setSku('OV1');
        $optionValue->setUnitPrice(100);
        $optionValue->setShippingWeight(10);

        $textOption = new Entity\TextOption;

        $cartItemOptionProduct = new Entity\CartItemOptionProduct;
        $cartItemOptionProduct->setOptionProduct($optionProduct);

        $cartItemOptionValue = new Entity\CartItemOptionValue;
        $cartItemOptionValue->setOptionValue($optionValue);

        $cartItemTextOptionValue = new Entity\CartItemTextOptionValue;
        $cartItemTextOptionValue->setTextOption($textOption);
        $cartItemTextOptionValue->setTextOptionValue('Happy Birthday');

        $cartItem = new Entity\CartItem;
        $cartItem->setProduct($product);
        $cartItem->setQuantity(2);
        $cartItem->setCart(new Entity\Cart);
        $cartItem->addCartItemOptionProduct($cartItemOptionProduct);
        $cartItem->addCartItemOptionValue($cartItemOptionValue);
        $cartItem->addCartItemTextOptionValue($cartItemTextOptionValue);

        return $cartItem;
    }

    protected function getDummyCartItem($product)
    {
        $cartItem = new Entity\CartItem;
        $cartItem->setProduct($product);
        $cartItem->setQuantity(2);

        return $cartItem;
    }

    protected function getDummyTaxRate()
    {
        $taxRate = new Entity\TaxRate;
        $taxRate->setState('CA');
        $taxRate->setZip5(90403);
        $taxRate->setRate(7.5);
        $taxRate->setApplyToShipping(true);

        return $taxRate;
    }

    /**
     * @param Entity\CartItem[] $cartItems
     * @return Entity\Cart
     */
    protected function getDummyCart(array $cartItems = [])
    {
        $cart = new Entity\Cart;

        foreach ($cartItems as $cartItem) {
            $cart->addCartItem($cartItem);
        }

        return $cart;
    }

    protected function getDummyAddress()
    {
        $address = new Entity\Address;
        $address->setAttention('John Doe');
        $address->setCompany('Acme Co.');
        $address->setAddress1('123 Any St');
        $address->setAddress2('Ste 3');
        $address->setCity('Santa Monica');
        $address->setState('CA');
        $address->setZip5('90401');
        $address->setZip4('3274');
        $address->setPoint(new Entity\Point(34.010947, -118.490541));

        return $address;
    }

    protected function getDummyWarehouse($num = 1)
    {
        $address = $this->getDummyAddress();

        $warehouse = new Entity\Warehouse;
        $warehouse->setName('Test Warehouse #' . $num);
        $warehouse->setAddress($address);

        return $warehouse;
    }

    protected function getDummyOption()
    {
        $option = new Entity\Option;
        $option->setName('Size');
        $option->setType(Entity\Option::TYPE_RADIO);
        $option->setDescription('Shirt Size');
        $option->setSortOrder(0);

        return $option;
    }

    protected function getDummyTextOption()
    {
        $textOption = new Entity\TextOption;
        $textOption->setName('Size');
        $textOption->setType(Entity\TextOption::TYPE_TEXTAREA);
        $textOption->setDescription('Shirt Size');
        $textOption->setSortOrder(0);

        return $textOption;
    }

    protected function getDummyOptionProduct(Entity\Option $option, Entity\Product $product)
    {
        $optionProduct = new Entity\OptionProduct;
        $optionProduct->setProduct($product);
        $optionProduct->setSortOrder(0);
        $optionProduct->setOption($option);

        return $optionProduct;
    }

    protected function getDummyOptionValue(Entity\Option $option)
    {
        $optionValue = new Entity\OptionValue;
        $optionValue->setName('Option Value Name');
        $optionValue->setSku('OV-SKU');
        $optionValue->setShippingWeight(16);
        $optionValue->setSortOrder(0);
        $optionValue->setUnitPrice(100);
        $optionValue->setOption($option);

        return $optionValue;
    }

    protected function getDummyAttribute()
    {
        $attribute = new Entity\Attribute;
        $attribute->setName('Test Attribute');
        $attribute->setDescription('Test Attribute Description');
        $attribute->setSortOrder(0);

        return $attribute;
    }

    protected function getDummyAttributeValue()
    {
        $attribute = new Entity\AttributeValue;
        $attribute->setSku('TAV');
        $attribute->setName('Test Attribute Value');
        $attribute->setDescription('Test Attribute Value Description');
        $attribute->setSortOrder(0);

        return $attribute;
    }

    protected function getDummyProductAttribute()
    {
        $productAttribute = new Entity\ProductAttribute;

        return $productAttribute;
    }

    protected function repository()
    {
        return new Lib\FactoryRepository($this->entityManager);
    }

    protected function service(Lib\CartCalculatorInterface $cartCalculator)
    {
        return new Lib\FactoryService($this->repository(), $cartCalculator);
    }

    protected function beginTransaction()
    {
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function rollback()
    {
        $this->entityManager->getConnection()->rollback();
    }

    protected function getOptionProduct(Product $product)
    {
        $option = new Option;
        $option->setType(Option::TYPE_SELECT);
        $option->setName('Team Logo');
        $option->setDescription('Embroidered Team Logo');
        $optionProduct = new OptionProduct;
        $optionProduct->setProduct($product);
        $optionProduct->setSortOrder(0);
        $optionProduct->setOption($option);

        return $optionProduct;
    }

    protected function getOptionValue()
    {
        $option = new Option;
        $option->setType(Option::TYPE_SELECT);
        $option->setName('Shirt Size');
        $option->setDescription('Shirt Size Description');
        $optionValue = new OptionValue;
        $optionValue->setSortOrder(0);
        $optionValue->setSku('MD');
        $optionValue->setName('Medium Shirt');
        $optionValue->setShippingWeight(0);
        $optionValue->setUnitPrice(500);
        $optionValue->setOption($option);

        return $optionValue;
    }

    protected function getTextOption()
    {
        $textOption = new TextOption;
        $textOption->setType(TextOption::TYPE_TEXTAREA);
        $textOption->setName('Custom Message');
        $textOption->setDescription('Custom engraved message');

        return $textOption;
    }

    protected function getFullDummyOrderItem()
    {
        $price = new Price;
        $price->addCatalogPromotion(new CatalogPromotion);
        $price->addProductQuantityDiscount(new ProductQuantityDiscount);

        $logoProductQuantityDiscount = new ProductQuantityDiscount;
        $logoProductQuantityDiscount->setType(ProductQuantityDiscount::TYPE_FIXED);
        $logoProductQuantityDiscount->setQuantity(2);
        $logoProductQuantityDiscount->setValue(100);

        $logoProduct = new Product;
        $logoProduct->setSku('LAA');
        $logoProduct->setName('LA Angels');
        $logoProduct->setShippingWeight(6);
        $logoProduct->addProductQuantityDiscount($logoProductQuantityDiscount);

        $orderItemOptionProduct = new OrderItemOptionProduct;
        $orderItemOptionProduct->setOptionProduct($this->getOptionProduct($logoProduct));

        $orderItemOptionValue = new OrderItemOptionValue;
        $orderItemOptionValue->setOptionValue($this->getOptionValue());

        $orderItemTextOptionValue = new OrderItemTextOptionValue;
        $orderItemTextOptionValue->setTextOption($this->getTextOption());
        $orderItemTextOptionValue->setTextOptionValue('Happy Birthday');

        $orderItem = new OrderItem;
        $orderItem->setProduct(new Product);
        $orderItem->setQuantity(1);
        $orderItem->setPrice($price);
        $orderItem->addOrderItemOptionProduct($orderItemOptionProduct);
        $orderItem->addOrderItemOptionValue($orderItemOptionValue);
        $orderItem->addOrderItemTextOptionValue($orderItemTextOptionValue);

        return $orderItem;
    }
}
