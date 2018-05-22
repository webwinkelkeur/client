<?php
namespace WebwinkelKeur\Client\Response;

use WebwinkelKeur\Client\ResponseAbstract;

class Rating extends ResponseAbstract
{
    public function __construct($ratingData)
    {
        $this->data['name'] = $ratingData->name;
        $this->data['email'] = $ratingData->email;
        $this->data['rating'] = $ratingData->rating;
        $this->data['rating_shippingtime'] = $ratingData->ratings->shippingtime;
        $this->data['rating_customerservice'] = $ratingData->ratings->customerservice;
        $this->data['rating_pricequality'] = $ratingData->ratings->pricequality;
        $this->data['rating_aftersale'] = $ratingData->ratings->aftersale;
        $this->data['comment'] = $ratingData->comment;
        $this->data['date'] = new \DateTimeImmutable($ratingData->date);
        $this->data['read'] = $ratingData->read;
        $this->data['quarantine'] = $ratingData->quarantine;
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getRating()
    {
        return $this->getData('rating');
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

    public function getComment()
    {
        return $this->getData('comment');
    }

    public function getDate()
    {
        return $this->getData('date');
    }

    public function isRead()
    {
        return (bool)$this->getData('read');
    }

    public function isQuarantined()
    {
        return (bool)$this->getData('quarantine');
    }
}
