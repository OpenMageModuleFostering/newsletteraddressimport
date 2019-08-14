<?php
    $installer = $this;
    $installer->startSetup();
    $installer->run("
        ALTER TABLE `{$installer->getTable('newsladdressimport')}`
        ADD store_id text NOT NULL default '';
    ");
    $installer->endSetup();
?>