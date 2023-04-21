<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Repository\BlogRepository;
use App\SzamlaAgent\SzamlaAgentAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\SzamlaAgent\Response\SzamlaAgentResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/receipt", name="receipt_")
 */
class ReceiptController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/download/{id}", name="download")
     */
    public function download(UrlGeneratorInterface $urlGenerator, int $id, ParameterBagInterface $params)
    {
        try {
            $agentApiKey = null;
            $receipt = $this->entityManager->getRepository(Receipt::class)->findOneById($id, $this->getUser());
            if ($receipt) {
                foreach (json_decode($params->get('company_data')) as $company) {
                    if ($company->name == $receipt->getTransaction()->getPaymentItem()->getCompanyName()) {
                        $agentApiKey = $company->agent_api_key;
                    }
                }
                $agent = SzamlaAgentAPI::create($agentApiKey);
                $agent->setResponseType(SzamlaAgentResponse::RESULT_AS_XML);
                $result = $agent->getInvoicePdf($receipt->getIdentifier());

                if ($result->isSuccess()) {
                    $result->downloadPdf();
                }
            } else {
                throw new NotFoundHttpException();
            }
        } catch (\Exception $e) {
            $this->addFlash('eroor', 'Hiba a letöltés közben.' . $e->getMessage());
        }
        return new RedirectResponse($urlGenerator->generate('portal_page_profile_form', ['id' => $this->getUser()->getId()]));
    }
}
