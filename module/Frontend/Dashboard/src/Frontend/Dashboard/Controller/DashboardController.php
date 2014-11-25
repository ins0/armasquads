<?php
namespace Frontend\Dashboard\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractFrontendController
{
    /**
     * Get Cache
     *
     * @return \Zend\Cache\Storage\Adapter\Filesystem
     */
    private function getCache()
    {
        return $this->getServiceLocator()->get('Zend\Cache\Storage\Filesystem');
    }

    private function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$sz[$factor];
    }

    private function directoryFileSize($path)
    {
        $ite=new \RecursiveDirectoryIterator($path);
        $bytestotal=0;
        foreach (new \RecursiveIteratorIterator($ite) as $filename=>$cur) {
            $filesize=$cur->getSize();
            $bytestotal+=$filesize;
        }

        return $this->human_filesize($bytestotal, 2);
    }

    public function indexAction(){

        $this->setAccess('frontend/dashboard/access');

        $key    = 'git-time-extractor-file';
        $timeStatsFilePath = ROOT_PATH . '/../data/git/stats.csv';

        $this->getCache()->getItem($key, $success);
        if (!$success || !file_exists($timeStatsFilePath)) {
            exec('git_time_extractor > ' . $timeStatsFilePath);
            $this->getCache()->setItem($key, 'true');
        }

        $stats = fopen($timeStatsFilePath, 'r');
        $x = 0;
        $changelog = [];
        while( ! feof($stats) )
        {
            $line = fgetcsv($stats, 1024, ',', '"');

            if( $x++ == 0 || count($line) <= 1 )
                continue;

            $changes = explode('---', $line[8]);
            foreach($changes as $key => $xc)
            {
                if(substr(trim($xc), 0, 1) == '!' || trim($xc) == "" || trim($xc) == " " )
                {
                    unset($changes[$key]);
                }
            }

            if( count( $changes ) <= 0 )
            {
                continue;
            }

            $changes = array_map('trim', $changes);
            $changes = array_reverse($changes);
            $changes = array_unique($changes);

            $date = new \DateTime($line[0]);
            $changelog[$date->getTimestamp()] = [
                'date' => $date,
                'changes' => $changes
            ];
        }

        ksort($changelog);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/dashboard/index.phtml');
        $viewModel->setVariable('changelog', array_reverse($changelog));

        return $viewModel;
    }

    public function donateAction()
    {
        $this->setAccess('frontend/dashboard/access');

        // collect some data
        $data = [];

        exec('git-summary', $gitSummary);
        exec('git log -1 --format=%cd', $gitLastCommit);

        $key    = 'git-time-extractor-file';
        $timeStatsFilePath = ROOT_PATH . '/../data/git/stats.csv';

        $this->getCache()->getItem($key, $success);
        if (!$success || !file_exists($timeStatsFilePath)) {
            exec('git_time_extractor > ' . $timeStatsFilePath);
            $this->getCache()->setItem($key, 'true');
        }

        if(isset($gitSummary[2]) && isset($gitSummary[4]) && isset($gitSummary[5]) ) {
            // summary
            $data['project_age'] = trim(str_replace('repo age : ', '', $gitSummary[2]));
            $data['total_commits*'] = trim(str_replace('commits  : ', '', $gitSummary[4]));
            $data['total_project_files'] = trim(str_replace('files    : ', '', $gitSummary[5]));
        } else {
            $data['project_age'] = '';
            $data['total_commits*'] = '';
            $data['total_project_files'] = '';
        }

        // changelog
        $data['last_commit'] = (isset($gitLastCommit[0]) ? $gitLastCommit[0] : '');

        $stats = fopen($timeStatsFilePath, 'r');
        $x = 0;
        $changelog = [];
        $totalTime = 0;
        while( ! feof($stats) )
        {
            $line = fgetcsv($stats, 1024, ',', '"');

            if( $x++ == 0 || count($line) <= 1 )
                continue;

            $changes = explode('---', $line[8]);
            unset($changes[0]);

            $changelog[] = [
                'date' => $line[0],
                'changes' => $changes 
            ];

            $totalTime += $line[3];
        }

        $userRepo = $this->getEntityManager()->getRepository('Auth\Entity\Benutzer');
        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $memberRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Member');

        // total user
        $data['registered_users'] = $userRepo->createQueryBuilder('c')->select('count(c.id)')->getQuery()->getSingleScalarResult();

        // total squads
        $data['total_squads'] = $squadRepo->createQueryBuilder('c')->select('count(c.id)')->getQuery()->getSingleScalarResult();

        // total squads
        $data['total_squad_members'] = $memberRepo->createQueryBuilder('c')->select('count(c.squad)')->getQuery()->getSingleScalarResult();

        // total images
        $data['total_squad_logos'] = $squadRepo->createQueryBuilder('c')->select('count(c.logo)')->where('c.logo IS NOT NULL')->andWhere("c.logo != ''")->getQuery()->getSingleScalarResult();

        // total image file size
        $data['total_squad_logos_size'] = $this->directoryFileSize(ROOT_PATH . '/uploads/logos/');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/dashboard/donate.phtml');
        $viewModel->setVariable('data', $data);
        $viewModel->setVariable('changelog', array_reverse($changelog));
        $viewModel->setVariable('total_time', $totalTime);
        return $viewModel;
    }
}
