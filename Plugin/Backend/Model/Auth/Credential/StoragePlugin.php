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
namespace Webcode\Ldap\Plugin\Backend\Model\Auth\Credential;

use Closure;
use Exception;
use Laminas\Ldap\Exception\LdapException;
use Magento\Backend\Model\Auth\Credential\StorageInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\User\Model\User;
use Psr\Log\LoggerInterface;
use Webcode\Ldap\Api\LdapClientInterface;
use Webcode\Ldap\Model\Ldap\Configuration;
use Webcode\Ldap\Model\Ldap\UserMapper;

/**
 * Class StoragePlugin
 */
class StoragePlugin
{
    /**
     * @var LdapClientInterface
     */
    private $ldapClient;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var \Magento\User\Model\ResourceModel\User
     */
    private $userResource;


    /**
     * StoragePlugin constructor.
     *
     * @param LoggerInterface $logger
     * @param LdapClientInterface $ldapClient
     * @param UserMapper $userMapper
     * @param ManagerInterface $eventManager
     * @param Configuration $configuration
     * @param \Magento\User\Model\ResourceModel\User $userResource
     * @internal param User $user
     */
    public function __construct(
        LoggerInterface $logger,
        LdapClientInterface $ldapClient,
        UserMapper $userMapper,
        ManagerInterface $eventManager,
        Configuration $configuration,
        \Magento\User\Model\ResourceModel\User $userResource
    ) {
        $this->ldapClient = $ldapClient;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->userMapper = $userMapper;
        $this->configuration = $configuration;
        $this->userResource = $userResource;
    }

    /**
     * @param StorageInterface $subject
     * @param Closure $proceed
     * @param $username
     * @param $password
     * @return bool
     * @throws LocalizedException
     * @throws LdapException
     */
    public function aroundAuthenticate(StorageInterface $subject, Closure $proceed, $username, $password)
    {
        // Skip ldap auth mechanism if someone replaced user
        if (!$subject instanceof User) {
            $msg = 'Ldap auth is unable to proceed. Type mismatch, expected [%s] but was [%s]';

            $this->logger->critical(sprintf($msg, User::class, get_class($subject)));

            return $proceed($username, $password);
        }

        $subject->loadByUsername($username);

        // allow local users to login
        if (!$subject->isEmpty() && strlen(trim((string)$subject->getLdapDn())) === 0) {
            // go the magento way and provide the ability to call other auth mechanism
            return $proceed($username, $password);
        }

        $result = false;

        try {
            $params = ['username' => $username, 'user' => $subject];

            $this->eventManager->dispatch('admin_user_authenticate_before', $params);

            $this->ldapClient->setUsername($username);
            $this->ldapClient->setPassword($password);

            // try to use local credentials if present
            if (!$this->ldapClient->canBind() && !$subject->isEmpty()) {
                if ($this->configuration->getCachePassword()) {
                    return $proceed($username, $password);
                }

                throw new LocalizedException(
                    __('Login temporarily deactivated. Check your logs for more Information.')
                );
            }

            if ($ldapUser = $this->ldapClient->getUserByUsername($username)) {
                $this->userMapper->mapUser($ldapUser, $password, $subject);
                $this->userResource->save($subject);
                $result = true;

                $this->validateIdentity($subject);

                $params = ['username' => $username, 'password' => $password, 'user' => $subject, 'result' => $result];

                $this->eventManager->dispatch('admin_user_authenticate_after', $params);
            }

        } catch (LocalizedException|LdapException|Exception $e) {
            $subject->unsetData();
            throw $e;
        }

        if ($result === false) {
            $subject->unsetData();
        }

        return $result;
    }

    /**
     * Check if user is active and has any assigned role
     *
     * @param User $user
     * @throws AuthenticationException
     * @return void
     */
    private function validateIdentity(User $user)
    {
        $isActive = $user->getIsActive();

        if (empty($isActive)) {
            throw new AuthenticationException(
                __('You did not sign in correctly or your account is temporarily disabled.')
            );
        }

        if (!$user->hasAssigned2Role($user)) {
             throw new AuthenticationException(__('You need more permissions to access this.'));
        }
    }
}
