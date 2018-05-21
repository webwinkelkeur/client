<?php
namespace WebwinkelKeur\Client\Response;

use WebwinkelKeur\Client\ResponseAbstract;

class Review extends ResponseAbstract
{
    public function __construct($reviewData)
    {
        $this->data['name'] = $reviewData->name;
        $this->data['email'] = $reviewData->email;
        $this->data['rating'] = $reviewData->rating;
        $this->data['rating_shippingtime'] = $reviewData->ratings->shippingtime;
        $this->data['rating_customerservice'] = $reviewData->ratings->customerservice;
        $this->data['rating_pricequality'] = $reviewData->ratings->pricequality;
        $this->data['rating_aftersale'] = $reviewData->ratings->aftersale;
        $this->data['comment'] = $reviewData->comment;
        $this->data['date'] = new \DateTimeImmutable($reviewData->date);
        $this->data['read'] = $reviewData->read;
        $this->data['quarantine'] = $reviewData->quarantine;
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
