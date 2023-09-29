<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Magenerds
 * @package    Magenerds_Ldap
 * @copyright  Copyright (c) 2017 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @link       https://github.com/Magenerds/Ldap
 * @author     Julian Schlarb <j.schlarb@techdivision.com>
 */
namespace Webcode\Ldap\Api;

use Laminas\Ldap\Collection;

/**
 * Interface LdapClientInterface
 *
 * @package Magenerds\Ldap\Api
 */
interface LdapClientInterface
{
    /**
     * @return Collection
     */
    function bind();

    /**
     * Try to bind with the ldap server
     *
     * @return boolean true if ldap is connected otherwise false
     */
    function canBind();

    /**
     * A global LDAP search routine for finding information of a user.
     *
     * @param string $username
     *
     * @return Collection
     */
    function getUserByUsername($username);
}
