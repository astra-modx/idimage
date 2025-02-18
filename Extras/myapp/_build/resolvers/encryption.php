<?php
if (getenv('PACKAGE_ENCRYPTION') === 'True' && getenv('PACKAGE_DEPLOY') === 'True') {
    $transport->xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
    $transport->xpdo->loadClass('EncryptedVehicle', MODX_CORE_PATH . 'components/' . strtolower($transport->name) . '/model/', true, true);
}
