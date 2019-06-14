<?php

namespace AppBundle\Controller;


use AppBundle\Entity\School;
use AppBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends Controller
{
    /**
     * Add School
     *
     * @param Request $request
     * @return JsonResponse
     * @Route("/school", name="fe_school_add", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $em        = $this->getDoctrine()->getManager();
        $school    = new School();

        if ($request->request->get('students')) {
            foreach ($request->request->get('students', []) as $student) {
                $studentEntity = new Student();

                 $studentEntity
                     ->setFirstName(isset($student['first_name']) ? $student['first_name'] : null)
                     ->setLastName(isset($student['last_name']) ? $student['last_name'] : null)
                     ->setGrade(isset($student['grade']) ? $student['grade'] : null)
                     ->setSchool($school)
                ;

                if ($school->getStudents()->contains($studentEntity) === false) {
                    $school->addStudent($studentEntity);
                }
                $em->persist($studentEntity);
            }
        }

        $school
            ->setTitle($request->get('title'))
            ->setDescriptiona($request->get('description'))
        ;

        /**
         * Можна було написати сервіс який виводить безпосередньо поле і які помилки в ньому.
         *
         * $errors = $violationLists->getIterator();
         *
         * foreach($errors as $error) {
         *      $result[$error->getPropertyPath()] = $error->getMessage();
         * }
         *
         * return $result;
         */

        $errors = $this->get('validator')->validate($school);

        if (count($errors)) {
            return new JsonResponse([
                'errorMessage' => 'School invalid data'
            ], 400);
        }
        $em->persist($school);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success add school'
        ], 200);
    }

    /**
     * Update School
     *
     * @param Request $request
     * @param School $school
     * @return JsonResponse
     * @Route("/school/{id}", name="fe_school_update", methods={"PUT"})
     */
    public function updateAction(Request $request, School $school)
    {
        $em        = $this->getDoctrine()->getManager();

        if ($request->request->get('students')) {
            foreach ($request->request->get('students', []) as $student) {
                $studentEntity = new Student();

                $studentEntity
                    ->setFirstName(isset($student['first_name']) ? $student['first_name'] : null)
                    ->setLastName(isset($student['last_name']) ? $student['last_name'] : null)
                    ->setGrade(isset($student['grade']) ? $student['grade'] : null)
                    ->setSchool($school)
                ;

                if ($school->getStudents()->contains($studentEntity) === false) {
                    $school->addStudent($studentEntity);
                }
                $em->persist($studentEntity);
            }
        }

        $school
            ->setTitle($request->get('title'))
            ->setDescriptiona($request->get('description'))
        ;

        $errors = $this->get('validator')->validate($school);

        if (count($errors)) {
            return new JsonResponse([
                'errorMessage' => 'School invalid data'
            ], 400);
        }

        $em->persist($school);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success update school'
        ], 200);
    }

    /**
     * Delete school
     *
     * @param School $school
     * @return JsonResponse
     * @Route("/school/{id}", name="fe_school_delete", methods={"DELETE"})
     */
    public function deleteAction(School $school)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($school);
        $em->flush();

        return new JsonResponse([
            'message' => 'Success delete school'
        ], 200);
    }

    /**
     * Show all school
     *
     * @Route("/school", name="fe_school_show_all", methods={"GET"})
     */
    public function getAllAction()
    {
        $cache = $this->container->get('cache.in_file');

        return $cache->find('schools', function() {
            $schools = $this->getDoctrine()->getRepository(School::class)->findAll();

            return $this->get('api.entity_manager')->serializeToResponse($schools, ['school']);
        }, '1 hour');
    }

    /**
     * Show one school
     *
     * @param School $school
     * @return mixed|null
     * @Route("/school/{id}", name="fe_school_show_one", methods={"GET"})
     */
    public function getOneAction(School $school)
    {
        $cache = $this->container->get('cache.in_file');

        return $cache->find('school_id_'.$school->getId(), function() use ($school){
            return $this->get('api.entity_manager')->serializeToResponse($school, ['school']);
        }, '1 hour');
    }
}