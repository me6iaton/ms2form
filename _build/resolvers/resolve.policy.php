<?php
/**
 * Resolve creating policies
 *
 * @var xPDOObject $object
 * @var array $options
 */

if ($object->xpdo) {
  /* @var modX $modx */
  $modx =& $object->xpdo;

  switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

      if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'ms2formUserPolicy'))) {
        if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'ms2formUserPolicyTemplate'))) {
          $policy->set('template', $template->get('id'));
          $policy->save();
        } else {
          $modx->log(xPDO::LOG_LEVEL_ERROR, '[ms2form] Could not find ms2formUserPolicy Access Policy Template!');
        }

        /* assign policy to admin group */
        if ($adminGroup = $modx->getObject('modUserGroup', array('name' => 'Administrator'))) {
          $properties = array(
            'target' => 'mgr'
          , 'principal_class' => 'modUserGroup'
          , 'principal' => $adminGroup->get('id')
          , 'authority' => 9999
          , 'policy' => $policy->get('id')
          );
          if (!$modx->getObject('modAccessContext', $properties)) {
            $access = $modx->newObject('modAccessContext');
            $access->fromArray($properties);
            $access->save();
          }
        }
        break;

      } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, '[ms2form] Could not find ms2formUserPolicy Access Policy!');
      }

      break;
  }
}
return true;