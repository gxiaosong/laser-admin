<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Rule;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Checkout\Cart\Rule\GoodsCountRule;
use Laser\Core\Checkout\Cart\Rule\LineItemOfTypeRule;
use Laser\Core\Checkout\Test\Cart\Rule\Helper\CartRuleHelperTrait;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Container\AndRule;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Test\TestCaseBase\DatabaseTransactionBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Test\TestCaseHelper\ReflectionHelper;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 *
 * @group rules
 */
#[Package('business-ops')]
class GoodsCountRuleTest extends TestCase
{
    use CartRuleHelperTrait;
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

    public function testValidateWithMissingParameters(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(2, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[0]['code']);

            static::assertSame('/0/value/operator', $exceptions[1]['source']['pointer']);
            static::assertSame(NotBlank::IS_BLANK_ERROR, $exceptions[1]['code']);
        }
    }

    public function testValidateWithStringCount(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'operator' => Rule::OPERATOR_EQ,
                        'count' => '3',
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(Type::INVALID_TYPE_ERROR, $exceptions[0]['code']);
        }
    }

    public function testValidateWithFloatCount(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'operator' => Rule::OPERATOR_EQ,
                        'count' => 1.1,
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/count', $exceptions[0]['source']['pointer']);
            static::assertSame(Type::INVALID_TYPE_ERROR, $exceptions[0]['code']);
        }
    }

    public function testAvailableOperators(): void
    {
        $ruleId = Uuid::randomHex();
        $this->ruleRepository->create(
            [['id' => $ruleId, 'name' => 'Demo rule', 'priority' => 1]],
            Context::createDefaultContext()
        );

        $conditionIdEq = Uuid::randomHex();
        $conditionIdNEq = Uuid::randomHex();
        $conditionIdLTE = Uuid::randomHex();
        $conditionIdGTE = Uuid::randomHex();
        $this->conditionRepository->create(
            [
                [
                    'id' => $conditionIdEq,
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => $ruleId,
                    'value' => [
                        'count' => 1,
                        'operator' => Rule::OPERATOR_EQ,
                    ],
                ],
                [
                    'id' => $conditionIdNEq,
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => $ruleId,
                    'value' => [
                        'count' => 1,
                        'operator' => Rule::OPERATOR_NEQ,
                    ],
                ],
                [
                    'id' => $conditionIdLTE,
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => $ruleId,
                    'value' => [
                        'count' => 1,
                        'operator' => Rule::OPERATOR_LTE,
                    ],
                ],
                [
                    'id' => $conditionIdGTE,
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => $ruleId,
                    'value' => [
                        'count' => 1,
                        'operator' => Rule::OPERATOR_GTE,
                    ],
                ],
            ],
            $this->context
        );

        static::assertCount(
            4,
            $this->conditionRepository->search(
                new Criteria([$conditionIdEq, $conditionIdNEq, $conditionIdLTE, $conditionIdGTE]),
                $this->context
            )
        );
    }

    public function testValidateWithInvalidOperator(): void
    {
        try {
            $this->conditionRepository->create([
                [
                    'type' => (new GoodsCountRule())->getName(),
                    'ruleId' => Uuid::randomHex(),
                    'value' => [
                        'count' => 42,
                        'operator' => 'Invalid',
                    ],
                ],
            ], $this->context);
            static::fail('Exception was not thrown');
        } catch (WriteException $stackException) {
            $exceptions = iterator_to_array($stackException->getErrors());
            static::assertCount(1, $exceptions);
            static::assertSame('/0/value/operator', $exceptions[0]['source']['pointer']);
            static::assertSame(Choice::NO_SUCH_CHOICE_ERROR, $exceptions[0]['code']);
        }
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
                'type' => (new GoodsCountRule())->getName(),
                'ruleId' => $ruleId,
                'value' => [
                    'operator' => Rule::OPERATOR_EQ,
                    'count' => 1,
                ],
            ],
        ], $this->context);

        static::assertNotNull($this->conditionRepository->search(new Criteria([$id]), $this->context)->get($id));
    }

    public function testCreateRuleWithFilter(): void
    {
        $ruleId = Uuid::randomHex();
        $this->ruleRepository->create(
            [
                [
                    'id' => $ruleId,
                    'name' => 'LineItemRule',
                    'priority' => 0,
                    'conditions' => [
                        [
                            'type' => (new GoodsCountRule())->getName(),
                            'ruleId' => $ruleId,
                            'children' => [
                                [
                                    'type' => (new LineItemOfTypeRule())->getName(),
                                    'value' => [
                                        'lineItemType' => 'test',
                                        'operator' => Rule::OPERATOR_EQ,
                                    ],
                                ],
                            ],
                            'value' => [
                                'count' => 100,
                                'operator' => Rule::OPERATOR_GTE,
                            ],
                        ],
                    ],
                ],
            ],
            Context::createDefaultContext()
        );

        $rule = $this->ruleRepository->search(new Criteria([$ruleId]), Context::createDefaultContext())->get($ruleId);

        static::assertNotNull($rule);
        static::assertFalse($rule->isInvalid());
        static::assertInstanceOf(AndRule::class, $rule->getPayload());
        /** @var AndRule $andRule */
        $andRule = $rule->getPayload();
        static::assertInstanceOf(GoodsCountRule::class, $andRule->getRules()[0]);
        $filterRule = ReflectionHelper::getProperty(GoodsCountRule::class, 'filter')->getValue($andRule->getRules()[0]);
        static::assertInstanceOf(AndRule::class, $filterRule);
        static::assertInstanceOf(LineItemOfTypeRule::class, $filterRule->getRules()[0]);
    }

    public function testFilter(): void
    {
        $item = $this->createLineItemWithPrice('test', 40);
        $item->setGood(true);

        $item2 = $this->createLineItemWithPrice('test', 100);
        $item2->setGood(true);

        $item3 = $this->createLineItemWithPrice('test-not-matching', 30);
        $item3->setGood(true);

        $cart = $this->createCart(new LineItemCollection([$item, $item2, $item3]));

        $this->assertRuleMatches($cart);
    }

    public function testFilterNested(): void
    {
        $item = $this->createLineItemWithPrice('test', 40);
        $item->setGood(true);

        $item2 = $this->createLineItemWithPrice('test', 100);
        $item2->setGood(true);

        $item3 = $this->createLineItemWithPrice('test-not-matching', 30);
        $item3->setGood(true);

        $containerLineItem = $this->createContainerLineItem(new LineItemCollection([$item, $item2, $item3]));
        $cart = $this->createCart(new LineItemCollection([$containerLineItem]));

        $this->assertRuleMatches($cart);
    }

    private function assertRuleMatches(Cart $cart): void
    {
        $rule = (new GoodsCountRule())->assign([
            'count' => 2,
            'filter' => new AndRule([
                (new LineItemOfTypeRule())
                    ->assign(['lineItemType' => 'test']),
            ]),
            'operator' => Rule::OPERATOR_EQ,
        ]);

        $mock = $this->createMock(SalesChannelContext::class);
        $scope = new CartRuleScope($cart, $mock);

        static::assertTrue($rule->match($scope));
    }
}