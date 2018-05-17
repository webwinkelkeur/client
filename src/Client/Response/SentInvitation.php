<?php
namespace WebwinkelKeur\Client\Response;

use WebwinkelKeur\Client\ResponseAbstract;

class SentInvitation extends ResponseAbstract
{
    public function __construct($invitationData)
    {
        $this->data['email'] = $invitationData->email;
        $this->data['order'] = $invitationData->order;
        $this->data['delay'] = $invitationData->delay;
        $this->data['created'] = new \DateTimeImmutable($invitationData->datetimes->created);
        $this->data['scheduled'] = new \DateTimeImmutable($invitationData->datetimes->scheduled);
        $this->data['sent'] = new \DateTimeImmutable($invitationData->datetimes->sent);
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getOrderNumber()
    {
        return $this->getData('order');
    }

    public function getDelay()
    {
        return $this->getData('delay');
    }

    public function getCreatedAt()
    {
        return $this->getData('created');
    }

    public function getScheduledAt()
    {
        return $this->getData('scheduled');
    }

    public function getSentAt()
    {
        return $this->getData('sent');
    }
}
