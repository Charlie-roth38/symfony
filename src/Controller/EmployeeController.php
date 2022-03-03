<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/')]
#[IsGranted('ROLE_USER')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_DIRECTION')]
    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmployeeRepository $employeeRepository, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {
        $employee = new Employee();

        if($request->getMethod() == 'POST'){
            $employee->setPassword($request->get('employee')["plainPassword"]["first"]);
        }



        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Upload des images !

            $profilePicture = $form->get('photo')->getData();

            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();

                try {
                    $profilePicture->move(
                        $this->getParameter('profile_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $employee->setPhoto($newFilename);
                $employee->setRoles(['ROLE_'.$employee->getSector()]);
                $employee->setPassword($hasher->hashPassword($employee, $request->get('employee')["plainPassword"]["first"]));
            }

            $employeeRepository->add($employee);

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'], requirements: ['id'=> '\d+'])]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[IsGranted('ROLE_DIRECTION')]
    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $em, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);


        $password = $form->get('plainPassword')->getData();

        if(!empty($password)){
            $employee->setPassword($form->get('plainPassword')->getData());
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('photo')->getData();
            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();

                try {
                    $profilePicture->move(
                        $this->getParameter('profile_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $employee->setPhoto($newFilename);

            }

            if($password){
                $employee->setPassword($hasher->hashPassword($employee, $password));
            }
            $employee->setRoles(['ROLE_'.$employee->getSector()]);


            $em->flush();

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_DIRECTION')]
    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'], requirements: ["id"=> '\d+'])]
    public function delete(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $employeeRepository->remove($employee);
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
