<?php

require_once(__DIR__ . '/../Database/DB.php');
require_once(__DIR__ . '/Operation.php');
require_once(__DIR__ . '/Stat.php');
require_once(__DIR__ . '/Chart.php');
require_once(__DIR__ . '/Action.php');

class Admin
{
    public static function getOperations()
    {
        return array(
            'createBranch' => new Operation('Nuova filiale', 'branches/create.php'),
            'branches' => new Operation('Filiali', 'branches/index.php'),
            'rentals' => new Operation('Noleggi', 'rentals/index.php'),
            'indexMember' => new Operation('Soci', 'members/index.php')
        );
    }

    public static function getActionsOnMembers()
    {
        return array(
            new Action('Profilo', 'members/show.php?', array('email'), Action::SELECT),
        );
    }

    public function getStats()
    {
        $db = new DB();

        $monthNames = array(
            'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno',
            'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'
        );

        $stats = array(
            'Branches' => new Stat(
                'Filiali',
                $this->queryBranchesCount($db)
            ),
            'Members' => new Stat(
                'Soci',
                $this->queryMembersCount($db)
            ),
            'Rentals' => new Stat(
                'Noleggi ' . $monthNames[idate('m') - 1],
                $this->queryMonthlyRentalsCount($db) ?? '0'
            ),
            'Revenue' => new Stat(
                'Fattuarato ' . $monthNames[idate('m') - 1],
                $this->queryMonthlyRevenue($db) ?? '0'
            ),
        );

        $db->disconnect();

        return $stats;
    }

    public function getChart()
    {
        $char = new Chart('Performance', Chart::PIE, 'performance', 'branches');

        $db = new DB();

        $performanceDataSet = $this->queryBranchesMonthlyPerformance($db);

        $totalPerformance = 0;

        if (!is_null($performanceDataSet)) {
            foreach ($performanceDataSet as $key => $value) {
                $performance = (int)$value['performance'] ?? 0;
                $totalPerformance += $performance;

                $char->add($key, $performance);
                $char->addX($key, $value['branch']);
            }
        }
        if ($totalPerformance === 0) {
            $char->setTitle('Non ci sono dati da mostrare per');
        }

        $db->disconnect();

        return $char;
    }

    public function getProfile()
    {
        return array('Admin' => 'Admin');
    }


    // QUERIES

    // STATS
    private function queryBranchesCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM filiale
        ';

        $result = $db->get($db->query($query));

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryMembersCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM socio
        ';

        $result = $db->get($db->query($query));

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryMonthlyRentalsCount($db)
    {
        $query = '
            SELECT COUNT(*) AS count
            FROM noleggiocheckin AS i
            WHERE MONTH(i.data_operazione) = MONTH(CURRENT_DATE())
            AND YEAR(i.data_operazione) = YEAR(CURRENT_DATE())
        ';

        $result = $db->get($db->query($query));

        return is_null($result) ? '#' : $result['count'];
    }

    private function queryMonthlyRevenue($db)
    {
        $query = '
            SELECT CONCAT(SUM(costo), "€") AS revenue
            FROM noleggiocheckout
            WHERE MONTH(data_operazione) = MONTH(CURRENT_DATE())
            AND YEAR(data_operazione) = YEAR(CURRENT_DATE())
        ';

        $result = $db->get($db->query($query));

        return is_null($result) ? '#' : $result['revenue'];
    }

    // CHART
    private function queryBranchesMonthlyPerformance($db)
    {
        $query = '
            SELECT f.nome AS branch , (SUM(o.costo)*100)/t.tot AS performance
            FROM filiale AS f
            LEFT JOIN noleggio AS n ON n.filiale = f.nome
            LEFT JOIN noleggiocheckout AS o ON  o.noleggio = n.codice_noleggio AND (
                MONTH(o.data_operazione) = MONTH(CURRENT_DATE()) AND
                YEAR(o.data_operazione) = YEAR(CURRENT_DATE())
            ) CROSS JOIN (
                SELECT SUM(o2.costo) AS tot
                FROM noleggiocheckout AS o2
            ) t
            GROUP BY f.nome
        ';

        $result = $db->getAll($db->query($query));

        return $result;
    }
}
