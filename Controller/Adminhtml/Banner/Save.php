<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BannerSlider\Controller\Adminhtml\Banner;

use DateTime;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Mageplaza\BannerSlider\Controller\Adminhtml\Banner;
use Mageplaza\BannerSlider\Helper\Image;
use Mageplaza\BannerSlider\Helper\Video;
use Mageplaza\BannerSlider\Model\BannerFactory;
use Mageplaza\BannerSlider\Model\Config\Source\Type;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Banner
 */
class Save extends Banner
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * Image Helper
     *
     * @var Image
     */
    protected $imageHelper;
    protected $videoHelper;

    
    protected $_dateFilter;

    /**
     * Save constructor.
     *
     * @param Image $imageHelper
     * @param BannerFactory $bannerFactory
     * @param Registry $registry
     * @param Js $jsHelper
     * @param Context $context
     */
    public function __construct(
        Image $imageHelper,
        Video $videoHelper,
        BannerFactory $bannerFactory,
        Registry $registry,
        Js $jsHelper,
        Context $context
    ) {
        $this->imageHelper = $imageHelper;
        $this->jsHelper = $jsHelper;

        parent::__construct($bannerFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');

        if ($this->getRequest()->getPost('banner')) {
            $data = $this->getRequest()->getPost('banner');
            $banner = $this->initBanner();
            if ($data['type'] === Type::IMAGE) {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_BANNER, $banner->getImage());
            } else if ($data['type'] === Type::VIDEO) {
                $this->imageHelper->uploadImage($data, 'image', Video::TEMPLATE_MEDIA_TYPE_BANNER, $banner->getImage());
            } {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
            $data['sliders_ids'] = (isset($data['sliders_ids']) && $data['sliders_ids'])
                ? explode(',', $data['sliders_ids']) : [];
            if ($this->getRequest()->getPost('sliders', false)) {
                $banner->setTagsData(
                    $this->jsHelper->decodeGridSerializedInput($this->getRequest()->getPost('sliders', false))
                );
            }

            $fromDate = $toDate = null;
            if (isset($data['from_date']) && isset($data['to_date'])) {
                $fromDate = $data['from_date'];
                $toDate = $data['to_date'];
            }
            if ($fromDate && $toDate) {
                $fromDate = new DateTime($fromDate);
                $toDate = new DateTime($toDate);

                if ($fromDate > $toDate) {

                    // make sure the date is converted to internal format
                    $fromFilter = new \Zend_Filter_Input(
                        ['quota_expiry' => $objDate],
                        [],
                        $data['from_date']
                    );
                    $toFilter = new \Zend_Filter_Input(
                        ['quota_expiry' => $objDate],
                        [],
                        $data['from_date']
                    );
                    $data['from_date'] = $fromFilter->getUnescaped();
                    $data['to_date'] = $toFilter->getUnescaped();

                    $this->messageManager->addErrorMessage(__('End Date must follow Start Date.'));
                    $this->_session->setPageData($data);
                    $this->dataPersistor->set('mpbannerslider_banner', $data);
                    $this->_redirect('*/*/edit', ['banner_id' => $slider->getId()]);

                    return;
                }
            }

            $banner->addData($data);

            $this->_eventManager->dispatch(
                'mpbannerslider_banner_prepare_save',
                [
                    'banner' => $banner,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $banner->save();
                $this->messageManager->addSuccess(__('The Banner has been saved.'));
                $this->_session->setMageplazaBannerSliderBannerData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mpbannerslider/*/edit',
                        [
                            'banner_id' => $banner->getId(),
                            '_current' => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mpbannerslider/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Banner.'));
            }

            $this->_getSession()->setData('mageplaza_bannerSlider_banner_data', $data);
            $resultRedirect->setPath(
                'mpbannerslider/*/edit',
                [
                    'banner_id' => $banner->getId(),
                    '_current' => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpbannerslider/*/');

        return $resultRedirect;
    }
}
