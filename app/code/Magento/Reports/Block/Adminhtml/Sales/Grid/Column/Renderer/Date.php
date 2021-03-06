<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer;

use Magento\Framework\Locale\Bundle\DataBundle;

/**
 * Adminhtml grid item renderer date
 */
class Date extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Date
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            $dataBundle = new DataBundle();
            $resourceBundle = $dataBundle->get($this->_localeResolver->getLocale());
            $formats = $resourceBundle['calendar']['gregorian']['availableFormats'];
            switch ($this->getColumn()->getPeriodType()) {
                case 'month':
                    $format = $formats['yM'];
                    break;
                case 'year':
                    $format = $formats['y'];
                    break;
                default:
                    $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
                    break;
            }
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            switch ($this->getColumn()->getPeriodType()) {
                case 'month':
                    $data = $data . '-01';
                    break;
                case 'year':
                    $data = $data . '-01-01';
                    break;
            }
            $format = $this->_getFormat();
            if ($this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone()) {
                $date = $this->_localeDate->date(new \DateTime($data));
            } else {
                $date = $this->_localeDate->date(new \DateTime($data), null, false);
            }
            return \IntlDateFormatter::formatObject($date, $format);
        }
        return $this->getColumn()->getDefault();
    }
}
