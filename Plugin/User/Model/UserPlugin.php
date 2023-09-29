<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @copyright  Copyright (c) 2017 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @link       https://github.com/Magenerds/Ldap
 * @author     Julian Schlarb <j.schlarb@techdivision.com>
 */
namespace Webcode\Ldap\Plugin\User\Model;

use Closure;
use Magento\User\Model\User;

/**
 * Class UserPlugin
 */
final class UserPlugin
{
    /**
     * Skip before save methods if user comes from ldap
     *
     * @param User $subject
     * @param Closure $proceed
     * @return User
     */
    public function aroundValidateBeforeSave(User $subject, Closure $proceed)
    {
        // only validate non ldap users
        /** @noinspection PhpUndefinedMethodInspection */
        if ($subject->getLdapDn() && strlen(trim($subject->getLdapDn())) === 0) {
            return $proceed();
        }

        if ($subject->isObjectNew()) {
            $subject->setPassword(uniqid());
        }

        return $subject;
    }
}
