<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class RestoreCart implements ResolverInterface
{
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CartRepositoryInterface $cartRepository
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        $maskedCartId = $args['cart_id'];

        $cartId = $this->maskedQuoteIdToQuoteId->execute($maskedCartId);
        $cart = $this->cartRepository->get($cartId);

        $this->checkCartCustomerId($cart, $context->getUserId(), $maskedCartId);

        $cart->setIsActive(1);
        $this->cartRepository->save($cart);

        return ['cart' => ['model' => $cart]];
    }

    /**
     * @param Cart $cart
     * @param int $customerId
     * @param string maskedCartId
     * @return null
     * @throws GraphQlAuthorizationException
     */
    private function checkCartCustomerId($cart, $customerId, $maskedCartId)
    {
        $cartCustomerId = (int) $cart->getCustomerId();
        if ($cartCustomerId === 0) {
            return;
        }
        if ($cartCustomerId !== $customerId) {
            throw new GraphQlAuthorizationException(
                __(
                    'The current user cannot perform operations on cart "%masked_cart_id"',
                    ['masked_cart_id' => $maskedCartId]
                )
            );
        }
    }
}
