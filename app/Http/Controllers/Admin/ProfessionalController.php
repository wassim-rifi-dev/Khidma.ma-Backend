<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\UpdateProfessionalVerifyRequest;
use App\Mail\Common\ActionNotificationMail;
use App\Services\Professional\ProfessionalService;
use Illuminate\Support\Facades\Mail;

class ProfessionalController extends Controller
{
    public function index(ProfessionalService $professionalService)
    {
        $professionals = $professionalService->getAllProfessionals();

        return response()->json([
            'success' => true,
            'data' => [
                'professionals' => $professionals,
                'total' => $professionals->count(),
            ],
            'message' => 'Professionals retrieved successfully'
        ], 200);
    }

    public function verifiedCount(ProfessionalService $professionalService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'active_professionals' => $professionalService->getVerifiedProfessionalsCount(),
            ],
            'message' => 'Number of active professionals'
        ], 200);
    }

    public function updateVerification(int $id, UpdateProfessionalVerifyRequest $request, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalById($id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional not found'
            ], 404);
        }

        $updatedProfessional = $professionalService->updateProfessionalVerification(
            $professional,
            (bool) $request->validated()['is_verified']
        );

        $professionalUser = $updatedProfessional->user;

        if ($professionalUser?->email) {
            $isVerified = (bool) $updatedProfessional->is_verified;

            Mail::to($professionalUser->email)->send(new ActionNotificationMail(
                $isVerified ? 'Votre profil professionnel a ete valide' : 'Mise a jour de votre verification professionnelle',
                $isVerified ? 'Profil valide' : 'Verification mise a jour',
                'Bonjour ' . $professionalUser->name . ',',
                $isVerified
                    ? 'Votre compte professionnel est maintenant verifie. Vous pouvez continuer a publier et gerer vos services.'
                    : 'Le statut de verification de votre compte professionnel a ete mis a jour. Consultez votre espace pour voir les changements.',
                [
                    'Categorie' => $updatedProfessional->category?->name ?? 'N/A',
                    'Statut' => $isVerified ? 'Verifie' : 'Non verifie',
                ],
                'Ouvrir mon espace',
                url('/'),
                'Merci de garder votre profil complet et a jour.'
            ));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'professional' => $updatedProfessional,
            ],
            'message' => 'Professional verification updated successfully'
        ], 200);
    }
}
