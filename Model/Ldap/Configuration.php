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
namespace Webcode\Ldap\Model\Ldap;

use Magento\Framework\App\DeploymentConfig;
use Webcode\Ldap\Api\ConfigInterface;

class Configuration implements ConfigInterface
{
    /**
     * @var DeploymentConfig
     */
    private DeploymentConfig $deploymentConfig;

    /**
     * Configuration constructor.
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        DeploymentConfig $deploymentConfig
    ) {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @inheritDoc
     */
    public function getUserFilter()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_USER_FILTER,
            ConfigInterface::DEFAULT_USER_FILTER
        );
    }

    /**
     * @inheritDoc
     */
    public function getCachePassword()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_CACHE_PASSWORD);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeNameUsername()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_ATTRIBUTE_USERNAME,
            ConfigInterface::DEFAULT_ATTRIBUTE_USERNAME
        );
    }

    /**
     * @inheritDoc
     */
    public function getAttributeNameFirstName()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_ATTRIBUTE_FIRST_NAME,
            ConfigInterface::DEFAULT_ATTRIBUTE_FIRST_NAME
        );
    }

    /**
     * @inheritDoc
     */
    public function getAttributeNameLastName()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_ATTRIBUTE_LAST_NAME,
            ConfigInterface::DEFAULT_ATTRIBUTE_LAST_NAME
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultRoleId()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_ROLE);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeNameEmail()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_ATTRIBUTE_EMAIL,
            ConfigInterface::DEFAULT_ATTRIBUTE_EMAIL
        );
    }

    /**
     * @inheritDoc
     */
    public function getLdapConnectionOptions()
    {
        return [
            'host' => $this->getHost(),
            'port' => $this->getPort(),
            'useSsl' => $this->getUseSsl(),
            'username' => $this->getBindDn(),
            'password' => $this->getBindPassword(),
            'bindRequiresDn' => $this->getBindRequiresDn(),
            'baseDn' => $this->getBaseDn(),
            'useStartTls' => $this->getUseStartTls(),
            'accountFilterFormat' => $this->getUserFilter(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getHost()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_HOST);
    }

    /**
     * @inheritDoc
     */
    public function getPort()
    {
        return $this->deploymentConfig->get(
            ConfigInterface::CONFIG_KEY_PORT,
            ConfigInterface::DEFAULT_PORT
        );
    }

    /**
     * @inheritDoc
     */
    public function getUseSsl()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_USE_SSL);
    }

    /**
     * @inheritDoc
     */
    public function getBindDn()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_BIND_DN);
    }

    /**
     * @inheritDoc
     */
    public function getBindPassword()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_BIND_PASSWORD);
    }

    /**
     * @inheritDoc
     */
    public function getBindRequiresDn()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_BIND_REQUIRES_DN);
    }

    /**
     * @inheritDoc
     */
    public function getBaseDn()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_BASE_DN);
    }

    /**
     * @inheritDoc
     */
    public function getAllowEmptyPassword()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_ALLOW_EMPTY_PASSWORD);
    }

    /**
     * @inheritDoc
     */
    public function getUseStartTls()
    {
        return $this->deploymentConfig->get(ConfigInterface::CONFIG_KEY_USE_TLS);
    }
}
