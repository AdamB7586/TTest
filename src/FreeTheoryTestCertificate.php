<?php
namespace TheoryTest\Car;

use DBAL\Database;
use Smarty;

class FreeTheoryTestCertificate extends TheoryTestCertificate{
    protected $testType = 'FREE';

    public function __construct(Database $db, Smarty $layout, $user, $testID) {
        parent::__construct($db, $layout, $user, $testID);
        $this->theory = new FreeTheoryTest(self::$db, $layout, self::$user);
    }
    
    public function generateCertificate(){
        $this->theory->getQuestions();
        $this->theory->getTestResults();
        $this->theory->getUserAnswers();
        $userInfo = self::$user->getUserInfo();
        if(!$this->theory->testresults['status']){redirect('/');}
        
        $this->PDFInfo();
        if($this->theory->testresults['status'] == 'pass'){
            $this->pdf->AddPage();
            $this->pdf->Image('images/cert.jpg', 0, 0, 210, 297);
            $this->pdf->SetFont('Arial','B', 24);
            $this->pdf->Ln(30);
            $this->pdf->Cell(190, 15, strip_tags($this->theory->getTestName()), 0, 0, 'C');
            $this->pdf->Ln(30);
            $this->pdf->SetFont('Arial','B', 18);
            $this->pdf->Cell(10, 14, '', 0); $this->pdf->Cell(28, 14, 'Candidate', 0);
            $this->pdf->Ln(12);
            $this->certLine('Name:', $userInfo['first_name'].' '.$userInfo['last_name']);
            $this->pdf->Ln(10);
            $this->pdf->SetFont('Arial','B', 18);
            $this->pdf->Cell(10, 10, '', 0); $this->pdf->Cell(14, 10, 'Test', 0);
            $this->pdf->Ln(12);
            $this->certLine('Test ID:', strtoupper($this->testType).$this->theory->testresults['id']);
            $this->certLine('Test Name:', strip_tags($this->theory->getTestName()));
            $this->certLine('Completion Date/Time:', date('d/m/Y g:i A', strtotime($this->theory->testresults['complete'])));
            $this->certLine('Score:', $this->theory->testresults['correct'].' / '.$this->theory->numQuestions());
            $this->certLine('Passmark:', $this->theory->passmark.' / '.$this->theory->numQuestions());
            $this->pdf->SetFont('Arial','B', 14);
            $this->pdf->Cell(10, 10, '', 0); $this->pdf->Cell(72, 10, 'Status:', 0);
            $this->pdf->SetTextColor(0,151,0);
            $this->pdf->Cell(92, 10, 'Passed', 0);
            $this->pdf->SetTextColor(0,0,0);
        }
        
        $this->pdf->AddPage('P', 'A4');
        $this->pdf->SetFont('Arial','B', 8);
        $this->pdf->BasicTable(
            array('Test Name', 'Unique Test ID', 'Taken on Date/Time'),
            array(array(strip_tags($this->theory->getTestName()), $this->theory->testresults['id'], date('d/m/Y g:i A', strtotime($this->theory->testresults['complete'])))),
            array(69,57,64)
        );
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial','B', 16);
        $this->pdf->Cell(92, 10, 'Theory Test Report', 0);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','', 12);
        if($this->theory->testresults['status'] == 'pass'){
            $this->pdf->Cell(184, 10, "Congratulations ".$userInfo['first_name']); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You have passed this test with ".$this->theory->testresults['percent']['correct']."%."); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You answered ".$this->theory->testresults['correct']." out of ".$this->theory->testresults['numquestions']." questions correctly");
        }
        else{
            $this->pdf->Cell(184, 10, "Sorry ".$userInfo['first_name'].", but you have not passed this time."); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You answered ".$this->theory->testresults['correct'].' out of '.$this->theory->testresults['numquestions']." questions correctly, the pass rate is ".$this->theory->passmark." out of ".$this->theory->testresults['numquestions']);
        }
        $this->pdf->Ln(12);
        $this->infoLine('Test:', strip_tags($this->theory->getTestName()));
        $this->infoLine('Date:', date('d/m/Y', strtotime($this->theory->testresults['complete'])));
        $this->pdf->Ln(6);
        $this->infoLine('Status:', strip_tags($this->theory->testStatus()));
        $this->infoLine('Questions:', $this->theory->testresults['numquestions']);
        $this->pdf->Ln(6);
        $this->infoLine('Time Taken:', $this->theory->getTime());
        $this->pdf->Ln(16);
        $this->pdf->SetFont('Arial','B', 8);

        $this->overallResults();
    }
}