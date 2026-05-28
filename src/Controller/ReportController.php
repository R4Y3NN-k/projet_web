<?php



namespace App\Controller;



use App\Entity\Report;

use App\Entity\User;

use App\Form\ReportType;

use App\Repository\ReportRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;



#[Route('/report')]

class ReportController extends AbstractController

{

    #[Route('/create/{userId}', name: 'app_report_create')]

    public function create(int $userId, Request $request, EntityManagerInterface $em): Response

    {

        $currentUser = $this->getUser();

        if (!$currentUser) {

            return $this->redirectToRoute('app_login');

        }



        // Get the user to be reported

        $reportedUser = $em->getRepository(User::class)->find($userId);

        if (!$reportedUser) {

            $this->addFlash('error', 'User not found.');

            return $this->redirectToRoute('app_home');

        }



        // Prevent self-reporting

        if ($reportedUser->getId() === $currentUser->getId()) {

            $this->addFlash('error', 'You cannot report yourself.');

            return $this->redirectToRoute('app_home');

        }



        // Check if user already reported this person

        $existingReport = $em->getRepository(Report::class)->findOneBy([

            'reporter' => $currentUser,

            'reportedUser' => $reportedUser,

            'status' => 'pending'

        ]);



        if ($existingReport) {

            $this->addFlash('warning', 'You have already submitted a report for this user.');

            return $this->redirectToRoute('app_home');

        }



        $report = new Report();

        $report->setReporter($currentUser);

        $report->setReportedUser($reportedUser);



        $form = $this->createForm(ReportType::class, $report);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $report->setUpdatedAt(new \DateTime());

                $em->persist($report);

                $em->flush();



                $this->addFlash('success', 'Thank you for your report. Our team will review it shortly.');

                // Redirect back to referrer, or to appropriate dashboard
                $referrer = $request->headers->get('referer');
                if ($referrer && strpos($referrer, $request->getHost()) !== false) {
                    return $this->redirect($referrer);
                }

                // Fallback to appropriate dashboard based on user role
                if ($this->isGranted('ROLE_CLIENT')) {
                    return $this->redirectToRoute('app_client_dashboard');
                } elseif ($this->isGranted('ROLE_PROVIDER')) {
                    return $this->redirectToRoute('app_provider_dashboard');
                }

                return $this->redirectToRoute('app_home');

            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while submitting your report. Please try again.');
                return $this->redirectToRoute('app_home');
            }

        }

        // Display any form errors if form was submitted but invalid
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }



        return $this->render('report/create.html.twig', [

            'form' => $form,

            'reportedUser' => $reportedUser,

        ]);

    }

    #[Route('/my-reports', name: 'app_report_my')]
    public function myReports(EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        // Get all reports submitted by the current user
        $reports = $em->getRepository(Report::class)->findBy(
            ['reporter' => $currentUser],
            ['createdAt' => 'DESC']
        );

        return $this->render('report/my_reports.html.twig', [
            'reports' => $reports,
        ]);
    }

    #[Route('/against-me', name: 'app_report_against_me')]
    public function reportsAgainstMe(EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        // Get all reports against the current user
        $reports = $em->getRepository(Report::class)->findBy(
            ['reportedUser' => $currentUser],
            ['createdAt' => 'DESC']
        );

        return $this->render('report/reports_against_me.html.twig', [
            'reports' => $reports,
        ]);
    }

   

} 

