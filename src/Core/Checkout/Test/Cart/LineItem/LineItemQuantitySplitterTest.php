<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\LineItem;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemQuantitySplitter;
use Laser\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class LineItemQuantitySplitterTest extends TestCase
{
    use KernelTestBehaviour;

    private SalesChannelContext $salesChannelContext;

    protected function setUp(): void
    {
        $context = $this->createMock(SalesChannelContext::class);
        $context->method('getTaxState')->willReturn(CartPrice::TAX_STATE_GROSS);
        $context->method('getItemRounding')->willReturn(new CashRoundingConfig(2, 0.01, true));

        $this->salesChannelContext = $context;
    }

    /**
     * @dataProvider splitProvider
     */
    public function testSplit(int $itemQty, int $splitterQty, int $calcExpects): void
    {
        $splitter = $this->createQtySplitter($calcExpects);

        $lineItem = new LineItem(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE, Uuid::randomHex(), $itemQty);
        $lineItem->setPrice(new CalculatedPrice(10, 99, new CalculatedTaxCollection(), new TaxRuleCollection(), $itemQty));
        $lineItem->setStackable(true);

        $newLineItem = $splitter->split($lineItem, $splitterQty, $this->salesChannelContext);

        if ($calcExpects <= 0) {
            static::assertEquals($lineItem, $newLineItem);
        } else {
            $expectedPrice = 10 * $splitterQty;

            static::assertNotEquals($lineItem, $newLineItem);
            static::assertEquals($splitterQty, $newLineItem->getQuantity());
            static::assertEquals($expectedPrice, $newLineItem->getPrice()->getTotalPrice());
        }
    }

    public static function splitProvider(): iterable
    {
        yield 'should not split items when item qty = 10 and splitter qty = 10' => [10, 10, 0];
        yield 'should split items when item qty = 10 and splitter qty = 9' => [10, 9, 1];
        yield 'should split items when item qty = 9 and splitter qty = 10' => [9, 10, 1];
    }

    private function createQtySplitter(int $expects): LineItemQuantitySplitter
    {
        $qtyCalc = $this->createMock(QuantityPriceCalculator::class);
        $qtyCalc
            ->expects(static::exactly($expects))
            ->method('calculate')
            ->willReturnCallback(fn (QuantityPriceDefinition $definition, SalesChannelContext $context) => $this->getContainer()->get(QuantityPriceCalculator::class)->calculate($definition, $context));

        return new LineItemQuantitySplitter($qtyCalc);
    }
}