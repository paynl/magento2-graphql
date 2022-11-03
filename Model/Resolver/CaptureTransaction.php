<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Paynl\Graphql\Model\CheckToken;

class CaptureTransaction implements ResolverInterface
{
    private $captureTransactionDataProvider;

    /**
     * @param DataProvider\CaptureTransaction $CaptureTransactionRepository
     */
    public function __construct(DataProvider\CaptureTransaction $captureTransactionDataProvider)
    {
        $this->captureTransactionDataProvider = $captureTransactionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        CheckToken::validate($context);
        return $this->captureTransactionDataProvider->CaptureTransaction($args);
    }
}
