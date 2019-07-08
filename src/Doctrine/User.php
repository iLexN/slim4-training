<?php

declare(strict_types=1);

namespace App\Doctrine;

/**
 * @Entity(repositoryClass="UserRepository")
 * @Table(name="users")
 **/
final class User
{

    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $count;

    public function __construct(
        int $id,
        string $email,
        string $name,
        int $count
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->count = $count;
    }

    public function nameAndEmail(): string
    {
        return $this->name . '(' . $this->email . ')';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


}