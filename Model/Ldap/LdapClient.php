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

use Exception;
use Laminas\Ldap\Collection;
use Laminas\Ldap\Exception\LdapException;
use Laminas\Ldap\Ldap;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Webcode\Ldap\Api\LdapClientInterface;

class LdapClient implements LdapClientInterface
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var Ldap|null
     */
    private ?Ldap $ldap = null;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $password;

    /**
     * LdapClient constructor.
     *
     * @param Configuration $configuration
     * @param LoggerInterface $logger
     */
    public function __construct(Configuration $configuration, LoggerInterface $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function getBoundUser(): string
    {
        try {
            $this->bind();
            return (string)$this->ldap->getBoundUser();
        } catch (LdapException|Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__('Login temporarily deactivated. Check your logs for more Information.'));
        }
    }

    /**
     * @param string $username
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function getUserByUsername(string $username): array
    {
        try {
            if ($this->ldap === null) {
                $this->bind();
            }

            if ($this->ldap->getBoundUser()) {
                return $this->ldap->getEntry($this->ldap->getBoundUser());
            }

            throw new LocalizedException(__('Login temporary deactivated. Check your logs for more Information.'));

        } catch (LdapException|Exception $e) {
            $this->logger->error($e);
            throw new LocalizedException(__('Login temporary deactivated. Check your logs for more Information.'));
        }
    }

    /**
     * @inheritDoc
     * @throws LdapException
     * @throws LocalizedException
     */
    public function bind(): void
    {
        if (empty($this->username) || empty($this->password)) {
            throw new LocalizedException(__('Missing username or password.'));
        }

        if ($this->ldap === null || !$this->ldap->getBoundUser()) {
            $options = $this->configuration->getLdapConnectionOptions();
            $params = [
                ':username' => $this->username,
                ':usernameAttribute' => $this->configuration->getAttributeNameUsername()
            ];

            $options['accountFilterFormat'] = strtr($this->configuration->getUserFilter(), $params);

            $this->ldap = new Ldap($options);
            $this->ldap->bind($this->username, $this->password);
        }
    }

    /**
     * @inheritDoc
     */
    public function canBind(): bool
    {
        try {
            $this->bind();
        } catch (LdapException|LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }
}
