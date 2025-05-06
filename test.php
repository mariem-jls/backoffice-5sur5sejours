<!-- $datePrev = date('Y-m', strtotime('first day of last month'));
    $monthPrev = date('m', strtotime('first day of last month'));
    $yearPrev = date('Y', strtotime('first day of last month'));
    if ($monthPrev == "01" || $monthPrev == "03" || $monthPrev == "05" || $monthPrev == "07" || $monthPrev == "08" || $monthPrev == "10" || $monthPrev == "12") {
        $fdayPrev = "01";
        $ldayPrev = "31";
    }
    if ($monthPrev == "04" || $monthPrev == "06" || $monthPrev == "09" || $monthPrev == "11") {
        $fdayPrev = "01";
        $ldayPrev = "30";
    }
    if ($monthPrev == "02") {
        $intyear = intval($yearPrev);
        $fdayPrev = "01";
        if ($intyear % 4 === 0) {
            $ldayPrev = "29";
        } else {
            $ldayPrev = "28";
        }
    }
    $date = date("Y-m");
    $year = date("Y");
    $month = date("m");
    if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") {
        $fday = "01";
        $lday = "31";
    }
    if ($month == "04" || $month == "06" || $month == "09" || $month == "11") {
        $fday = "01";
        $lday = "30";
    }
    if ($month == "02") {
        $intyear = intval($year);
        $fday = "01";
        if ($intyear % 4 === 0) {
            $lday = "29";
        } else {
            $lday = "28";
        }
    }
    $datedebutPrev = $datePrev . "-" . $fdayPrev;
    $datefinPrev = $datePrev . "-" . $ldayPrev;
    $datedebutNow = $date . "-" . $fday;
    $datefinNow = $date . "-" . $lday;
    
    $datedebutPrev = \DateTime::createFromFormat('Y-m-d', $datedebutPrev)->setTime(0, 0);
    $datefinPrev = \DateTime::createFromFormat('Y-m-d', $datefinPrev)->setTime(0, 0);
    $ComandeFindPrev = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutPrev, $datefinPrev);
    $datedebutNow = \DateTime::createFromFormat('Y-m-d', $datedebutNow)->setTime(0, 0);
    $datefinNow = \DateTime::createFromFormat('Y-m-d', $datefinNow)->setTime(0, 0);
    $ComandeFindNow = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);
    $ComandeFind = array_merge($ComandeFindPrev, $ComandeFindNow);
    $ComandeFind = $this->em->getRepository(ComandeProduit::class)->ProduitCommandeAvecCmdPayExipBetween($datedebutNow, $datefinNow);

    $dateDebut = new \DateTime('first day of this month');
        //var_dump($dateDebut);
        //$dateDebut=$firstDay->format('Y-m-d');
        $dateFin = new \DateTime('last day of this month');
        //  var_dump($dateFin);
        //$dateFin=$lastDay->format('Y-m-d');
        if ($request->get('dateDebut')) {
            $dateDebut = new Datetime($request->get('dateDebut'));
        }
        if ($request->get('dateFin')) {
            $dateFin = new Datetime($request->get('dateFin'));
        }
        $idClient = null;
        if ($request->get('idClient')) {
            $idClient = $request->get('idClient');
        }
        $statut = null;
        if ($request->get('statut')) {
            $statut = $request->get('statut');
        }
        // avoir les paniers créés dans ce mois 
        $listePanier = $this->em->getRepository(Panier::class)->getListePaniersBetween($dateDebut, $dateFin, "35");

    $alletablissement = $this->em->getRepository(Etablisment::class)->findAll();
    $listeSejour = $this->em->getRepository(Sejour::class)->findSejourListForSearch();
    $listeCommande = $this->em->getRepository(Commande::class)->findCommandeListForSearchEspaceComptable();
    $listeTypeProduit = $this->em->getRepository(TypeProduitConditionnement::class)->findAll();
    $tab = $this->em->getRepository(Commande::class)->findAppelFacture();
    array_unique($tab, SORT_REGULAR);
    $array = [];
    foreach ($tab as $appelFact) {
        $array[$appelFact['id']][$appelFact['periode']]['numAppelFacture'] = $appelFact['numfacture'];
    }
    $dateNow = date('Y/m');
    $currentDate = date('d/m/Y');
    $dateToday = \DateTime::createFromFormat('d/m/Y',  $currentDate)->setTime(0, 0);
    $datedebutPrevString = $datedebutPrev->format('Y-m-d'); // Convert to string for the query
    $dateNowString = \DateTime::createFromFormat('Y/m', $dateNow)->format('Y-m-d'); // Convert to DateTime and then to string
    //$listeCMDAbondonnees = $this->em->getRepository(Commande::class)->listeCommandesNonPayeeBetween($dateNowString, $datedebutPrevString);
    
    $datedebut = date('01/m/Y');
    $datefin = date('t/m/Y');
    $datedebut = \DateTime::createFromFormat('d/m/Y', $datedebut)->setTime(0, 0);
    $datefin = \DateTime::createFromFormat('d/m/Y', $datefin)->setTime(0, 0);
    $ListDesCommande = $this->em->getRepository(Commande::class)->ListDesCommande($datedebut, $datefin);
    $ListDesCommandeToday = $this->em->getRepository(Commande::class)->ListDesCommandeToday($dateToday);
    $day = date('w');
    $week_start = date('d/m/Y', strtotime('-' . $day . ' days'));
    $week_end = date('d/m/Y', strtotime('+' . (6 - $day) . ' days'));
    $week_start = \DateTime::createFromFormat('d/m/Y',   $week_start);
    $week_end  = \DateTime::createFromFormat('d/m/Y',    $week_end);
    $ListDesCommandeWeek = $this->em->getRepository(Commande::class)->ListDesCommande($week_start,   $week_end);

    $totalMontanthtWeek = array_reduce($ListDesCommandeWeek, function($carry, $commande) {
        return $carry + $commande->getMontantht();
    }, 0);

    $totalMontanthtMonth = array_reduce($ListDesCommande, function($carry, $commande) {
        return $carry + $commande->getMontantht();
    }, 0);

//   dd($ListDesCommande);
    return $this->render('commandes/ListedesCommandes.html.twig', [
        'tabNumFacture' => $array,
        'dateNow' => $dateNow,
        'listeTypeProduit' => $listeTypeProduit,
        'listeSejour' => $listeSejour,
        'listeCommande' => $ListDesCommande,
        'alletablissement' => $alletablissement,
        'ComandeFind' => $ComandeFind,
        "carts" => $carts,
        "ListDesCommandeToday" => $ListDesCommandeToday,
        "ListDesCommandesWeek" => $ListDesCommandeWeek,
        // 'caProduits' => $totalEnv,
        'nbrDesCommande' => sizeof($ListDesCommande),
        'totalMontanthtWeek' => $totalMontanthtWeek,
        'totalMontanthtMonth' => $totalMontanthtMonth,
        // 'caWeek' => $caWeek,
        'nbrDesCmdWeek' => sizeof($ListDesCommandeWeek)
    ]); -->