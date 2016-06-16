<?php
/**
 * A backend model to update a cron schedule
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Model_System_Config_Backend_CronPriceUpdate extends Mage_Core_Model_Config_Data
{
    /**
     * Cron schedule configuration path
     *
     * @var string
     */
    const XPATH_CRON_SCHEDULE = 'crontab/jobs/tim_priceupdate_update_price/schedule/cron_expr';

    /**
     * Cron model configuration path
     *
     * @var string
     */
    const XPATH_CRON_MODEL = 'crontab/jobs/tim_priceupdate_update_price/run/model';

    /**
     * Cron settings after save
     *
     * @return null
     */
    protected function _afterSave()
    {
        $time      = $this->getData('groups/price_update/fields/cron_time/value');
        $frequency = $this->getData('groups/price_update/fields/cron_frequency/value');

        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronSchedule = array();
        $cronSchedule[] = ltrim($time[1], '0');                                      // minute
        $cronSchedule[] = ltrim($time[0], '0');                                      // hour
        $cronSchedule[] = ($frequency == $frequencyMonthly) ? '1' : '*'; // day
        $cronSchedule[] = '*';                                           // month
        $cronSchedule[] = ($frequency == $frequencyWeekly) ? '1' : '*';  // day of the week
        $cronSchedule = implode(' ', $cronSchedule);

        // todo: remove this after tests
        $cronSchedule = '*/10 * * * *';

        try {
            Mage::getModel('core/config_data')
                ->load(self::XPATH_CRON_SCHEDULE, 'path')
                ->setValue($cronSchedule)
                ->setPath(self::XPATH_CRON_SCHEDULE)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::XPATH_CRON_MODEL, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::XPATH_CRON_MODEL))
                ->setPath(self::XPATH_CRON_MODEL)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('tim_priceupdate')->__('Unable to save the cron expression.'));
        }
    }
}