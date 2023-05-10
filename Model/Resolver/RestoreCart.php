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
     * @param Field $field
     * @param object $context
     * @param ResolveInfo info
     * @param array value
     * @param array args
     * @return boolean
     * @throws GraphQlAuthorizationException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        $cartId = $this->maskedQuoteIdToQuoteId->execute($args['cart_id']);
        $cart = $this->cartRepository->get($cartId);

        if ((int)$cart->getCustomerId() !== $context->getUserId()) {
            throw new GraphQlAuthorizationException(
                __(
                    'The current user cannot perform operations on cart "%cart_id"',
                    ['cart_id' => $args['cart_id']]
                )
            );
        }

        $cart->setIsActive(1);
        $this->cartRepository->save($cart);

        return true;
    }
}
