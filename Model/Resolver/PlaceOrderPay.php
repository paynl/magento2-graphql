<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\QuoteGraphQl\Model\Resolver\PlaceOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PlaceOrderPay implements ResolverInterface
{
    /**
     * @var DataProvider\StartTransaction
     */
    private $startTransactionDataProvider;

    /**
     * @var string
     */
    public $returnUrl = '';

    /**
     * @param DataProvider\StartTransaction $startTransactionDataProvider
     */
    public function __construct(DataProvider\StartTransaction $startTransactionDataProvider)
    {
        $this->startTransactionDataProvider = $startTransactionDataProvider;
    }

    /**
     * @var boolean
     */
    public $sendIncrementId  = false;

    /**
     * @inheritdoc
     */
    public function beforeResolve(
        PlaceOrder $subject,
        $field,
        $context,
        $info,
        array $value = null,
        array $args = null
    ) {
        if (isset($args['input']['pay_return_url'])) {
            $this->returnUrl = $args['input']['pay_return_url'];            
        }
        if (isset($args['input']['pay_send_increment_id'])) {
            $this->sendIncrementId = $args['input']['pay_send_increment_id'];            
        }
    }  
   
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {               
        return $this->startTransactionDataProvider->placeOrder($value['order_number'], $this->returnUrl);
    }
}
