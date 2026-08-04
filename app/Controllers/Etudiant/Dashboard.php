<?php

namespace App\Controllers\Etudiant;

use App\Controllers\BaseController;
use App\Models\InscriptionModel;
use App\Models\SessionModel;
use App\Models\CompteModel;

class Dashboard extends BaseController
{
    private InscriptionModel $inscriptionModel;
    private SessionModel     $sessionModel;
    private CompteModel      $compteModel;
    public function __construct()
    {
        $this->inscriptionModel = new InscriptionModel();
        $this->sessionModel     = new SessionModel();
        $this->compteModel      = new CompteModel();
    }

    /**
     * Affiche le tableau de bord de l'étudiant avec un résumé de ses inscriptions.
     */
    public function index(): string
    {
        $id_compte    = session()->get('id_compte');
        $inscriptions = $this->inscriptionModel->getByCompte($id_compte);

        return view('etudiant/dashboard', [
            'title'        => 'Dashboard',
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * Affiche la liste complète des inscriptions de l'étudiant connecté.
     */
    public function mesInscriptions(): string
    {
        $id_compte = session()->get('id_compte');

        return view('etudiant/mes_inscriptions', [
            'title'        => 'Mes inscriptions',
            'inscriptions' => $this->inscriptionModel->getByCompte($id_compte),
        ]);
    }

    /**
     * Traite l'envoi d'un paiement IBAN pour une inscription.
     * Valide le format belge (BE + 14 chiffres) et le checksum mod-97
     * avant d'enregistrer la date d'envoi.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function envoyerPaiement(int $id_compte, int $id_session)
    {

        $this->inscriptionModel->updateByKeys($id_compte, $id_session, [
            'date_envoi_paiement' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Votre paiement a été envoyé, en attente de validation.');
        return redirect()->to(site_url('etudiant/mes-inscriptions'));
    }

    /**
     * Affiche le catalogue des sessions ouvertes auxquelles l'étudiant peut s'inscrire.
     */
    public function catalogue(): string
    {
        $id_compte = (int) session()->get('id_compte');

        return view('etudiant/catalogue', [
            'title'    => 'Catalogue des formations',
            'sessions' => $this->sessionModel->getSessionsOuvertes($id_compte),
        ]);
    }

    /**
     * Inscrit l'étudiant à une session.
     * Vérifie l'absence de doublon et la disponibilité des places avant d'insérer.
     *
     * @param int $id_session
     */
    public function inscrire(int $id_session)
    {
        $id_compte = (int) session()->get('id_compte');
        $session   = $this->sessionModel->find($id_session);

        if ($session['statut'] !== 'Ouverte') {
            session()->setFlashdata('error', 'Cette session n\'est plus disponible à l\'inscription.');
            return redirect()->to(site_url('etudiant/catalogue'));
        }

        if ($this->inscriptionModel->isAlreadyInscrit($id_compte, $id_session)) {
            session()->setFlashdata('error', 'Vous êtes déjà inscrit à cette session.');
            return redirect()->to(site_url('etudiant/catalogue'));
        }

        if ($this->inscriptionModel->countInscriptions($id_session) >= $session['place_max']) {
            session()->setFlashdata('error', 'Il n\'y a plus de places disponibles.');
            return redirect()->to(site_url('etudiant/catalogue'));
        }

        $this->inscriptionModel->insert([
            'id_compte'        => $id_compte,
            'id_session'       => $id_session,
            'date_inscription' => date('Y-m-d H:i:s'),
            'etat_parcours'    => 'Inscrit',
        ]);

        session()->setFlashdata('success', 'Inscription réussie !');
        return redirect()->to(site_url('etudiant/mes-inscriptions'));
    }


    /**
     * Affiche la page de profil de l'étudiant connecté.
     */
    public function profil(): string
    {
        $id_compte = session()->get('id_compte');
        $compte    = $this->compteModel->find($id_compte);

        return view('etudiant/profil', [
            'title'  => 'Mon profil',
            'compte' => $compte,
        ]);
    }

    /**
     * Traite la modification des données du profil de l'étudiant.
     */
    public function modifierProfil()
    {
        $id_compte = session()->get('id_compte');

        $this->compteModel->update($id_compte, [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'adresse'        => $this->request->getPost('adresse'),
            'code_postal'    => $this->request->getPost('code_postal'),
            'date_naissance' => $this->request->getPost('date_naissance'),
        ]);

        session()->setFlashdata('success', 'Profil mis à jour avec succès.');
        return redirect()->to(site_url('etudiant/profil'));
    }
}
