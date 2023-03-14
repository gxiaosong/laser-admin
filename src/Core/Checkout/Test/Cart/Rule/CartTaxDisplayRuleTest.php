<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Rule;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Rule\CartTaxDisplayRule;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('business-ops')]
class CartTaxDisplayRuleTest extends TestCase
{
    use KernelTestBehaviour;
    use DatabaseTransactionBehaviour;

    private EntityRepository $ruleRepository;

    private EntityRepository $conditionRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->ruleRepository = $this->getContainer()->get('rule.repository');
        $this->conditionRepository = $this->getContainer()->get('rule_condition.repository');
        $this->context = Context::createDefaultContext();
    }

    public function testIfRuleIsConsistent(): void
    {
        $ruleId = Uuid::randomHex();
        $this->ruleRepository->create(
            [['id' => $ruleId, 'name' => 'Demo rule', 'priority' => 1]],
            Context::createDefaultContext()
        );

        $id = Uuid::randomHex();
        $this->conditionRepository->create([
            [
                'id' => $id,
                'type' => (new CartTaxDisplayRule())->getName(),
                'ruleId' => $ruleId,
                'value' => [
                    'taxDisplay' => 'gross',
                ],
            ],
        ], $this->context);

        static::assertNotNull($this->conditionRepository->search(new Criteria([$id]), $this->context)->get($id));
    }

    public function testThatTaxStateMatches(): void
    {
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getTaxState')->willReturn('gross');
        $isGrossRule = new CartTaxDisplayRule('gross');
        $scope = new CheckoutRuleScope($salesChannelContext);

        static::assertTrue($isGrossRule->match($scope));
    }

    public function testThatTaxStateNotMatches(): void
    {
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getTaxState')->willReturn('net');
        $isGrossRule = new CartTaxDisplayRule('gross');
        $scope = new CheckoutRuleScope($salesChannelContext);

        static::assertFalse($isGrossRule->match($scope));
    }
}