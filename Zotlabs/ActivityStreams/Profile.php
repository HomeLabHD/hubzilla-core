<?php

namespace Zotlabs\ActivityStreams;

class Profile extends ASObject
{

    public $describes;

    /**
     * @return mixed
     */
    public function getDescribes()
    {
        return $this->describes;
    }

    /**
     * @param mixed $describes
     * @return Profile
     */
    public function setDescribes($describes)
    {
        $this->describes = $describes;
        return $this;
    }


}
