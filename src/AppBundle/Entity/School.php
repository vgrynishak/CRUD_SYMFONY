<?php


namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="school")
 * @ORM\Entity()
 */
class School
{
    /**
     * @ORM\Id
     * @Serializer\Groups({"school", "student"})
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Serializer\Groups({"school", "student"})
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @Serializer\Groups({"school"})
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Serializer\Groups({"school"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Student", mappedBy="school", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid
     */
    private $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return School
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return School
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return School
     */
    public function setDescriptiona($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @param mixed $students
     * @return School
     */
    public function setStudents($students)
    {
        $this->students = $students;
        return $this;
    }

    /**
     * @param Student $student
     */
    public function addStudent(Student $student)
    {
        $this->students[] = $student;
    }

    /**
     * @param Student $student
     */
    public function removeStudent(Student $student) {
       $this->students->removeElement($student);
    }

    /**
     * @Serializer\Groups({"school"})
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("studentCount")
     */
    public function GetNumberStudent(){
        return $this->students->count();
    }
}