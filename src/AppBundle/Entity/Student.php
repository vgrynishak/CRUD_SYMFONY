<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StudentRepository")
 */
class Student
{
    /**
     * @ORM\Id
     * @Serializer\Groups({"student", "school"})
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Serializer\Groups({"student", "school"})
     * @ORM\Column( name="first_name", type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2, minMessage="Имя должно быть не менее {{ limit }} символов.",
     *     max=50, maxMessage="Слишком длинное имя."
     * )
     */
    private $firstName;

    /**
     * @Serializer\Groups({"student"})
     * @ORM\Column(name="last_name", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2, minMessage="Имя должно быть не менее {{ limit }} символов.",
     *     max=50, maxMessage="Слишком длинное имя."
     * )
     */
    private $lastName;

    /**
     * @Serializer\Groups({"student", "school"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade;

    /**
     * @Serializer\Groups({"student"})
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\School", inversedBy="students", cascade={"persist"})
     */
    private $school;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Student
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return Student
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return Student
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     * @return Student
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param mixed $school
     * @return Student
     */
    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }
}