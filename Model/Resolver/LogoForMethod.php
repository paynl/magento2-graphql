<?php

declare (strict_types = 1);

namespace Paynl\Graphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class LogoForMethod implements ResolverInterface
{
    /**
     * @var DataProvider\GetMethodVars
     */
    private $getMethodVars;

    /**
     * @param DataProvider\GetMethodVars $getMethodVars
     */
    public function __construct(DataProvider\GetMethodVars $getMethodVars)
    {
        $this->getMethodVars = $getMethodVars;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $cart = $value['model'] ?? null;
        $method = $value['code'] ?? null;
        if (empty($method) && !empty($cart)) {
            $method = $cart->getPayment()->getMethod();
        }
        if (empty($method) || strpos($method, 'paynl_') === false) {
            return null;
        }
        return $this->getMethodVars->getLogo($method);
    }
}
