<?php

if ($object->xpdo) {
  /** @var modX $modx */
  $modx =& $object->xpdo;

  switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
      $modelPath = $modx->getOption('ms2form_core_path', null, $modx->getOption('core_path') . 'components/ms2form/') . 'model/';
      $modx->addPackage('ms2form', $modelPath);

      $manager = $modx->getManager();
      $objects = array(
        'ms2formItem',
      );
      foreach ($objects as $tmp) {
        $manager->createObjectContainer($tmp);
      }
      break;

    case xPDOTransport::ACTION_UPGRADE:
      break;

    case xPDOTransport::ACTION_UNINSTALL:
      break;
  }
}
return true;
