<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Plan;

class HomeController extends Controller {
    private Plan $planModel;

    public function __construct() {
        parent::__construct();
        $this->planModel = new Plan();
    }

    public function index(): void {
        $lang = $_GET['lang'] ?? 'en';
        if (!in_array($lang, ['en', 'bn'])) {
            $lang = 'en';
        }

        $plans = $this->planModel->getActivePlans();

        $this->render('home/index', [
            'currentLang' => $lang,
            'plans'       => $plans
        ]);
    }
}
