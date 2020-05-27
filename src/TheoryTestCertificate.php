<?php
namespace TheoryTest\Car;

use TheoryTest\Car\Essential\CertificateInterface;
use FPDF;

/**
 * @codeCoverageIgnore
 */
class TheoryTestCertificate implements CertificateInterface{
    protected $user;
    protected $pdf;
    protected $theory;
    
    protected $certImage = 'images/cert.jpg';
    protected $questions;
    protected $testType = 'CAR';
    
    public $certUsername;

    /**
     * Constructor
     * @param object $user This should be an instance of the User object
     * @param object $theoryTest This should be an instance of the Theory Test object
     * @param int|false $alias If you are viewing a certificate for someone other than the person logged in set this to the users ID
     */
    public function __construct($user, $theoryTest, $alias = false) {
        $this->user = $user;
        $this->theory = $theoryTest;
        $this->pdf = new FPDF_Protection();
        if(method_exists($this->user, 'getFirstname') && method_exists($this->user, 'getLastname')){$this->certUsername = $this->user->getFirstname($alias).' '.$this->user->getLastname($alias);}
        elseif(method_exists($this->user, 'getUsername')){$this->certUsername = $this->user->getUsername($alias);}
    }
    
    /**
     * Sets the path to the certificate background image
     * @param string $path
     */
    public function setCertificateImage($path){
        $this->certImage = $path;
        return $this;
    }
    
    /**
     * Returns the certificate image path
     * @return string
     */
    public function getCertificateImage(){
        return $this->certImage;
    }
    
    /**
     * Sets the PDF headers
     */
    public function PDFInfo(){
        $this->pdf->SetTitle('LDC Theory Test');
        $this->pdf->SetSubject('LDC Theory Test Results');
        $this->pdf->SetAuthor('Teaching Driving Ltd [www.learnerdriving.com]');
        $this->pdf->SetCreator('Teaching Driving Ltd');
    }
    
    /**
     * Creates a new row in the PDF 
     * @param mixed $text This should be the bold info in the first half
     * @param mixed $text2 This should be the info in the second half of the page
     */
    protected function certLine($text, $text2){
        $this->pdf->SetFont('Arial','B', 14);
        $this->pdf->Cell(10, 10, '', 0); $this->pdf->Cell(72, 10, $text, 0);
        $this->pdf->SetFont('Arial','', 14);
        $this->pdf->Cell(92, 10,  $text2, 0);
        $this->pdf->Ln(8);
    }
    
    /**
     * Creates a new row in the PDF (without line break after)
     * @param mixed $text This should be the bold info in the first half
     * @param mixed $text2 This should be the info in the second half of the page
     */
    protected function infoLine($text, $text2){
        $this->pdf->SetFont('Arial','B', 12);
        $this->pdf->Cell(46, 10, $text);
        $this->pdf->SetFont('Arial','', 12);
        $this->pdf->Cell(46, 10, $text2);
    }
    
    /**
     * Create the certificate page base on if they have passed of failed
     */
    public function generateCertificate(){
        $this->theory->getQuestions();
        $this->theory->getTestResults();
        $this->theory->getUserAnswers();
        if(!$this->theory->testresults['status']){redirect('/');}
        
        $this->PDFInfo();
        if($this->theory->testresults['status'] == 'pass'){
            $this->pdf->AddPage();
            $this->pdf->Image($this->getCertificateImage(), 0, 0, 210, 297);
            $this->pdf->SetFont('Arial','B', 24);
            $this->pdf->Ln(30);
            $this->pdf->Cell(190, 15, strip_tags($this->theory->getTestName()), 0, 0, 'C');
            $this->pdf->Ln(30);
            $this->pdf->SetFont('Arial','B', 18);
            $this->pdf->Cell(10, 14, '', 0); $this->pdf->Cell(28, 14, 'Candidate', 0);
            $this->pdf->Ln(12);
            $this->certLine('Name:', $this->certUsername);
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
        $this->pdf->basicTable(
            ['Name', 'Test Name', 'Unique Test ID', 'Taken on Date/Time'],
            [array($this->certUsername, strip_tags($this->theory->getTestName()), $this->theory->testresults['id'], date('d/m/Y g:i A', strtotime($this->theory->testresults['complete'])))],
            [52,52,39,47]
        );
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial','B', 16);
        $this->pdf->Cell(92, 10, 'Theory Test Report', 0);
        $this->pdf->Ln(8);
        $this->pdf->SetFont('Arial','', 12);
        if($this->theory->testresults['status'] == 'pass'){
            $this->pdf->Cell(184, 10, "Congratulations ".trim($this->certUsername)); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You have passed this test with ".$this->theory->testresults['percent']['correct']."%."); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You answered ".$this->theory->testresults['correct']." out of ".$this->theory->testresults['numquestions']." questions correctly");
        }
        else{
            $this->pdf->Cell(184, 10, "Sorry ".trim($this->certUsername).", but you have not passed this time."); $this->pdf->Ln(4);
            $this->pdf->Cell(184, 10, "You answered ".$this->theory->testresults['correct'].' out of '.$this->theory->testresults['numquestions']." questions correctly, the pass rate is ".$this->theory->passmark." out of ".$this->theory->testresults['numquestions']);
        }
        $this->pdf->Ln(12);
        $this->infoLine('Test:', strip_tags($this->theory->getTestName()));
        $this->infoLine('Date:', date('d/m/Y', strtotime($this->theory->testresults['complete'])));
        $this->pdf->Ln(6);
        $this->infoLine('Status:', ($this->theory->testresults['status'] == 'pass' ? ' Passed' : 'Failed'));
        $this->infoLine('Questions:', $this->theory->testresults['numquestions']);
        $this->pdf->Ln(6);
        $this->infoLine('Candidate:', trim($this->certUsername));
        $this->infoLine('Time Taken:', $this->theory->getTime());
        $this->pdf->Ln(16);
        $this->pdf->SetFont('Arial','B', 8);

        $this->overallResults();
    }
    
    /**
     * Build the results table
     */
    protected function overallResults(){
        $header = ['Group', 'Topics in group', 'Correct', 'Incorrect', 'Total', 'Percentage', 'Status'];
        $groupdata = [];
        $totalcorrect = 0;
        $totalincorrect = 0;
        $totalq = 0;
        foreach($this->theory->getCategories() as $data){
            $correct = isset($this->theory->testresults['dvsa'][$data['section']]['correct']) ? (int)$this->theory->testresults['dvsa'][$data['section']]['correct'] : 0;
            $incorrect = isset($this->theory->testresults['dvsa'][$data['section']]['incorrect']) ? (int)$this->theory->testresults['dvsa'][$data['section']]['incorrect'] : 0;
            $total = $correct + $incorrect;
            $groupdata[] = [$data['section'], substr($data['name'], 0, 53), $correct, $incorrect, $total, number_format(intval(($correct / $total) * 100), 0).'%', ''];
            
            $totalcorrect = $totalcorrect + $correct;
            $totalincorrect = $totalincorrect + $incorrect;
            $totalq = $totalq + $total;
        }
        $widths = [14,78,19,19,19,20,21];
        $this->pdf->basicTable($header, $groupdata, $widths, 6, true);
        $first = true;
        $grouppercent = round(($totalcorrect / $totalq) * 100);
        
        if($this->theory->testresults['status'] == 'pass'){$status = 'Passed';}
        else{$status = 'Failed';}
        
        $overall = ['', 'Overall Status', $totalcorrect, $totalincorrect, $totalq, $grouppercent.'%', $status];
        $this->pdf->SetFont('Arial','B', 9);
        foreach($widths as $col){
            if($first === true){$first = false; $currentvalue = current($overall);}else{$currentvalue = next($overall);}
            $this->pdf->Cell($col, 6, $currentvalue, 1, 0, 'C');
        }
        
        $this->pdf->AddPage('P', 'A4');
        $this->pdf->SetFont('Arial','B', 9);
        $testheader = ['Question', 'Learning Section', 'Question No.', 'Status'];
        $testdata = [];
        foreach($this->theory->questions as $question => $prim){
            if(isset($this->theory->useranswers[$question]['status']) && $this->theory->useranswers[$question]['status'] == '4'){$correct = 'Correct';}else{$correct = 'Incorrect';}
            $questioninfo = $this->theory->questionInfo($prim);
            $testdata[] = [$question, $questioninfo['cat'], $questioninfo['topic'], $correct];
        }
        $this->pdf->basicTable($testheader, $testdata, [22,98,30,40], 5, true);
    }
    
    /**
     * Output the PDF to the screen
     */
    public function createPDF(){
        $this->generateCertificate();
        $this->pdf->Output();
    }
}

/**
 * @codeCoverageIgnore
 */
class FPDF_Protection extends FPDF{
    /**
     * Create a PDF table
     * @param array $header This should be the table headers as an array
     * @param array $data This should be that table data as a multi-dimensional array
     * @param array|string $widths The widths of the table columns as aran array if required
     * @param int $height The hight given to the table fields
     * @param boolean $left If left aligned columns set to true for center aligned set to false
     */
    public function basicTable($header, $data, $widths = '', $height = 6, $left = false){
        $first = true;
        $this->SetFont('Arial', 'B');
        foreach($header as $col){
            if($first === true){$first = false; $currentwidth = intval(current($widths));}else{$currentwidth = intval(next($widths));}
            $this->Cell($currentwidth,7,$col,1,0,'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '');
        foreach($data as $row){
            reset($widths);
            $first = true;
            $i = 1;
            foreach($row as $col){
                if($first === true){$first = false; $currentwidth = intval(current($widths));}else{$currentwidth = intval(next($widths));}
                if($left !== false){if($i == $left){$align = 'L';}else{$align = 'C';}}else{$align = 'C';}
                $this->Cell($currentwidth,$height,$col,1,0,$align);
                $i++;
            }
            $this->Ln();
        }
    }
}