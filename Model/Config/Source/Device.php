<?php

namespace Mageplaza\BannerSlider\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Device implements ArrayInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => 'desktop', 'label' => __('Desktop')],
    ['value' => 'mobile', 'label' => __('Mobile')]
  ];
 }
}
