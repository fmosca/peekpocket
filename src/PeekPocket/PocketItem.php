<?php

namespace PeekPocket;

class PocketItem
{
    private $url;
    private $title;
    private $excerpt;
    private $status;
    private $readAt;

    public function __construct(
        $url,
        $title,
        $excerpt,
        $status,
        $readAt
    )
    {
        $this->url = $url;
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->status = $status;
        $this->readAt = $readAt;
    }

    public static function buildFromArray($data)
    {
        return new self(
            $data['resolved_url'],
            $data['resolved_title'],
            $data['excerpt'],
            $data['status'],
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
}
