<?php
namespace WebwinkelKeur\Client\Response;

use WebwinkelKeur\Client\ResponseAbstract;

class ReviewsSummary extends ResponseAbstract
{
    public function __construct($summaryData)
    {
        $this->data['amount'] = $summaryData->amount;
        $this->data['rating_average'] = $summaryData->rating_average;

        $this->data['shippingtime'] = $summaryData->ratings_average->shippingtime;
        $this->data['customerservice'] = $summaryData->ratings_average->customerservice;
        $this->data['pricequality'] = $summaryData->ratings_average->pricequality;
        $this->data['aftersale'] = $summaryData->ratings_average->aftersale;
    }

    public function getAmount()
    {
        return $this->data['amount'];
    }

    public function getRatingAverage()
    {
        return $this->data['rating_average'];
    }

    public function getShippingTimeRating()
    {
        return $this->getData('rating_shippingtime');
    }

    public function getCustomerServiceRating()
    {
        return $this->getData('rating_customerservice');
    }

    public function getPriceQualityRating()
    {
        return $this->getData('rating_pricequality');
    }

    public function getAfterSaleRating()
    {
        return $this->getData('rating_aftersale');
    }
}
