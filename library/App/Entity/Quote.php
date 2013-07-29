<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Quote")
 * @ORM\Table(name="quote")
 */
class Quote
{
    /**
     * @ORM\Id @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    private $_id;

    /** @ORM\Column(type="string", name="wording") */
    private $_wording;

    /** @ORM\Column(type="string", name="author") */
    private $_author;

    public function getId()
    {
        return $this->_id;
    }

    public function getWording()
    {
        return $this->_wording;
    }

    public function setWording($wording)
    {
        $this->_wording = $wording;
        return $this;
    }

    public function getAuthor()
    {
        return $this->_author;
    }

    public function setAuthor($author)
    {
        $this->_author = $author;
        return $this;
    }

    public function __toString()
    {
        return $this->getWording();
    }

}
