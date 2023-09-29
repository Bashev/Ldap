<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Webcode\Ldap\Block\System\Account\Edit;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @copyright  Copyright (c) 2017 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 * @link       https://github.com/Magenerds/Ldap
 * @author     Julian Schlarb <j.schlarb@techdivision.com>
 */
class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    protected function _prepareForm()
    {
        $result = parent::_prepareForm();
        $model = $this->_coreRegistry->registry('permissions_user');

        $isLdapUser = $model->getLdapDn();

        if (strlen(trim($isLdapUser)) === 0) {
            return $result;
        }

        $fieldsToDisable = ['username', 'firstname', 'lastname', 'email', 'password', 'password_confirmation'];

        $fields = $result->getForm()->getElements();
        foreach ($fields as $field) {
            /** @var AbstractElement $element */
            foreach ($field->getElements() as $element) {
                if (in_array($element->getName(), $fieldsToDisable)) {
                    $element->setReadonly(true);
                }
            }
        }

        return $result;
    }
}
