<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Firebase\JWT\JWT;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $access_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $refresh_token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;



    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(?string $access_token): self
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(?string $refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getCreatedAt() : \DateTime
    {
        return $this->created_at;

    }

    public function setCreatedAt(\DateTime $created_at){

        $this->created_at = $created_at;

        return $this;
    }

    public function generateAccessToken(){
        if($this->access_token == null){
            $this->setAccessToken(JWT::encode([
                'iss' => 'jwt_authenticator',
                'sub' => $this->getId()
            ], $_ENV['APP_SECRET']));
            return $this->getAccessToken();
        }else{
            return $this->access_token;
        }
    }
}
