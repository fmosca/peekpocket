<?php

namespace PeekPocket;

class PocketItem
{
    private $url;
    private $title;
    private $excerpt;
    private $status;
    private $createdAt;
    private $readAt;

    public function __construct(
        $url,
        $title,
        $excerpt,
        $status,
        $createdAt,
        $readAt
    )
    {
        $this->url = $url;
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->readAt = $readAt;
    }

    public static function buildFromArray($data)
    {
        $createdAt = isset($data['time_added']) 
            ? \DateTime::createFromFormat('U', $data['time_added'])
            : new \DateTime();
        return new self(
            $data['resolved_url'],
            $data['resolved_title'],
            $data['excerpt'],
            $data['status'],
            $createdAt,
            \DateTime::createFromFormat('U', $data['time_read'])
        );

    }

    
    /**
     * Get url.
     *
     * @return url.
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Get title.
     *
     * @return title.
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Get excerpt.
     *
     * @return excerpt.
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }
    
    /**
     * Get status.
     *
     * @return status.
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Get readAt.
     *
     * @return readAt.
     */
    public function getReadAt()
    {
        return $this->readAt;
    }

    
    /**
     * Get createdAt.
     *
     * @return createdAt.
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
