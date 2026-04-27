<?php

namespace App\Services\Request;

use App\Models\Request;

class RequestService
{
    protected const ALLOWED_STATUS_TRANSITIONS = [
        'Nouveau' => ['En_Cour', 'Refuser'],
        'En_Cour' => ['Terminer'],
        'Refuser' => [],
        'Terminer' => [],
    ];

    public function createRequest(array $data)
    {
        return Request::create($data);
    }

    public function getClientRequests(int $clientId)
    {
        return Request::with(['service.professional.user', 'review'])
            ->where('client_id', $clientId)
            ->where('is_canceled', false)
            ->get();
    }

    public function getClientRequestsCount(int $clientId) {
        return Request::with('service.professional.user')
            ->where('client_id', $clientId)
            ->where('is_canceled', false)
            ->count();
    }

    public function getOpenRequestsCount(): int
    {
        return Request::whereIn('status', ['Nouveau', 'En_Cour'])
            ->where('is_canceled', false)
            ->count();
    }

    public function getLatestRequestsForAdmin(int $limit = 4)
    {
        return Request::with(['service'])
            ->where('is_canceled', false)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getCompletedClientRequestsCount(int $clientId)
    {
        return Request::where('client_id', $clientId)
            ->where('status', 'Terminer')
            ->where('is_canceled', false)
            ->count();
    }

    public function getLastThreeClientRequests(int $clientId)
    {
        return Request::with(['service.professional.user', 'review'])
            ->where('client_id', $clientId)
            ->where('is_canceled', false)
            ->latest()
            ->limit(3)
            ->get();
    }

    public function getLastSixClientRequests(int $clientId)
    {
        return Request::with(['service.professional.user', 'review'])
            ->where('client_id', $clientId)
            ->where('is_canceled', false)
            ->latest()
            ->limit(6)
            ->get();
    }

    public function getProfessionalRequests(int $professionalId)
    {
        return Request::with(['client', 'service'])
            ->whereHas('service', function ($query) use ($professionalId) {
                $query->where('professional_id', $professionalId);
            })
            ->get();
    }

    public function getRequestById(int $id)
    {
        return Request::find($id);
    }

    public function getProfessionalRequestById(int $id, int $professionalId)
    {
        return Request::with(['client', 'service.category'])
            ->where('id', $id)
            ->whereHas('service', function ($query) use ($professionalId) {
                $query->where('professional_id', $professionalId);
            })
            ->first();
    }

    public function getClientRequestById(int $id, int $clientId)
    {
        return Request::where('id', $id)
            ->where('client_id', $clientId)
            ->first();
    }

    public function updateRequestStatus(Request $request, string $status)
    {
        $currentStatus = $request->status;
        $allowedTransitions = self::ALLOWED_STATUS_TRANSITIONS[$currentStatus] ?? [];

        if ($currentStatus === $status) {
            return $request->fresh();
        }

        if (!in_array($status, $allowedTransitions, true)) {
            return null;
        }

        $request->update([
            'status' => $status,
            'is_accepted' => $status === 'En_Cour' ? true : ($status === 'Refuser' ? false : $request->is_accepted),
        ]);

        return $request->fresh();
    }

    public function cancelRequest(Request $request)
    {
        $request->update([
            'is_canceled' => true,
        ]);

        return $request->fresh();
    }
}
