<?php

namespace SMSMode\Request;

/**
 * Class Message
 * @package SMSMode\Request
 * @author Mykhailo YATSYHSYN <myyat@mirko.in.ua>
 * @copyright Mirko 2021 <https://mirko.in.ua>
 */
class Message
{
    const CLASSE_MSG__PRO = 2;
    const CLASSE_MSG__WITH_FEEDBACK = 4;

    private const SCHEDULE__DATE_FORMAT = 'dmY-H:i';

    /** @var string */
    private $message;
    /** @var array  */
    private $phoneNumbers = [];
    /** @var int */
    private $class = self::CLASSE_MSG__PRO;
    /** @var string */
    private $sender;
    /** @var null|\DateTime */
    private $scheduleTime;

    /**
     * @param string $message
     * @return Message
     */
    public static function create(string $message): Message
    {
        $object = new self();
        $object->setMessage($message);

        return $object;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function setMessage(string $message): Message
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $phoneNumber
     *
     * @return $this
     */
    public function addPhoneNumber(string $phoneNumber): Message
    {
        $this->phoneNumbers[$phoneNumber] = $phoneNumber;

        return $this;
    }

    /**
     * @return array
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param array $phoneNumbers
     * @return Message
     */
    public function setPhoneNumbers(array $phoneNumbers): Message
    {
        foreach ($phoneNumbers as $phoneNumber) {
            $this->addPhoneNumber($phoneNumber);
        }

        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function removePhoneNumber(string $phoneNumber): Message
    {
        unset($this->phoneNumbers[$phoneNumber]);

        return $this;
    }

    /**
     * @return int
     */
    public function getClass(): int
    {
        return $this->class;
    }

    /**
     * @param int $class
     * @return Message
     */
    public function setClass(int $class): Message
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getSender(): ?string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     * @return Message
     */
    public function setSender(string $sender): Message
    {
        $this->sender = $sender;

        return $this;
    }

    public function hasSender(): bool
    {
        return !empty($this->getSender());
    }

    /**
     * @return bool
     */
    public function isSchedule(): bool
    {
        return !empty($this->getScheduleTime());
    }

    /**
     * @return \DateTime|null
     */
    public function getScheduleTime(): ?\DateTime
    {
        return $this->scheduleTime;
    }

    /**
     * @param \DateTime|null $scheduleTime
     * @return Message
     */
    public function setScheduleTime(?\DateTime $scheduleTime): Message
    {
        $this->scheduleTime = $scheduleTime;

        return $this;
    }

    /**
     * @return array
     */
    public function buildRequestBody(): array
    {
        $body = [
            "message" => $this->getMessage(),
            'numero' => implode(",", $this->getPhoneNumbers()),
            "classe_msg" => $this->getClass(),
            "emetteur" => $this->getSender(),
        ];

        if($this->isSchedule()) {
           $body['date_envoi'] = $this->getScheduleTime()->format(self::SCHEDULE__DATE_FORMAT);
        }

        return $body;
    }
}
