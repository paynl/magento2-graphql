<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

class CaptureTransaction implements ResolverInterface
{
    const USER_TYPE_INTEGRATION  = 1;
    const USER_TYPE_ADMIN        = 2;
    const USER_TYPE_CUSTOMER     = 3;
    const USER_TYPE_GUEST        = 4;

    private $captureTransactionDataProvider;

    /**
     * @param DataProvider\CaptureTransaction $CaptureTransactionRepository
     */
    public function __construct(
        DataProvider\CaptureTransaction $captureTransactionDataProvider
    ) {
        $this->captureTransactionDataProvider = $captureTransactionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        // Check if token is an integration or admin token
        if ($context->getUserType() !== self::USER_TYPE_INTEGRATION && $context->getUserType() !== self::USER_TYPE_ADMIN) {
            throw new GraphQlAuthorizationException(__('User not authorized to issue Captures.'));
        }
        return $this->captureTransactionDataProvider->CaptureTransaction($args);
    }
}
