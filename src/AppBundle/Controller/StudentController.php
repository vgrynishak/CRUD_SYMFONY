<?php

namespace AppBundle\Controller;

use AppBundle\Entity\School;
use AppBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentController extends Controller
{

    /**
     * Add Student
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/student", name="fe_student_add", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $repSchool  = $this->getDoctrine()->getRepository(School::class);
        $student    = new Student();

        if ($request->query->has('school_id') && !($school = $repSchool->find($request->get('school_id')))) {
            return new JsonResponse([
                'errorMessage' => 'School not found'
            ], 404);
        }

        $student
            ->setFirstName($request->get('first_name'))
            ->setLastName($request->get('last_name'))
            ->setGrade($request->get('grade'))
            ->setSchool($school ?? null)
        ;

        $errors = $this->get('validator')->validate($student);

        if (count($errors)) {
            return new JsonResponse([
                'errorMessage' => 'Student invalid data'
            ], 400);
        }

        $em->persist($student);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success add student'
        ], 200);
    }

    /**
     * Update Student
     *
     * @param Request $request
     * @param Student $student
     * @return JsonResponse
     * @Route("/student/{id}", name="fe_student_update", methods={"PUT"})
     */
    public function updateAction(Request $request, Student $student)
    {
        $em         = $this->getDoctrine()->getManager();
        $repSchool  = $this->getDoctrine()->getRepository(School::class);

        if ($request->request->has('school_id') && !($school = $repSchool->find($request->get('school_id')))) {
            return new JsonResponse([
                'errorMessage' => 'School not found'
            ], 404);
        }

        $student
            ->setFirstName($request->get('first_name'))
            ->setLastName($request->get('last_name'))
            ->setGrade($request->get('grade'))
            ->setSchool($school ?? null)
        ;

        $errors = $this->get('validator')->validate($student);

        if (count($errors)) {
            return new JsonResponse([
                'errorMessage' => 'Student invalid data'
            ], 400);
        }

        $em->persist($student);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success update student'
        ], 200);
    }

    /**
     * Delete Student
     *
     * @param Student $student
     * @return JsonResponse
     * @Route("/student/{id}", name="fe_student_delete", methods={"DELETE"})
     */
    public function deleteAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($student);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success delete student'
        ], 200);
    }

    /**
     * Show all student
     *
     * @Route("/student", name="fe_student_show_all", methods={"GET"})
     */
    public function getAllSAction()
    {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();

        return $this->get('api.entity_manager')->serializeToResponse($students, ['student']);
    }

    /**
     * Show one student
     *
     * @param Student $student
     * @return mixed
     * @Route("/student/{id}", name="fe_student_show_one", methods={"GET"})
     */
    public function getOneAction(Student $student)
    {
        return $this->get('api.entity_manager')->serializeToResponse($student, ['student']);
    }
}
