<?php

namespace SMSMode\Request;

/**
 * Class Response
 * @package SMSMode\Request
 * @author Mykhailo YATSYHSYN <myyat@mirko.in.ua>
 * @copyright Mirko 2021 <https://mirko.in.ua>
 */
class Response
{
    const ACTION_STATUS__PROGRESS = 'process';
    const ACTION_STATUS__SUCCESS = 'success';
    const ACTION_STATUS__ERROR = 'error';

    const STATUS__SENT = 0;
    const STATUS__INTERNAL_ERROR = 2;
    const STATUS__SCHEDULED = 10;
    const STATUS__RECEIVED = 11;
    const STATUS__DELIVERED = 13;
    const STATUS__PARTIALLY_DELIVERED = 15;
    const STATUS__DELIVERY_ERROR = 39;
    const STATUS__BALANCE_ERROR = 33;
    const STATUS__SMS_NOT_FOUND = 61;
    const STATUS__UNDEFINED_ERROR = 3599;
    const STATUS__INVALID_NUMBER_ERROR = 398;
    const STATUS__RECIPIENT_BLACKLIST = 3999;

    const ACTION_STATUS = [
        // progress
        self::STATUS__SENT => self::ACTION_STATUS__PROGRESS,
        self::STATUS__SCHEDULED => self::ACTION_STATUS__PROGRESS,
        // success
        self::STATUS__RECEIVED => self::ACTION_STATUS__SUCCESS,
        self::STATUS__DELIVERED => self::ACTION_STATUS__SUCCESS,
        self::STATUS__PARTIALLY_DELIVERED => self::ACTION_STATUS__SUCCESS,
    ];

    const STATUS_MESSAGE = [
        self::STATUS__SENT => 'Sent',
        self::STATUS__INTERNAL_ERROR => 'Internal error',
        self::STATUS__SCHEDULED => 'Schedule',
        self::STATUS__RECEIVED => 'Received',
        self::STATUS__DELIVERED => 'Delivered',
        self::STATUS__BALANCE_ERROR => 'Balance insuffisants',
        self::STATUS__PARTIALLY_DELIVERED => 'Partially Delivered',
        self::STATUS__SMS_NOT_FOUND => 'SMS not found or Deleted',
        self::STATUS__DELIVERY_ERROR => 'Delivered error',
        self::STATUS__UNDEFINED_ERROR => 'Undefined error',
        self::STATUS__INVALID_NUMBER_ERROR => 'Invalid phone number error',
        self::STATUS__RECIPIENT_BLACKLIST => 'Recipient in blacklist error',
    ];

    /** @var string */
    private $messageID;
    /** @var int */
    private $status;
    /** @var string */
    private $actionStatus;
    /** @var string */
    private $statusMessage;
    /** @var string */
    private $responseMessage;

    /**
     * Response constructor.
     *
     * @param string $requestResponse
     * @param string|null $smsID
     */
    public function __construct(string $requestResponse, ?string $smsID = null)
    {
        $parse = explode("|", $requestResponse);

        $this->status = (int)$parse[0];
        $this->statusMessage = trim((string)$parse[1]);
        $this->messageID = trim($smsID);
        if(isset($parse[2])) {
            $this->messageID = trim($parse[2]);
        }

        $this->identifyActionStatus();
        $this->responseMessage = isset(self::STATUS_MESSAGE[$this->status])
            ? self::STATUS_MESSAGE[$this->status]
            : self::STATUS_MESSAGE[self::STATUS__UNDEFINED_ERROR];
    }

    /**
     * @return string
     */
    public function getMessageID(): string
    {
        return $this->messageID;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * @return string
     */
    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    /**
     * @return string
     */
    public function getActionStatus(): string
    {
        return $this->actionStatus;
    }

    /**
     * Identify action status by Status
     */
    private function identifyActionStatus()
    {
        $status = self::ACTION_STATUS__ERROR;
        if(isset(self::ACTION_STATUS[$this->status])) {
            $status = self::ACTION_STATUS[$this->status];
        }

        $this->actionStatus = $status;
    }
}
