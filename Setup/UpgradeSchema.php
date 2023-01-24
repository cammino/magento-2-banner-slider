<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_All
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Mageplaza\BannerSlider\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $setup->getConnection()->addColumn(
                $installer->getTable('mageplaza_bannerslider_banner'),
                'from_date',
                [
                    'type' => Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'From',
                    'after' => 'newtab'
                ]
            );
            $setup->getConnection()->addColumn(
                $installer->getTable('mageplaza_bannerslider_banner'),
                'to_date',
                [
                    'type' => Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'To',
                    'after' => 'newtab'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $setup->getConnection()->addColumn(
                $installer->getTable('mageplaza_bannerslider_banner'),
                'device',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Device',
                    'after' => 'newtab'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.3.0') < 0) {
            $setup->getConnection()->addColumn(
                $installer->getTable('mageplaza_bannerslider_banner'),
                'width',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Width',
                    'after' => 'newtab'
                ]
            );
            $setup->getConnection()->addColumn(
                $installer->getTable('mageplaza_bannerslider_banner'),
                'height',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Height',
                    'after' => 'newtab'
                ]
            );
        }

        $installer->endSetup();
    }
}