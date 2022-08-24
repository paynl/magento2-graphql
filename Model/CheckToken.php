<?php

declare(strict_types=1);

namespace Paynl\Graphql\Model;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

class CheckToken
{
    const USER_TYPE_INTEGRATION  = 1;
    const USER_TYPE_ADMIN        = 2;
    const USER_TYPE_CUSTOMER     = 3;
    const USER_TYPE_GUEST        = 4;

    /**
     * @inheritdoc
     */
    public static function validate($context)
    {
        # Check if token is an integration or admin token
        if ($context->getUserType() !== self::USER_TYPE_INTEGRATION && $context->getUserType() !== self::USER_TYPE_ADMIN) {
            throw new GraphQlAuthorizationException(__('User not authorized.'));
        }
        return true;
    }
}